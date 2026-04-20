<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\SchoolProfile;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\MajorProgram;
use App\Models\Classroom;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        $selectedYearId = $request->get('academic_year_id', $activeYear?->id);
        
        $academicYears = \App\Models\AcademicYear::orderBy('tahun_ajaran', 'desc')->get();
        
        $query = Student::query();
        if ($selectedYearId) {
            $query->where('academic_year_id', $selectedYearId);
        }
        
        $perPage = $request->get('per_page', 25);
        $students = $query->with('academicYear')->latest()->paginate($perPage)->withQueryString();
        $selectedYear = \App\Models\AcademicYear::find($selectedYearId);
        
        return view('admin.students.index', compact('students', 'academicYears', 'selectedYearId', 'activeYear', 'selectedYear'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $school = SchoolProfile::first();
        $jenjang = $school->jenjang ?? null;
        $programs = MajorProgram::with('concentrations')->get();
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        $classrooms = Classroom::where('academic_year_id', $activeYear?->id)
            ->with('majorProgram')->orderBy('nama_kelas')->get();
        return view('admin.students.create', compact('jenjang', 'programs', 'classrooms'));
    }

    public function store(Request $request)
    {
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        
        if (!$activeYear) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif. Silakan aktifkan tahun ajaran terlebih dahulu.');
        }

        $validated = $request->validate([
            'nisn' => 'required|numeric|unique:students,nisn',
            'nis_lokal' => 'nullable|string|max:255',
            'nama_lengkap' => 'required|string|max:255',
            'kelas' => 'nullable|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'program_keahlian' => 'nullable|string|max:255',
            'konsentrasi_keahlian' => 'nullable|string|max:255',
            'major_program_id' => 'nullable|exists:major_programs,id',
            'major_concentration_id' => 'nullable|exists:major_concentrations,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'status_lulus' => 'required|boolean',
        ]);

        $validated['academic_year_id'] = $activeYear->id;
        $validated['is_released'] = 0;

        // Auto-sync string columns from relational data for backward compatibility
        if (!empty($validated['major_program_id'])) {
            $prog = \App\Models\MajorProgram::find($validated['major_program_id']);
            $validated['program_keahlian'] = $prog?->nama_program ?? $validated['program_keahlian'];
        }
        if (!empty($validated['major_concentration_id'])) {
            $conc = \App\Models\MajorConcentration::find($validated['major_concentration_id']);
            $validated['konsentrasi_keahlian'] = $conc?->nama_konsentrasi ?? $validated['konsentrasi_keahlian'];
        }
        // Auto-fill kelas string from classroom_id if kelas is empty
        if (!empty($validated['classroom_id']) && empty($validated['kelas'])) {
            $room = Classroom::find($validated['classroom_id']);
            $validated['kelas'] = $room?->nama_kelas;
        }

        Student::create($validated);

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil ditambahkan ke tahun ajaran ' . $activeYear->tahun_ajaran);
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        // Not used, redirect to index
        return redirect()->route('students.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $school = SchoolProfile::first();
        $jenjang = $school->jenjang ?? null;
        $programs = MajorProgram::with('concentrations')->get();

        $subjects = Subject::where(function($query) use ($student) {
            $query->whereNull('program_keahlian');
            // Match by program name (legacy) or if null (for all)
            if ($student->program_keahlian) {
                $query->orWhere('program_keahlian', $student->program_keahlian);
            }
        })->get();

        $grades = Grade::where('student_id', $student->id)->pluck('nilai', 'subject_id');

        $classrooms = Classroom::where('academic_year_id', $student->academic_year_id)
            ->orderBy('nama_kelas')->get();

        return view('admin.students.edit', compact('student', 'subjects', 'grades', 'jenjang', 'programs', 'classrooms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        if (!$student->academicYear?->is_active) {
            abort(403, 'Hanya data pada tahun ajaran aktif yang dapat diubah.');
        }

        $validated = $request->validate([
            'nisn' => 'required|numeric|unique:students,nisn,' . $student->id,
            'nis_lokal' => 'nullable|string|max:255',
            'nama_lengkap' => 'required|string|max:255',
            'kelas' => 'nullable|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'program_keahlian' => 'nullable|string|max:255',
            'konsentrasi_keahlian' => 'nullable|string|max:255',
            'major_program_id' => 'nullable|exists:major_programs,id',
            'major_concentration_id' => 'nullable|exists:major_concentrations,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'status_lulus' => 'required|boolean',
        ]);

        // Auto-sync string columns from relational data for backward compatibility
        if (!empty($validated['major_program_id'])) {
            $prog = \App\Models\MajorProgram::find($validated['major_program_id']);
            $validated['program_keahlian'] = $prog?->nama_program ?? $validated['program_keahlian'];
        }
        if (!empty($validated['major_concentration_id'])) {
            $conc = \App\Models\MajorConcentration::find($validated['major_concentration_id']);
            $validated['konsentrasi_keahlian'] = $conc?->nama_konsentrasi ?? $validated['konsentrasi_keahlian'];
        }
        // Auto-fill kelas string from classroom_id if kelas is empty
        if (!empty($validated['classroom_id']) && empty($validated['kelas'])) {
            $room = Classroom::find($validated['classroom_id']);
            $validated['kelas'] = $room?->nama_kelas;
        }

        $student->update($validated);

        // Update Grades
        if ($request->has('grades')) {
            foreach ($request->grades as $subject_id => $nilai) {
                if ($nilai !== null) {
                    Grade::updateOrCreate(
                        ['student_id' => $student->id, 'subject_id' => $subject_id],
                        ['nilai' => $nilai]
                    );
                }
            }
        }

        return redirect()->route('students.index')->with('success', 'Data siswa dan nilai berhasil diperbarui!');
    }

    /**
     * Download excellent template.
     */
    public function template()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\StudentTemplateExport, 'template_siswa.xlsx');
    }

    /**
     * Import data from excel.
     */
    public function import(Request $request)
    {
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return redirect()->route('students.index')->with('error', 'Tidak ada tahun ajaran aktif. Silakan aktifkan tahun ajaran terlebih dahulu sebelum import.');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\StudentsImport, $request->file('file'));
            return redirect()->route('students.index')->with('success', 'Data siswa berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->route('students.index')->with('error', 'Gagal memproses import. Pastikan format kolom sesuai. Pesan error: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        if (!$student->academicYear?->is_active) {
            abort(403, 'Hanya data pada tahun ajaran aktif yang dapat dihapus.');
        }

        $student->delete();

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus!');
    }

    public function toggleRelease(Student $student)
    {
        if (!$student->academicYear?->is_active) {
            abort(403, 'Hanya data pada tahun ajaran aktif yang dapat diubah.');
        }

        $student->update(['is_released' => !$student->is_released]);
        return back()->with('success', 'Status akses siswa berhasil diubah.');
    }

    public function bulkRelease(Request $request)
    {
        $ids = $request->ids;
        if (!$ids) return back()->with('error', 'Belum ada siswa yang dipilih.');
        
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if (!$activeYear) abort(403, 'Tidak ada tahun ajaran aktif.');

        Student::whereIn('id', $ids)
            ->where('academic_year_id', $activeYear->id)
            ->update(['is_released' => 1]);

        return back()->with('success', 'Siswa berhasil dirilis (Hanya berlaku untuk tahun ajaran aktif).');
    }

    public function bulkHold(Request $request)
    {
        $ids = $request->ids;
        if (!$ids) return back()->with('error', 'Belum ada siswa yang dipilih.');
        
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if (!$activeYear) abort(403, 'Tidak ada tahun ajaran aktif.');

        Student::whereIn('id', $ids)
            ->where('academic_year_id', $activeYear->id)
            ->update(['is_released' => 0]);

        return back()->with('success', 'Akses siswa berhasil ditahan (Hanya berlaku untuk tahun ajaran aktif).');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (!$ids) return back()->with('error', 'Belum ada siswa yang dipilih.');
        
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if (!$activeYear) abort(403, 'Tidak ada tahun ajaran aktif.');

        // Only allow deleting students from active year
        Student::whereIn('id', $ids)
            ->where('academic_year_id', $activeYear->id)
            ->delete();

        return back()->with('success', 'Proses penghapusan selesai (Hanya siswa pada tahun ajaran aktif yang terhapus).');
    }
}
