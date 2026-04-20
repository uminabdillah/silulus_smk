<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MajorConcentration extends Model
{
    protected $fillable = ['major_program_id', 'nama_konsentrasi'];

    public function program()
    {
        return $this->belongsTo(MajorProgram::class, 'major_program_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
