<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MajorProgram extends Model
{
    protected $fillable = ['nama_program'];

    public function concentrations()
    {
        return $this->hasMany(MajorConcentration::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}
