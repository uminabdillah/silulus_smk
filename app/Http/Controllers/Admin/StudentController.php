<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
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
        return view('admin.students.create');
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
            'status_lulus' => 'required|boolean',
        ]);

        $validated['academic_year_id'] = $activeYear->id;
        $validated['is_released'] = 0;

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
        return view('admin.students.edit', compact('student'));
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
            'status_lulus' => 'required|boolean',
        ]);

        $student->update($validated);

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui!');
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
