<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SklTemplate;

class UpdateSklTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template = SklTemplate::first();
        if ($template) {
            $content = '
<p style="text-align: center; margin-bottom: 20px;"><b>SURAT KETERANGAN LULUS</b><br>Nomor: {nomor_skl}</p>
<p>Kepala {nama_sekolah} selaku Satuan Pendidikan di bawah naungan Kementerian Pendidikan, Kebudayaan, Riset dan Teknologi, berdasarkan hasil rapat pleno Kelulusan pada tanggal {tanggal_pleno}, dengan ini menerangkan bahwa:</p>

<table style="width: 100%; margin: 15px 0;">
    <tr><td width="30%">Nama Lengkap</td><td width="5%">:</td><td><b>{nama_lengkap}</b></td></tr>
    <tr><td>Tempat/Tgl Lahir</td><td>:</td><td>{tempat_lahir}, {tgl_lahir}</td></tr>
    <tr><td>NISN / Kelas</td><td>:</td><td>{nisn} / {kelas}</td></tr>
    <tr><td>Program Keahlian</td><td>:</td><td>{program_keahlian}</td></tr>
</table>

<p>Dinyatakan <b>{lulus_tidak}</b> dari satuan pendidikan {nama_sekolah} tahun ajaran {tahun_ajaran}.</p>

<div style="margin: 20px 0;">
    <p>Berikut adalah rincian nilai siswa yang bersangkutan sebagai dasar kelulusan:</p>
    {tabel_nilai}
</div>

<p>Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.</p>
';
            $template->update(['content' => trim($content)]);
        }
    }
}
