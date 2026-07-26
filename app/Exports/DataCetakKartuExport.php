<?php

namespace App\Exports;

use App\Models\DataSiswa;
use App\Models\PengaturanAplikasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Style;

class DataCetakKartuExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithDefaultStyles, WithCustomValueBinder, WithCustomStartCell, WithEvents
{
    protected $search;
    protected $jurusanId;
    protected $gelombangId;
    protected $pengaturan;
    protected $dataCount = 0;
    protected $no = 0;

    public function __construct($search = null, $jurusanId = null, $gelombangId = null)
    {
        $this->search = $search;
        $this->jurusanId = $jurusanId;
        $this->gelombangId = $gelombangId;
        $this->pengaturan = PengaturanAplikasi::getSettings();
    }

    public function bindValue(Cell $cell, $value)
    {
        $column = $cell->getColumn();
        // Format string/number columns explicitly as text to prevent scientific notation (e.g. NISN, No Pendaftaran)
        if (in_array($column, ['B', 'E', 'I', 'J', 'K', 'L'])) {
            $val = is_null($value) ? '' : (string) $value;
            $cell->setValueExplicit($val, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function collection()
    {
        $query = DataSiswa::with(['user', 'jurusan', 'gelombang.tahunAjaran']);

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('username', 'like', "%{$search}%");
                  });
            });
        }

        if ($this->jurusanId) {
            $query->where('jurusan_id', $this->jurusanId);
        }

        if ($this->gelombangId) {
            $query->where('gelombang_id', $this->gelombangId);
        }

        $data = $query->orderBy('created_at', 'desc')->get();
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
            'NO PENDAFTARAN',
            'NAMA LENGKAP',
            'EMAIL',
            'NISN',
            'JURUSAN',
            'GELOMBANG',
            'TAHUN AJARAN',
            'USERNAME KARTU',
            'PASSWORD KARTU',
            'USERNAME LOGIN PORTAL',
            'PASSWORD LOGIN PORTAL',
            'STATUS AKUN',
            'TANGGAL PENDAFTARAN',
        ];
    }

    public function map($row): array
    {
        $this->no++;
        $user = $row->user;

        // Username & Password Kartu
        $usernameKartu = empty($this->pengaturan->kartu_username_contoh) || $this->pengaturan->kartu_username_contoh === '[Username Anda]'
            ? ($user->username ?? '-')
            : $this->pengaturan->kartu_username_contoh . (isset($user->username) ? substr($user->username, -3) : '');

        $passwordKartu = !empty($this->pengaturan->kartu_password_contoh)
            ? $this->pengaturan->kartu_password_contoh
            : (isset($user->id) ? str_pad(abs(crc32($user->id . $user->username)) % 900000 + 100000, 6, '0', STR_PAD_LEFT) : '123456');

        return [
            $this->no,
            $row->no_pendaftaran ?? '-',
            $row->nama_lengkap ?? '-',
            $row->email ?? ($user->email ?? '-'),
            $row->nisn ?? '-',
            $row->jurusan->nama_jurusan ?? '-',
            $row->gelombang->nama_gelombang ?? '-',
            $row->gelombang->tahunAjaran->nama ?? '-',
            $usernameKartu,
            $passwordKartu,
            $user->username ?? '-',
            $user->password_plain ?? 'password123',
            'Aktif',
            $row->created_at ? $row->created_at->format('d/m/Y H:i') : '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // NO
            'B' => 22,  // NO PENDAFTARAN
            'C' => 30,  // NAMA LENGKAP
            'D' => 28,  // EMAIL
            'E' => 18,  // NISN
            'F' => 30,  // JURUSAN
            'G' => 20,  // GELOMBANG
            'H' => 18,  // TAHUN AJARAN
            'I' => 22,  // USERNAME KARTU
            'J' => 22,  // PASSWORD KARTU
            'K' => 25,  // USERNAME LOGIN PORTAL
            'L' => 25,  // PASSWORD LOGIN PORTAL
            'M' => 15,  // STATUS AKUN
            'N' => 22,  // TANGGAL PENDAFTARAN
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
                    'startColor' => ['argb' => '2B4C7E'],
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
                $sheet->mergeCells('A1:N1');
                $sheet->setCellValue('A1', 'DATA CETAK KARTU PESERTA PPDB');
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

                // Subtitle / Info filter
                $sheet->mergeCells('A2:N2');
                $tanggalExport = date('d F Y H:i');
                $sheet->setCellValue('A2', "Diunduh pada: {$tanggalExport} WIB | Total Data: {$this->dataCount} Peserta");
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
                    $sheet->getStyle("A4:N{$lastRow}")->applyFromArray($styleArray);

                    // Center alignment for specific columns
                    $sheet->getStyle("A5:A{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("B5:B{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("E5:E{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("I5:N{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }
}
