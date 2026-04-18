<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolProfile extends Model
{
    protected $fillable = [
        'nama_sekolah',
        'npsn',
        'alamat',
        'kepala_sekolah',
        'nip_kepala',
        'logo_path',
        'tanda_tangan_path',
        'kop_surat',
    ];
}
