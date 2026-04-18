<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SchoolProfile;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Grade;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
        ]);

        $this->call([
            SklTemplateSeeder::class,
        ]);

        SchoolProfile::create([
            'nama_sekolah' => 'SMA Negeri 1 Contoh',
            'npsn' => '12345678',
            'alamat' => 'Jl. Pendidikan No. 1, Kota Contoh',
            'kepala_sekolah' => 'Budi Santoso, M.Pd.',
            'nip_kepala' => '198001012005011001',
        ]);

        $student = Student::create([
            'nisn' => '0012345678',
            'nis_lokal' => '1001',
            'nama_lengkap' => 'Ahmad Fulan',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2005-08-17',
            'nama_ortu' => 'Bapak Fulan',
            'program_keahlian' => 'IPA',
            'status_lulus' => true,
        ]);

        $matematika = Subject::create(['nama_mapel' => 'Matematika', 'kelompok' => 'A']);
        $biologi = Subject::create(['nama_mapel' => 'Biologi', 'kelompok' => 'C']);
        $bahasaInggris = Subject::create(['nama_mapel' => 'Bahasa Inggris', 'kelompok' => 'A']);

        Grade::create(['student_id' => $student->id, 'subject_id' => $matematika->id, 'nilai' => 85.5]);
        Grade::create(['student_id' => $student->id, 'subject_id' => $biologi->id, 'nilai' => 90.0]);
        Grade::create(['student_id' => $student->id, 'subject_id' => $bahasaInggris->id, 'nilai' => 88.0]);
    }
}
