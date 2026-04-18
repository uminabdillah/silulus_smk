<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = [
        'tahun_ajaran',
        'nomor_skl_template',
        'tanggal_pleno',
        'tanggal_kelulusan',
        'tempat',
        'template_path',
        'is_active',
        'announcement_start',
        'announcement_end',
        'hold_message',
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
