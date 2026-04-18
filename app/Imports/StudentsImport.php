<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Skip if nisn is empty
        if (empty($row['nisn'])) {
            return null;
        }

        // Handle date from excel
        $tanggal_lahir = $row['tanggal_lahir'] ?? null;
        if (is_numeric($tanggal_lahir)) {
            $tanggal_lahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal_lahir)->format('Y-m-d');
        } else {
            // Assume string like '2005-08-17' or fallback to a default date
            $tanggal_lahir = date('Y-m-d', strtotime($tanggal_lahir ?: '2000-01-01'));
        }

        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();

        return new Student([
            'nisn'             => $row['nisn'],
            'nis_lokal'        => $row['nis_lokal'] ?? null,
            'nama_lengkap'     => $row['nama_lengkap'] ?? 'Tanpa Nama',
            'tempat_lahir'     => $row['tempat_lahir'] ?? 'Tidak Diketahui',
            'tanggal_lahir'    => $tanggal_lahir,
            'program_keahlian' => $row['program_keahlian'] ?? null,
            'konsentrasi_keahlian' => $row['konsentrasi_keahlian'] ?? null,
            'status_lulus'     => ((string)($row['status_lulus'] ?? '0') === '1' || strtolower($row['status_lulus'] ?? '') === 'lulus') ? 1 : 0,
            'academic_year_id' => $activeYear ? $activeYear->id : null,
            'is_released'      => ((string)($row['is_released'] ?? '0') !== '0') ? 1 : 0,
        ]);
    }
}
