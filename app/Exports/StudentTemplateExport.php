<?php

namespace App\Exports;

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

class StudentTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithEvents
{
    public function headings(): array
    {
        return [
            'nisn',
            'nis_lokal',
            'nama_lengkap',
            'tempat_lahir',
            'tanggal_lahir',
            'nama_ortu',
            'program_keahlian',
            'status_lulus',
            'is_released'
        ];
    }

    public function array(): array
    {
        return [
            [
                '0012345678',
                '1001',
                'Ahmad Fulan',
                'Jakarta',
                '2005-08-17',
                'Bapak Fulan',
                'IPA',
                'Lulus',
                '1'
            ],
            [
                '0012345679',
                '1002',
                'Siti Sarah',
                'Bandung',
                '2006-01-20',
                'Ibu Sarah',
                'IPS',
                'Belum Lulus',
                '1'
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF16A34A'], // Tailwind Green-600
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
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Add borders to the first 3 rows
                $cellRange = 'A1:I3'; 
                $sheet->getStyle($cellRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                // Add comments to clarify columns
                $sheet->getComment('A1')->getText()->createTextRun('Wajib diisi, hanya angka (misal: 0012345678) dan Unik.');
                $sheet->getComment('B1')->getText()->createTextRun('Opsional (misal: 1001)');
                $sheet->getComment('C1')->getText()->createTextRun('Wajib diisi, berisi nama peserta didik.');
                $sheet->getComment('E1')->getText()->createTextRun('Wajib diisi, format tanggal YYYY-MM-DD (Misal: 2005-08-17).');
                $sheet->getComment('H1')->getText()->createTextRun('Wajib diisi, tulis "Lulus", "1", atau "0", "Belum Lulus".');
                $sheet->getComment('I1')->getText()->createTextRun('Status rilis akses SKL. Isi "1" untuk rilis (default) atau "0" untuk ditahan.');
                
                // Adjust row dimension for header
                $sheet->getRowDimension(1)->setRowHeight(25);
            },
        ];
    }
}
