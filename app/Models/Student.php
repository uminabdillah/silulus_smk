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
}
