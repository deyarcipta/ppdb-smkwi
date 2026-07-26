<?php

namespace App\Exports;

use App\Models\DataSmp;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Style;

class DataSmpExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithDefaultStyles, WithCustomStartCell, WithEvents
{
    protected $search;
    protected $dataCount = 0;
    protected $no = 0;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        $query = DataSmp::withCount('dataSiswa');

        if ($this->search) {
            $query->where('nama_smp', 'like', "%{$this->search}%");
        }

        $data = $query->orderBy('nama_smp', 'asc')->get();
        $this->dataCount = $data->count();

        return $data;
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA SMP / MTs',
            'TOTAL SISWA DAFTAR',
            'TANGGAL DITAMBAHKAN',
        ];
    }

    public function map($row): array
    {
        $this->no++;
        return [
            $this->no,
            $row->nama_smp,
            $row->total_siswa_count,
            $row->created_at ? $row->created_at->format('d/m/Y H:i') : '-',

        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // NO
            'B' => 45,  // NAMA SMP
            'C' => 22,  // TOTAL SISWA DAFTAR
            'D' => 25,  // TANGGAL DITAMBAHKAN
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
                    'startColor' => ['argb' => '1E3A8A'],
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
                $sheet->mergeCells('A1:D1');
                $sheet->setCellValue('A1', 'DATA DAFTAR SMP / MTs - PPDB');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['argb' => '1E3A8A'],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Subtitle
                $sheet->mergeCells('A2:D2');
                $tanggalExport = date('d F Y H:i');
                $sheet->setCellValue('A2', "Diunduh pada: {$tanggalExport} WIB | Total Data: {$this->dataCount} Sekolah");
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 10,
                        'color' => ['argb' => '4B5563'],
                    ],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(28);

                $lastRow = 4 + $this->dataCount;
                if ($this->dataCount > 0) {
                    $styleArray = [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => 'D1D5DB'],
                            ],
                        ],
                    ];
                    $sheet->getStyle("A4:D{$lastRow}")->applyFromArray($styleArray);

                    // Alignments
                    $sheet->getStyle("A5:A{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("C5:D{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }
}
