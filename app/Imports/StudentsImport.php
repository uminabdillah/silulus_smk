<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\MajorProgram;
use App\Models\SchoolProfile;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    private $activeYear;
    private $jenjang;
    private $programs;
    private $classrooms;

    public function __construct()
    {
        $this->activeYear = AcademicYear::where('is_active', true)->first();

        $school = SchoolProfile::first();
        $this->jenjang = $school?->jenjang;

        // Pre-load for fast lookup
        $this->programs = MajorProgram::with('concentrations')->get();
        $this->classrooms = Classroom::where('academic_year_id', $this->activeYear?->id)->get();
    }

    public function model(array $row)
    {
        // Skip empty NISN rows
        if (empty($row['nisn'])) {
            return null;
        }

        // Handle date from excel
        $tanggalLahir = $row['tanggal_lahir'] ?? null;
        if (is_numeric($tanggalLahir)) {
            $tanggalLahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalLahir)->format('Y-m-d');
        } else {
            $tanggalLahir = date('Y-m-d', strtotime($tanggalLahir ?: '2000-01-01'));
        }

        $jenjangSMK = in_array($this->jenjang, ['SMK', 'MAK']);

        // === Resolve classroom_id from nama_kelas ===
        $classroomId = null;
        $kelasLabel  = null;
        if (!empty($row['nama_kelas'])) {
            $classroom = $this->classrooms->firstWhere('nama_kelas', $row['nama_kelas']);
            $classroomId = $classroom?->id;
            $kelasLabel  = $row['nama_kelas']; // keep as string label too
        }

        // === Resolve major_program_id from program_keahlian name ===
        $majorProgramId      = null;
        $majorConcentrationId = null;
        if (!empty($row['program_keahlian'])) {
            $program = $this->programs->firstWhere('nama_program', $row['program_keahlian']);
            if ($program) {
                $majorProgramId = $program->id;
                if (!empty($row['konsentrasi_keahlian'])) {
                    $conc = $program->concentrations->firstWhere('nama_konsentrasi', $row['konsentrasi_keahlian']);
                    $majorConcentrationId = $conc?->id;
                }
            }
        }

        return new Student([
            'nisn'                   => $row['nisn'],
            'nis_lokal'              => $row['nis_lokal'] ?? null,
            'nama_lengkap'           => $row['nama_lengkap'] ?? 'Tanpa Nama',
            'kelas'                  => $kelasLabel ?? ($row['kelas'] ?? null),
            'classroom_id'           => $classroomId,
            'tempat_lahir'           => $row['tempat_lahir'] ?? 'Tidak Diketahui',
            'tanggal_lahir'          => $tanggalLahir,
            'program_keahlian'       => $row['program_keahlian'] ?? null,
            'konsentrasi_keahlian'   => $row['konsentrasi_keahlian'] ?? null,
            'major_program_id'       => $majorProgramId,
            'major_concentration_id' => $majorConcentrationId,
            'status_lulus'           => ((string)($row['status_lulus'] ?? '0') === '1' || strtolower($row['status_lulus'] ?? '') === 'lulus') ? 1 : 0,
            'academic_year_id'       => $this->activeYear?->id,
            'is_released'            => ((string)($row['is_released'] ?? '0') !== '0') ? 1 : 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'nisn'         => 'required_with:nama_lengkap',
            'nama_lengkap' => 'required_with:nisn',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nisn.required_with'         => 'Kolom NISN wajib diisi jika nama lengkap diisi.',
            'nama_lengkap.required_with' => 'Kolom Nama Lengkap wajib diisi jika NISN diisi.',
        ];
    }
}
