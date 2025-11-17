<?php

namespace App\Exports;

use App\Models\UserSiswa;
use App\Models\MasterBiaya;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanPembayaranExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected $namaSiswa;
    protected $jenisPembayaran;
    protected $tanggalAwal;
    protected $tanggalAkhir;
    protected $totalFormulir = 0;
    protected $totalPPDB = 0;
    protected $totalSemua = 0;

    public function __construct($namaSiswa = null, $jenisPembayaran = null, $tanggalAwal = null, $tanggalAkhir = null)
    {
        $this->namaSiswa = $namaSiswa;
        $this->jenisPembayaran = $jenisPembayaran;
        $this->tanggalAwal = $tanggalAwal;
        $this->tanggalAkhir = $tanggalAkhir;
    }

    public function collection()
    {
        $query = UserSiswa::with(['pembayaran' => function($query) {
            $query->where('status', 'diverifikasi')
                  ->orderBy('created_at', 'asc');
            
            if ($this->jenisPembayaran) {
                $query->where('jenis_pembayaran', $this->jenisPembayaran);
            }
            
            if ($this->tanggalAwal && $this->tanggalAkhir) {
                $query->whereBetween('created_at', [
                    $this->tanggalAwal,
                    $this->tanggalAkhir
                ]);
            }
        }]);

        if ($this->namaSiswa) {
            $query->whereHas('datasiswa', function($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->namaSiswa . '%');
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No Pendaftaran',
            'Nama Siswa',
            'Jumlah Cicilan',
            'Formulir',
            'Cicilan 1',
            'Tanggal Cicilan 1',
            'Cicilan 2',
            'Tanggal Cicilan 2',
            'Cicilan 3',
            'Tanggal Cicilan 3',
            'Cicilan 4',
            'Tanggal Cicilan 4',
            'Cicilan 5',
            'Tanggal Cicilan 5',
            'Total',
            'Keterangan'
        ];
    }

    public function map($siswa): array
    {
        // Reset totals untuk setiap siswa
        $totalFormulirSiswa = 0;
        $totalPPDBSiswa = 0;
        $totalSemuaSiswa = 0;

        // Ambil semua pembayaran yang diverifikasi, diurutkan berdasarkan created_at
        $allPayments = $siswa->pembayaran->where('status', 'diverifikasi')->sortBy('created_at');
        
        // Pisahkan pembayaran formulir dan cicilan PPDB
        $pembayaranFormulir = $allPayments->where('jenis_pembayaran', 'formulir')->first();
        $pembayaranCicilan = $allPayments->where('jenis_pembayaran', 'ppdb');
        
        $formulir = $pembayaranFormulir ? 'Rp ' . number_format($pembayaranFormulir->jumlah, 0, ',', '.') : '-';
        $totalFormulirSiswa = $pembayaranFormulir ? $pembayaranFormulir->jumlah : 0;
        
        // Inisialisasi array untuk cicilan
        $cicilanData = [];
        for ($i = 1; $i <= 5; $i++) {
            $cicilanData["cicilan{$i}"] = '-';
            $cicilanData["tanggal_cicilan{$i}"] = '-';
        }
        
        // Isi data cicilan yang ada
        $index = 1;
        foreach ($pembayaranCicilan as $pembayaran) {
            if ($index <= 5) {
                $cicilanData["cicilan{$index}"] = 'Rp ' . number_format($pembayaran->jumlah, 0, ',', '.');
                $cicilanData["tanggal_cicilan{$index}"] = $pembayaran->created_at->format('d/m/Y');
                $totalPPDBSiswa += $pembayaran->jumlah;
                $index++;
            }
        }
        
        // Hitung total per siswa
        $totalSemuaSiswa = $totalFormulirSiswa + $totalPPDBSiswa;
        
        // Tambahkan ke total keseluruhan
        $this->totalFormulir += $totalFormulirSiswa;
        $this->totalPPDB += $totalPPDBSiswa;
        $this->totalSemua += $totalSemuaSiswa;

        $keterangan = $allPayments->count() . ' pembayaran diverifikasi';

        return [
            $siswa->datasiswa->no_pendaftaran ?? '-',
            $siswa->datasiswa->nama_lengkap ?? $siswa->nama,
            $pembayaranCicilan->count() . ' kali',
            $formulir,
            $cicilanData['cicilan1'],
            $cicilanData['tanggal_cicilan1'],
            $cicilanData['cicilan2'],
            $cicilanData['tanggal_cicilan2'],
            $cicilanData['cicilan3'],
            $cicilanData['tanggal_cicilan3'],
            $cicilanData['cicilan4'],
            $cicilanData['tanggal_cicilan4'],
            $cicilanData['cicilan5'],
            $cicilanData['tanggal_cicilan5'],
            'Rp ' . number_format($totalSemuaSiswa, 0, ',', '.'),
            $keterangan
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Auto size semua kolom dari A sampai P
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'];
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Definisikan styles
        $styles = [
            // Header row
            1 => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFE6E6FA', // Warna ungu muda untuk header
                    ],
                ],
            ],
        ];

        // Alignment untuk kolom tertentu
        // Kolom angka (right align)
        $angkaColumns = ['D', 'E', 'G', 'I', 'K', 'M', 'O'];
        foreach ($angkaColumns as $col) {
            $styles[$col . '2:' . $col . $sheet->getHighestRow()] = [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                ],
            ];
        }

        // Kolom tanggal (center align)
        $tanggalColumns = ['F', 'H', 'J', 'L', 'N'];
        foreach ($tanggalColumns as $col) {
            $styles[$col . '2:' . $col . $sheet->getHighestRow()] = [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ];
        }

        // Kolom teks (left align)
        $teksColumns = ['A', 'B', 'P'];
        foreach ($teksColumns as $col) {
            $styles[$col . '2:' . $col . $sheet->getHighestRow()] = [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ],
            ];
        }

        // Kolom jumlah cicilan (center align)
        $styles['C2:C' . $sheet->getHighestRow()] = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];

        // Vertical center untuk semua cells
        $styles['A1:P' . $sheet->getHighestRow()] = [
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        return $styles;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                
                // Tambahkan baris total di bawah data
                $totalRow = $lastRow + 2;
                
                // Format untuk baris total
                $sheet->setCellValue('A' . $totalRow, 'TOTAL KESELURUHAN');
                $sheet->setCellValue('D' . $totalRow, 'Rp ' . number_format($this->totalFormulir, 0, ',', '.'));
                $sheet->setCellValue('O' . $totalRow, 'Rp ' . number_format($this->totalSemua, 0, ',', '.'));
                
                // Merge cells untuk judul total
                $sheet->mergeCells('A' . $totalRow . ':C' . $totalRow);
                
                // Style untuk baris total
                $totalStyle = [
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FF2E75B5', // Warna biru
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ];
                
                $sheet->getStyle('A' . $totalRow . ':P' . $totalRow)->applyFromArray($totalStyle);
                
                // Detail total per jenis pembayaran
                $detailRow = $lastRow + 3;
                $sheet->setCellValue('A' . $detailRow, 'Rincian Total:');
                $sheet->setCellValue('B' . $detailRow, 'Formulir: Rp ' . number_format($this->totalFormulir, 0, ',', '.'));
                
                $detailRow++;
                $sheet->setCellValue('B' . $detailRow, 'PPDB: Rp ' . number_format($this->totalPPDB, 0, ',', '.'));
                
                $detailRow++;
                $sheet->setCellValue('B' . $detailRow, 'Total Semua: Rp ' . number_format($this->totalSemua, 0, ',', '.'));
                
                // Style untuk detail
                $detailStyle = [
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ],
                ];
                
                $sheet->getStyle('A' . ($lastRow + 3) . ':B' . $detailRow)->applyFromArray($detailStyle);

                // Conditional formatting untuk baris yang totalnya sama dengan total_biaya
                $totalBiayaFormulir = MasterBiaya::where('jenis_biaya', 'formulir')->value('total_biaya') ?? 0;
                $totalBiayaPPDB = MasterBiaya::where('jenis_biaya', 'ppdb')->value('total_biaya') ?? 0;
                $totalBiayaSemua = $totalBiayaFormulir + $totalBiayaPPDB;

                for ($row = 2; $row <= $lastRow; $row++) {
                    // Ambil nilai total dari kolom O
                    $totalCellValue = $sheet->getCell('O' . $row)->getValue();
                    $totalNumeric = 0;
                    
                    if ($totalCellValue && strpos($totalCellValue, 'Rp ') !== false) {
                        $cleanValue = str_replace(['Rp ', '.', ' '], '', $totalCellValue);
                        $totalNumeric = (float) $cleanValue;
                    }
                    
                    // Jika total pembayaran sama dengan total biaya, beri background hijau
                    if ($totalNumeric > 0 && $totalNumeric == $totalBiayaSemua) {
                        $range = 'A' . $row . ':P' . $row;
                        
                        $sheet->getStyle($range)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => [
                                    'argb' => 'FF90EE90', // Warna hijau muda
                                ],
                            ],
                        ]);
                    }
                }

                // Tambahkan border untuk seluruh data
                $dataRange = 'A1:P' . $lastRow;
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
            },
        ];
    }
}