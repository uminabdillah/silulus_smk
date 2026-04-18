<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SklTemplate;

class SklTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $content = '<p>Yang bertanda tangan di bawah ini, Kepala {nama_sekolah} Kabupaten Brebes, Provinsi Jawa Tengah menerangkan bahwa :</p>
<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
    <tbody>
        <tr>
            <td style="width: 35%;">Nama Lengkap</td>
            <td style="width: 3%; text-align: center;">:</td>
            <td style="width: 62%;"><strong>{nama_lengkap}</strong></td>
        </tr>
        <tr>
            <td>Tempat, Tanggal Lahir</td>
            <td style="text-align: center;">:</td>
            <td>{tempat_lahir}, {tgl_lahir}</td>
        </tr>
        <tr>
            <td>Nomor Induk Siswa Nasional</td>
            <td style="text-align: center;">:</td>
            <td>{nisn}</td>
        </tr>
        <tr>
            <td>Program Keahlian</td>
            <td style="text-align: center;">:</td>
            <td>{program_keahlian}</td>
        </tr>
        <tr>
            <td>Konsentrasi Keahlian</td>
            <td style="text-align: center;">:</td>
            <td>{konsentrasi_keahlian}</td>
        </tr>
    </tbody>
</table>
<p>Benar siswa/siswi {nama_sekolah} yang telah mengikuti seluruh rangkaian kriteria penilaian dan evaluasi belajar, meliputi Asesmen Sumatif Akhir Jenjang (ASAJ), Uji Kompetensi Keahlian (UKK), serta penuntasan Projek Penguatan Profil Pelajar Pancasila (P5) pada Kurikulum Merdeka, dan berdasarkan rapat pleno dewan guru tanggal {tanggal_pleno}, yang bersangkutan dinyatakan:</p>
<div style="text-align: center; font-weight: bold; font-size: 16pt; margin: 20px 0;">{lulus_tidak}</div>
<p>Dari {nama_sekolah} pada tanggal {tanggal_kelulusan} tahun ajaran {tahun_ajaran}.</p>
<p>Surat Keterangan Lulus ini dibuat untuk dapat dipergunakan sebagaimana mestinya, dan berlaku sampai dengan Ijasah Asli dari yang bersangkutan diterbitkan.</p>';

        SklTemplate::updateOrCreate(
            ['id' => 1],
            ['content' => $content]
        );
    }
}
