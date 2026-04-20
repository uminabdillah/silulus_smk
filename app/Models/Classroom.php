<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = [
        'academic_year_id',
        'major_program_id',
        'major_concentration_id',
        'nama_kelas',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function majorProgram()
    {
        return $this->belongsTo(MajorProgram::class);
    }

    public function majorConcentration()
    {
        return $this->belongsTo(MajorConcentration::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class)->orderBy('nama_lengkap');
    }
}
