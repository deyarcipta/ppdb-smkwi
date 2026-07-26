<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Style;

class TemplateDataSmpExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithDefaultStyles, WithCustomStartCell, WithEvents
{
    public function array(): array
    {
        return [
            [1, 'SMP Negeri 1 Jakarta'],
            [2, 'SMP Negeri 98 Jakarta'],
            [3, 'SMP Islam Al Makiyah'],
            [4, 'MTs Negeri 3 Jakarta'],
            [5, 'SMP Wisata Indonesia'],
        ];
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA SMP',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // NO
            'B' => 50,  // NAMA SMP
        ];
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return [
            'font' => [
                'name' => 'Calibri',
                'size' => 11,
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            4 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => '059669'], // Green header for template
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Title Banner
                $sheet->mergeCells('A1:B1');
                $sheet->setCellValue('A1', 'TEMPLATE IMPORT DATA SMP / MTs');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['argb' => '065F46'],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Subtitle Instruction
                $sheet->mergeCells('A2:B2');
                $sheet->setCellValue('A2', 'Petunjuk: Isi Nama SMP pada kolom "NAMA SMP". Jangan mengedit atau menghapus header kolom di baris ke-4.');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 10,
                        'color' => ['argb' => '374151'],
                    ],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(26);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(26);

                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'D1D5DB'],
                        ],
                    ],
                ];
                $sheet->getStyle('A4:B9')->applyFromArray($styleArray);
                $sheet->getStyle('A5:A9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
