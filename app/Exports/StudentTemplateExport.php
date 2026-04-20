<?php

namespace App\Exports;

use App\Models\Classroom;
use App\Models\AcademicYear;
use App\Models\MajorProgram;
use App\Models\SchoolProfile;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class StudentTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithEvents
{
    private $jenjang;
    private $programs;
    private $classrooms;

    public function __construct()
    {
        $school = SchoolProfile::first();
        $this->jenjang = $school?->jenjang;
        $this->programs = MajorProgram::with('concentrations')->get();

        $activeYear = AcademicYear::where('is_active', true)->first();
        $this->classrooms = Classroom::where('academic_year_id', $activeYear?->id)
            ->orderBy('nama_kelas')->get();
    }

    public function headings(): array
    {
        return [
            'nisn',
            'nis_lokal',
            'nama_lengkap',
            'nama_kelas',          // NEW: matches classroom name
            'tempat_lahir',
            'tanggal_lahir',
            'program_keahlian',
            'konsentrasi_keahlian',
            'status_lulus',
            'is_released'
        ];
    }

    public function array(): array
    {
        $jenjangSMK = in_array($this->jenjang, ['SMK', 'MAK']);
        $firstProgram = $this->programs->first();
        $firstConcentration = $firstProgram?->concentrations->first();
        $firstClass = $this->classrooms->first();

        $sampleClass     = $firstClass?->nama_kelas ?? 'XII TKJ 1';
        $sampleProgram   = $jenjangSMK ? ($firstProgram?->nama_program ?? 'Teknik Komputer') : 'MIPA';
        $sampleConc      = $jenjangSMK ? ($firstConcentration?->nama_konsentrasi ?? 'TKJ') : '';

        return [
            [
                '0012345678',
                '1001',
                'Ahmad Fulan (Contoh - Hapus baris ini)',
                $sampleClass,
                'Jakarta',
                '2005-08-17',
                $sampleProgram,
                $sampleConc,
                'Lulus',
                '0'
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1D4ED8'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $jenjangSMK = in_array($this->jenjang, ['SMK', 'MAK']);
                $jenjangSMA = in_array($this->jenjang, ['SMA', 'MA']);

                // Borders & Styles
                $sheet->getStyle('A1:J1')->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']]]
                ]);
                $sheet->getStyle('A2:J200')->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]]
                ]);
                $sheet->getRowDimension(1)->setRowHeight(28);

                // Add Comments
                $sheet->getComment('A1')->getText()->createTextRun('WAJIB: NISN hanya angka, unik (misal: 0012345678)');
                $sheet->getComment('B1')->getText()->createTextRun('Opsional: Nomor Induk Siswa Lokal');
                $sheet->getComment('C1')->getText()->createTextRun('WAJIB: Nama lengkap siswa');
                $sheet->getComment('E1')->getText()->createTextRun('WAJIB: Kota/Kabupaten tempat lahir');
                $sheet->getComment('F1')->getText()->createTextRun('WAJIB: Format tanggal YYYY-MM-DD (misal: 2005-08-17)');
                $sheet->getComment('I1')->getText()->createTextRun('WAJIB: Tulis "Lulus" atau "Belum Lulus"');
                $sheet->getComment('J1')->getText()->createTextRun('Status rilis SKL: "1" = Rilis, "0" = Tahan (biarkan kosong = 0)');

                // === BUILD HIDDEN REFERENCE DATA FOR DROPDOWNS ===
                // We use columns Z, AA, AB, AC to bypass the 255-char limit of inline Excel lists
                
                // 1. Classes -> Column Z
                $refClass = '';
                if ($this->classrooms->isNotEmpty()) {
                    $r = 1;
                    foreach ($this->classrooms as $kelas) {
                        $sheet->setCellValue("Z{$r}", $kelas->nama_kelas);
                        $r++;
                    }
                    $refClass = '$Z$1:$Z$' . ($r - 1);
                }
                $sheet->getColumnDimension('Z')->setVisible(false);

                // 2. Programs -> Column AA
                $refProg = '';
                if ($this->programs->isNotEmpty()) {
                    $r = 1;
                    foreach ($this->programs as $prog) {
                        $sheet->setCellValue("AA{$r}", $prog->nama_program);
                        $r++;
                    }
                    $refProg = '$AA$1:$AA$' . ($r - 1);
                }
                $sheet->getColumnDimension('AA')->setVisible(false);

                // Dummy Blank Named Range for empty dropdowns
                $sheet->setCellValue('AK100', ' ');
                $spreadsheet = $sheet->getParent();
                $spreadsheet->addNamedRange(new \PhpOffice\PhpSpreadsheet\NamedRange('DUMMY_BLANK', $sheet, '$AK$100'));

                // 3. Dynamic Concentrations Map
                $hasPrograms = $this->programs->isNotEmpty();
                if ($hasPrograms) {
                    $colIdx = 40; // AN is 40
                    $mapRow = 1;
                    foreach($this->programs as $prog) {
                        $rangeName = "CONC_" . $prog->id;
                        
                        // Write to Map table in AL (Name) and AM (Range Name)
                        $sheet->setCellValue('AL' . $mapRow, $prog->nama_program);
                        $sheet->setCellValue('AM' . $mapRow, $rangeName);
                        $mapRow++;

                        // Write Concentrations to dynamic columns starting from AN (40)
                        if ($prog->concentrations->isNotEmpty()) {
                            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                            $r = 1;
                            foreach($prog->concentrations as $conc) {
                                $sheet->setCellValue($colLetter . $r, $conc->nama_konsentrasi);
                                $r++;
                            }
                            // Create actual named range strictly bounded to existing rows
                            $spreadsheet->addNamedRange(new \PhpOffice\PhpSpreadsheet\NamedRange($rangeName, $sheet, "\${$colLetter}\$1:\${$colLetter}\$" . ($r - 1)));
                            $sheet->getColumnDimension($colLetter)->setVisible(false);
                            $colIdx++;
                        } else {
                            // Link to dummy blank if no concentrations
                            $spreadsheet->addNamedRange(new \PhpOffice\PhpSpreadsheet\NamedRange($rangeName, $sheet, '$AK$100'));
                        }
                    }
                    $sheet->getColumnDimension('AL')->setVisible(false);
                    $sheet->getColumnDimension('AM')->setVisible(false);
                }


                // 4. Status Lulus -> Column AD
                $refStatus = '$AD$1:$AD$2';
                $sheet->setCellValue("AD1", "Lulus");
                $sheet->setCellValue("AD2", "Belum Lulus");
                $sheet->getColumnDimension('AD')->setVisible(false);


                // === APPLY VALIDATIONS D2:D200 (Kelas) ===
                if ($refClass) {
                    $sheet->getComment('D1')->getText()->createTextRun('WAJIB: Pilih kelas dari dropdown yang tersedia.');
                    $vClass = $sheet->getCell("D2")->getDataValidation();
                    $vClass->setType(DataValidation::TYPE_LIST)
                          ->setErrorStyle(DataValidation::STYLE_STOP)
                          ->setAllowBlank(true)
                          ->setShowDropDown(true)
                          ->setFormula1("={$refClass}")
                          ->setError('Pilih kelas dari daftar yang tersedia!')
                          ->setErrorTitle('Kelas Tidak Valid')
                          ->setShowErrorMessage(true)
                          ->setSqref('D2:D200');
                } else {
                    $sheet->getComment('D1')->getText()->createTextRun('⚠ PERHATIAN: Belum ada kelas yang dibuat. Buka menu Kelas dan tambahkan kelas terlebih dahulu.');
                    $sheet->getStyle('D1')->getFont()->getColor()->setARGB('FFDC2626');
                }

                // === APPLY VALIDATIONS G2:G200 (Program Keahlian) ===
                if ($this->programs->isNotEmpty() && $refProg) {
                    $sheet->getComment('G1')->getText()->createTextRun('WAJIB: Pilih Program/Jurusan dari dropdown.');
                    $vProg = $sheet->getCell("G2")->getDataValidation();
                    $vProg->setType(DataValidation::TYPE_LIST)
                          ->setErrorStyle(DataValidation::STYLE_STOP)
                          ->setAllowBlank(true)
                          ->setShowDropDown(true)
                          ->setFormula1("={$refProg}")
                          ->setSqref('G2:G200');
                } else {
                    $sheet->getComment('G1')->getText()->createTextRun('⚠ PERHATIAN: Belum ada Program/Jurusan. Isi di menu Jurusan terlebih dahulu.');
                }

                // === APPLY VALIDATIONS H2:H200 (Konsentrasi) - DYNAMIC ===
                if ($hasPrograms) {
                    $sheet->getComment('H1')->getText()->createTextRun('OPSIONAL: Pilih Konsentrasi (Akan muncul setelah anda memilih opsi Program di sebelah kiri jika ada).');
                    $vConc = $sheet->getCell("H2")->getDataValidation();
                    $vConc->setType(DataValidation::TYPE_LIST)
                          ->setAllowBlank(true)
                          ->setShowDropDown(true)
                          ->setFormula1('=INDIRECT(IF(ISBLANK($G2), "DUMMY_BLANK", VLOOKUP($G2, $AL$1:$AM$200, 2, FALSE)))')
                          ->setSqref('H2:H200');
                } else {
                    $sheet->getComment('H1')->getText()->createTextRun('Opsional: Konsentrasi Keahlian');
                }

                // === APPLY VALIDATIONS I2:I200 (Status Lulus) ===
                $vStatus = $sheet->getCell("I2")->getDataValidation();
                $vStatus->setType(DataValidation::TYPE_LIST)
                      ->setAllowBlank(false)
                      ->setShowDropDown(true)
                      ->setFormula1("={$refStatus}")
                      ->setSqref('I2:I200');

                // Warnings
                if ($this->classrooms->isEmpty()) {
                    $sheet->setCellValue('A102', '⚠️ PERHATIAN: Belum ada kelas yang dibuat untuk tahun ajaran aktif! Buka menu Kelas terlebih dahulu sebelum mendownload template ini.');
                    $sheet->getStyle('A102')->getFont()->setBold(true)->getColor()->setARGB('FFDC2626');
                    $sheet->mergeCells('A102:J102');
                }
            },
        ];
    }
}
