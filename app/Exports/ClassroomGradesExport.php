<?php

namespace App\Exports;

use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Grade;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ClassroomGradesExport implements FromArray, WithHeadings, WithTitle, WithEvents
{
    private $classroom;
    private $subjects;

    public function __construct(Classroom $classroom)
    {
        $this->classroom = $classroom->load(['students', 'majorProgram', 'majorConcentration']);
        
        $programName = $this->classroom->majorProgram?->nama_program;
        $concentrationName = $this->classroom->majorConcentration?->nama_konsentrasi;

        // Fallback: If classroom mapping is incomplete, try guessing from the students inside
        if (!$programName || !$concentrationName) {
            $studentSample = $this->classroom->students->first();
            if ($studentSample) {
                $programName = $programName ?? $studentSample->program_keahlian ?? $studentSample->majorProgram?->nama_program;
                $concentrationName = $concentrationName ?? $studentSample->konsentrasi_keahlian ?? $studentSample->majorConcentration?->nama_konsentrasi;
            }
        }
        
        $this->subjects = Subject::where(function ($q) use ($programName, $concentrationName) {
            // 1. General Subjects
            $q->whereNull('program_keahlian')->whereNull('konsentrasi_keahlian');

            // 2. Program-specific subjects (C2 - no specific concentration)
            if ($programName) {
                $q->orWhere(function ($subQ) use ($programName) {
                    $subQ->where('program_keahlian', $programName)
                         ->whereNull('konsentrasi_keahlian');
                });
            }

            // 3. Concentration-specific subjects (C3)
            if ($concentrationName) {
                $q->orWhere('konsentrasi_keahlian', $concentrationName);
            }
        })->orderBy('kelompok')->orderBy('id')->get();
    }

    public function headings(): array
    {
        $headers = ['NISN', 'NAMA LENGKAP'];
        foreach ($this->subjects as $subject) {
            // Include kelompok hint in the header for clarity
            $headers[] = $subject->nama_mapel;
        }
        return $headers;
    }

    public function array(): array
    {
        $data = [];
        $studentIds = $this->classroom->students->pluck('id');
        $allGrades = Grade::whereIn('student_id', $studentIds)
            ->get()
            ->groupBy('student_id')
            ->map(fn($g) => $g->pluck('nilai', 'subject_id'));

        foreach ($this->classroom->students as $student) {
            $row = [
                'nisn' => $student->nisn,
                'nama' => $student->nama_lengkap,
            ];
            
            foreach ($this->subjects as $subject) {
                $row[] = (string) ($allGrades[$student->id][$subject->id] ?? '');
            }
            
            $data[] = $row;
        }

        return $data;
    }

    public function title(): string
    {
        return substr('TEMPLATE ' . $this->classroom->nama_kelas, 0, 31);
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->subjects) + 2);
                $lastRow = $this->classroom->students->count() + 1;
                $fullRange = "A1:{$lastCol}{$lastRow}";

                // 1. Freeze first row and first two columns
                $sheet->freezePane('C2');

                // 2. Styling Header Row
                $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2563EB']], // Blue Header
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ]
                ]);
                
                // 3. Set row height for header
                $sheet->getRowDimension(1)->setRowHeight(40);

                // 4. Auto Size first two columns, manual size for subject columns
                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('B')->setAutoSize(true);
                
                for ($i = 3; $i <= count($this->subjects) + 2; $i++) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                    $sheet->getColumnDimension($colLetter)->setWidth(15);
                    $sheet->getStyle("{$colLetter}2:{$colLetter}{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // 5. Add Borders to all cells
                $sheet->getStyle($fullRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFCCCCCC'],
                        ],
                    ],
                ]);
                
                // 6. Data Validation Data Rule (0 - 100) for grade columns
                if (count($this->subjects) > 0 && $lastRow > 1) {
                    $valRange = "C2:{$lastCol}{$lastRow}";
                    $validation = $sheet->getCell('C2')->getDataValidation();
                    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_WHOLE);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setErrorTitle('Input Tidak Valid');
                    $validation->setError('Harap masukkan angka di antara 0 hingga 100.');
                    $validation->setPromptTitle('Input Nilai');
                    $validation->setPrompt('Masukkan angka 0 - 100');
                    $validation->setFormula1(0);
                    $validation->setFormula2(100);
                    $validation->setSqref($valRange);
                }
            },
        ];
    }
}
