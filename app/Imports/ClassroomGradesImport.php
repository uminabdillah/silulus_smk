<?php

namespace App\Imports;

use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Grade;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ClassroomGradesImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    private $classroom;
    private $subjects;

    public function __construct(Classroom $classroom)
    {
        // Enforce exact header names for array keys (so it matches $subject->nama_mapel exactly)
        \Maatwebsite\Excel\Imports\HeadingRowFormatter::default('none');
        
        $this->classroom = $classroom->load(['students', 'majorProgram', 'majorConcentration']);
        
        // Fetch matching subjects
        $programName = $this->classroom->majorProgram?->nama_program;
        $concentrationName = $this->classroom->majorConcentration?->nama_konsentrasi;

        // Fallback: If classroom mapping is incomplete, try guessing from the students inside
        if (!$programName || !$concentrationName) {
            $studentSample = $this->classroom->students->first();
            if ($studentSample) {
                $programName = $programName ?? $studentSample->program_keahlian ?? $studentSample->majorProgram?->nama_program;
                $concentrationName = $concentrationName ?? $studentSample->konsentrasi_keahlian ?? $studentSample->majorConcentration?->nama_konsentrasi;
            }
        }
        
        $this->subjects = Subject::where(function ($q) use ($programName, $concentrationName) {
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
    }

    public function collection(Collection $rows)
    {
        $students = $this->classroom->students->keyBy('nisn');
        
        foreach ($rows as $row) {
            $nisn = $row['NISN'] ?? null;
            if (!$nisn) continue;

            $student = $students->get($nisn);
            if (!$student) continue;

            foreach ($this->subjects as $subject) {
                $mataPelajaran = $subject->nama_mapel;
                
                // If the column exists and the value is provided in Excel
                if ($row->has($mataPelajaran) && $row[$mataPelajaran] !== null && $row[$mataPelajaran] !== '') {
                    $nilai = $row[$mataPelajaran];
                    
                    Grade::updateOrCreate(
                        ['student_id' => $student->id, 'subject_id' => $subject->id],
                        ['nilai' => $nilai]
                    );
                }
            }
        }
        
        // Restore standard heading formatting for other imports across the app
        \Maatwebsite\Excel\Imports\HeadingRowFormatter::default('slug');
    }
}
