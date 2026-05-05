<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'nisn',
        'nis_lokal',
        'nama_lengkap',
        'kelas',
        'tempat_lahir',
        'tanggal_lahir',
        'nama_ortu',
        'program_keahlian',
        'konsentrasi_keahlian',
        'major_program_id',
        'major_concentration_id',
        'classroom_id',
        'status_lulus',
        'academic_year_id',
        'is_released'
    ];

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function majorProgram()
    {
        return $this->belongsTo(MajorProgram::class);
    }

    public function majorConcentration()
    {
        return $this->belongsTo(MajorConcentration::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function getApplicableSubjects()
    {
        return Subject::where(function($query) {
            $query->whereNull('program_keahlian')->whereNull('konsentrasi_keahlian');
            if ($this->program_keahlian) {
                $query->orWhere(function($subq) {
                    $subq->where('program_keahlian', $this->program_keahlian)
                         ->whereNull('konsentrasi_keahlian');
                });
            }
            if ($this->konsentrasi_keahlian) {
                $query->orWhere('konsentrasi_keahlian', $this->konsentrasi_keahlian);
            }
        })->get();
    }

    public function updateGraduationStatus()
    {
        $applicableSubjects = $this->getApplicableSubjects();
        $grades = $this->grades()->whereNotNull('nilai')->where('nilai', '!=', '')->get();

        // 1. All subjects must have a grade
        $isComplete = $grades->count() >= $applicableSubjects->count() && $applicableSubjects->count() > 0;
        
        if ($isComplete) {
            // 2. No grade below 70
            $anyBelow70 = $grades->contains(fn($grade) => $grade->nilai < 70);
            
            if (!$anyBelow70) {
                $this->update(['status_lulus' => 'lulus']);
            } else {
                if ($this->status_lulus === 'lulus') {
                    $this->update(['status_lulus' => 'lulus bersyarat']);
                }
            }
        } else {
            // If grades are incomplete but status is 'lulus', demote to 'lulus bersyarat'
            if ($this->status_lulus === 'lulus') {
                $this->update(['status_lulus' => 'lulus bersyarat']);
            }
        }
    }
}
