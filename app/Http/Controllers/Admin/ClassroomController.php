<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\MajorProgram;
use App\Models\SchoolProfile;
use App\Models\Subject;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $allYears   = AcademicYear::orderBy('tahun_ajaran', 'desc')->get();

        $classrooms = Classroom::with(['majorProgram', 'majorConcentration', 'students'])
            ->where('academic_year_id', $activeYear?->id)
            ->orderBy('nama_kelas')
            ->get();

        $programs = MajorProgram::with('concentrations')->get();
        $school   = SchoolProfile::first();

        return view('admin.classrooms.index', compact('classrooms', 'activeYear', 'allYears', 'programs', 'school'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas'             => 'required|string|max:100',
            'academic_year_id'       => 'required|exists:academic_years,id',
            'major_program_id'       => 'nullable|exists:major_programs,id',
            'major_concentration_id' => 'nullable|exists:major_concentrations,id',
        ]);

        Classroom::create($request->only('nama_kelas', 'academic_year_id', 'major_program_id', 'major_concentration_id'));

        return back()->with('success', 'Kelas berhasil ditambahkan!');
    }

    public function update(Request $request, Classroom $classroom)
    {
        $request->validate([
            'nama_kelas'             => 'required|string|max:100',
            'major_program_id'       => 'nullable|exists:major_programs,id',
            'major_concentration_id' => 'nullable|exists:major_concentrations,id',
        ]);

        $classroom->update($request->only('nama_kelas', 'major_program_id', 'major_concentration_id'));

        return back()->with('success', 'Kelas berhasil diperbarui!');
    }

    public function destroy(Classroom $classroom)
    {
        $classroom->delete();
        return back()->with('success', 'Kelas berhasil dihapus!');
    }

    // ──────────────────────────────────────────────────────
    // BULK GRADE INPUT
    // ──────────────────────────────────────────────────────

    /**
     * Show the bulk grade input spreadsheet for a classroom.
     */
    public function gradesIndex(Classroom $classroom)
    {
        $classroom->load(['students', 'majorProgram', 'majorConcentration']);

        // Get subjects relevant to this classroom's program or concentration
        $programName = $classroom->majorProgram?->nama_program;
        $concentrationName = $classroom->majorConcentration?->nama_konsentrasi;

        // Fallback: If classroom mapping is incomplete, try guessing from the students inside
        if (!$programName || !$concentrationName) {
            $studentSample = $classroom->students->first();
            if ($studentSample) {
                $programName = $programName ?? $studentSample->program_keahlian ?? $studentSample->majorProgram?->nama_program;
                $concentrationName = $concentrationName ?? $studentSample->konsentrasi_keahlian ?? $studentSample->majorConcentration?->nama_konsentrasi;
            }
        }
        
        $subjects = Subject::where(function ($q) use ($programName, $concentrationName) {
            // 1. General Subjects
            $q->whereNull('program_keahlian')->whereNull('konsentrasi_keahlian');

            // 2. Program-specific subjects (C2 - no specific concentration)
            if ($programName) {
                $q->orWhere(function ($subQ) use ($programName) {
                    $subQ->where('program_keahlian', $programName)
                         ->whereNull('konsentrasi_keahlian');
                });
            }

            // 3. Concentration-specific subjects (C3)
            if ($concentrationName) {
                $q->orWhere('konsentrasi_keahlian', $concentrationName);
            }
        })->orderBy('kelompok')->orderBy('id')->get();

        // Load all grades for students in this classroom
        $studentIds = $classroom->students->pluck('id');
        $allGrades  = Grade::whereIn('student_id', $studentIds)
            ->get()
            ->groupBy('student_id')
            ->map(fn($g) => $g->pluck('nilai', 'subject_id'));

        return view('admin.classrooms.grades', compact('classroom', 'subjects', 'allGrades'));
    }

    /**
     * Save bulk grades (POST).
     * Expects: grades[student_id][subject_id] = nilai
     */
    public function gradesSave(Request $request, Classroom $classroom)
    {
        $gradesData = $request->input('grades', []);

        foreach ($gradesData as $studentId => $subjectGrades) {
            foreach ($subjectGrades as $subjectId => $nilai) {
                if ($nilai !== null && $nilai !== '') {
                    Grade::updateOrCreate(
                        ['student_id' => $studentId, 'subject_id' => $subjectId],
                        ['nilai' => $nilai]
                    );
                }
            }
        }

        return back()->with('success', 'Nilai seluruh siswa di kelas ' . $classroom->nama_kelas . ' berhasil disimpan!');
    }

    public function gradesExport(Classroom $classroom)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ClassroomGradesExport($classroom),
            'Template_Nilai_' . str_replace(' ', '_', $classroom->nama_kelas) . '.xlsx'
        );
    }

    public function gradesImport(Request $request, Classroom $classroom)
    {
        $request->validate([
            'grade_file' => 'required|mimes:xlsx,xls,csv'
        ]);

        \Maatwebsite\Excel\Facades\Excel::import(
            new \App\Imports\ClassroomGradesImport($classroom),
            $request->file('grade_file')
        );

        return back()->with('success', 'Nilai berhasil di-import massal ke kelas ' . $classroom->nama_kelas . '!');
    }
}
