<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'nisn',
        'nis_lokal',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'nama_ortu',
        'program_keahlian',
        'konsentrasi_keahlian',
        'status_lulus',
        'academic_year_id',
        'is_released'
    ];

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
