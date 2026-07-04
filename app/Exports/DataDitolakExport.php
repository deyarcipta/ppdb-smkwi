<?php

namespace App\Exports;

use App\Models\UserSiswa;
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

class DataDitolakExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithDefaultStyles, WithCustomValueBinder, WithCustomStartCell, WithEvents
{
    protected $dataCount = 0;

    public function bindValue(Cell $cell, $value)
    {
        $column = $cell->getColumn();
        
        // Format columns containing numbers with long digits (or leading zeros) explicitly as text
        if (in_array($column, ['F', 'G', 'H', 'M', 'AI', 'AO', 'AS', 'AW'])) {
            // If the value is null or empty, write empty string
            $val = is_null($value) ? '' : (string) $value;
            $cell->setValueExplicit($val, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }
    public function collection()
    {
        $collection = UserSiswa::with([
            'dataSiswa', 
            'dataSiswa.gelombang', 
            'dataSiswa.jurusan',
            'pembayaran'
        ])
        ->whereHas('dataSiswa', function($query) {
            $query->where('status_pendaftar', 'ditolak');
        })
        ->orderBy('created_at', 'desc')
        ->get();

        $this->dataCount = $collection->count();
        return $collection;
    }

    public function headings(): array
    {
        return [
            'NO PENDAFTARAN',
            'USERNAME',
            'PASSWORD',
            'EMAIL',
            'NAMA LENGKAP',
            'NISN',
            'NIK',
            'NO KK',
            'JENIS KELAMIN',
            'TEMPAT LAHIR',
            'TANGGAL LAHIR',
            'AGAMA',
            'NO HP',
            'ASAL SEKOLAH',
            'UKURAN BAJU',
            'HOBI',
            'CITA-CITA',
            'ALAMAT',
            'RT',
            'RW',
            'DESA',
            'KECAMATAN',
            'KOTA',
            'PROVINSI',
            'KODE POS',
            'ANAK KE',
            'JUMLAH SAUDARA',
            'TINGGI BADAN',
            'BERAT BADAN',
            'STATUS DALAM KELUARGA',
            'TINGGAL BERSAMA',
            'JARAK KE SEKOLAH (KM)',
            'WAKTU TEMPUH (MENIT)',
            'TRANSPORTASI',
            'NO KIP',
            'GELOMBANG',
            'JURUSAN',
            'NAMA AYAH',
            'PEKERJAAN AYAH',
            'PENDIDIKAN AYAH',
            'NO HP AYAH',
            'NAMA IBU',
            'PEKERJAAN IBU',
            'PENDIDIKAN IBU',
            'NO HP IBU',
            'NAMA WALI',
            'PEKERJAAN WALI',
            'PENDIDIKAN WALI',
            'NO HP WALI',
            'TOTAL PEMBAYARAN',
            'STATUS PEMBAYARAN',
            'ALASAN PENOLAKAN',
            'TANGGAL DAFTAR'
        ];
    }

    public function map($user): array
    {
        $dataSiswa = $user->dataSiswa;
        
        // Hitung total pembayaran
        $totalBayar = $user->pembayaran->where('status', 'diverifikasi')->sum('jumlah');
        $statusPembayaran = 'Belum Bayar';
        
        if ($totalBayar > 0) {
            $masterBiaya = \App\Models\MasterBiaya::first();
            $totalBiaya = $masterBiaya ? $masterBiaya->total_biaya : 2500000;
            
            if ($totalBayar >= $totalBiaya) {
                $statusPembayaran = 'Lunas';
            } else {
                $statusPembayaran = 'Belum Lunas';
            }
        }

        // Cek jika ada pembayaran pending
        $pembayaranPending = $user->pembayaran->where('status', 'pending')->count() > 0;
        if ($pembayaranPending) {
            $statusPembayaran = 'Menunggu Verifikasi';
        }

        return [
            $dataSiswa->no_pendaftaran ?? $user->username,
            $user->username,
            $user->password_plain ?? 'password123',
            $user->email,
            $dataSiswa->nama_lengkap ?? '-',
            $dataSiswa->nisn ?? '-',
            $dataSiswa->nik ?? '-',
            $dataSiswa->no_kk ?? '-',
            $dataSiswa->jenis_kelamin ?? '-',
            $dataSiswa->tempat_lahir ?? '-',
            $dataSiswa->tanggal_lahir ? \Carbon\Carbon::parse($dataSiswa->tanggal_lahir)->format('d/m/Y') : '-',
            $dataSiswa->agama ?? '-',
            $dataSiswa->no_hp ?? '-',
            $dataSiswa->asal_sekolah ?? '-',
            $dataSiswa->ukuran_baju ?? '-',
            $dataSiswa->hobi ?? '-',
            $dataSiswa->cita_cita ?? '-',
            $dataSiswa->alamat ?? '-',
            $dataSiswa->rt ?? '-',
            $dataSiswa->rw ?? '-',
            $dataSiswa->desa ?? '-',
            $dataSiswa->kecamatan ?? '-',
            $dataSiswa->kota ?? '-',
            $dataSiswa->provinsi ?? '-',
            $dataSiswa->kode_pos ?? '-',
            $dataSiswa->anak_ke ?? '-',
            $dataSiswa->jumlah_saudara ?? '-',
            $dataSiswa->tinggi_badan ?? '-',
            $dataSiswa->berat_badan ?? '-',
            $dataSiswa->status_dalam_keluarga ?? '-',
            $dataSiswa->tinggal_bersama ?? '-',
            $dataSiswa->jarak_kesekolah ?? '-',
            $dataSiswa->waktu_tempuh ?? '-',
            $dataSiswa->transportasi ?? '-',
            $dataSiswa->no_kip ?? '-',
            $dataSiswa->gelombang->nama_gelombang ?? '-',
            $dataSiswa->jurusan->nama_jurusan ?? '-',
            $dataSiswa->nama_ayah ?? '-',
            $dataSiswa->pekerjaan_ayah ?? '-',
            $dataSiswa->pendidikan_ayah ?? '-',
            $dataSiswa->no_hp_ayah ?? '-',
            $dataSiswa->nama_ibu ?? '-',
            $dataSiswa->pekerjaan_ibu ?? '-',
            $dataSiswa->pendidikan_ibu ?? '-',
            $dataSiswa->no_hp_ibu ?? '-',
            $dataSiswa->nama_wali ?? '-',
            $dataSiswa->pekerjaan_wali ?? '-',
            $dataSiswa->pendidikan_wali ?? '-',
            $dataSiswa->no_hp_wali ?? '-',
            'Rp ' . number_format($totalBayar, 0, ',', '.'),
            $statusPembayaran,
            $dataSiswa->ket_pendaftaran ?? '-', // Alasan penolakan
            $user->created_at->format('d/m/Y H:i')
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // NO PENDAFTARAN
            'B' => 15, // USERNAME
            'C' => 15, // PASSWORD
            'D' => 25, // EMAIL
            'E' => 25, // NAMA LENGKAP
            'F' => 20, // NISN
            'G' => 20, // NIK
            'H' => 20, // NO KK
            'I' => 15, // JENIS KELAMIN
            'J' => 15, // TEMPAT LAHIR
            'K' => 15, // TANGGAL LAHIR
            'L' => 12, // AGAMA
            'M' => 15, // NO HP
            'N' => 25, // ASAL SEKOLAH
            'O' => 12, // UKURAN BAJU
            'P' => 15, // HOBI
            'Q' => 15, // CITA-CITA
            'R' => 30, // ALAMAT
            'S' => 8,  // RT
            'T' => 8,  // RW
            'U' => 15, // DESA
            'V' => 15, // KECAMATAN
            'W' => 15, // KOTA
            'X' => 15, // PROVINSI
            'Y' => 12, // KODE POS
            'Z' => 10, // ANAK KE
            'AA' => 15, // JUMLAH SAUDARA
            'AB' => 12, // TINGGI BADAN
            'AC' => 12, // BERAT BADAN
            'AD' => 20, // STATUS DALAM KELUARGA
            'AE' => 15, // TINGGAL BERSAMA
            'AF' => 18, // JARAK KE SEKOLAH
            'AG' => 18, // WAKTU TEMPUH
            'AH' => 15, // TRANSPORTASI
            'AI' => 15, // NO KIP
            'AJ' => 15, // GELOMBANG
            'AK' => 15, // JURUSAN
            'AL' => 20, // NAMA AYAH
            'AM' => 15, // PEKERJAAN AYAH
            'AN' => 15, // PENDIDIKAN AYAH
            'AO' => 15, // NO HP AYAH
            'AP' => 20, // NAMA IBU
            'AQ' => 15, // PEKERJAAN IBU
            'AR' => 15, // PENDIDIKAN IBU
            'AS' => 15, // NO HP IBU
            'AT' => 20, // NAMA WALI
            'AU' => 15, // PEKERJAAN WALI
            'AV' => 15, // PENDIDIKAN WALI
            'AW' => 15, // NO HP WALI
            'AX' => 18, // TOTAL PEMBAYARAN
            'AY' => 20, // STATUS PEMBAYARAN
            'AZ' => 30, // ALASAN PENOLAKAN
            'BA' => 18, // TANGGAL DAFTAR
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set row height untuk header (row 4)
        $sheet->getRowDimension(4)->setRowHeight(40);

        // Set wrap text untuk semua cell
        $sheet->getStyle('A:BA')->getAlignment()->setWrapText(true);

        // Set vertical alignment to top untuk semua cell
        $sheet->getStyle('A:BA')->getAlignment()->setVertical('top');

        return [
            // Style untuk header (row 4)
            4 => [
                'font' => [
                    'bold' => true,
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFfcccc'] // Background merah muda untuk data ditolak
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ],
            
            // Style untuk data rows (mulai baris 5)
            'A5:BA' . (4 + $this->dataCount) => [
                'alignment' => [
                    'vertical' => 'top',
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FFD0D0D0'],
                    ],
                ],
            ],
            
            // Style khusus untuk kolom alasan penolakan (warna background)
            'AZ' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFFF0F0']
                ],
            ],
        ];
    }

    public function defaultStyles(Style $defaultStyle)
    {
        // Set default font dan size untuk semua cell
        return [
            'font' => [
                'name' => 'Arial',
                'size' => 10,
            ],
        ];
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $actualLastRow = 4 + $this->dataCount;
                
                // 1. Tulis Judul Laporan di Baris 1
                $sheet->setCellValue('A1', 'LAPORAN DATA CALON SISWA DITOLAK - PPDB');
                $sheet->mergeCells('A1:BA1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['argb' => 'FFFF3E1D'] // Sneat Danger Color
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ]
                ]);
                
                // 2. Tulis Subjudul (Tanggal Ekspor) di Baris 2
                $sheet->setCellValue('A2', 'Diekspor pada: ' . now()->format('d/m/Y H:i') . ' | Total Data: ' . $this->dataCount . ' Calon Siswa');
                $sheet->mergeCells('A2:BA2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 10,
                        'color' => ['argb' => 'FF8592A3'] // Secondary text color
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ]
                ]);

                // Set tinggi baris untuk judul
                $sheet->getRowDimension(1)->setRowHeight(35);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(15); // Pemisah kosong

                // 3. Berikan warna background selang-seling (Zebra striping) pada baris data untuk meningkatkan keterbacaan
                for ($row = 5; $row <= $actualLastRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle('A' . $row . ':BA' . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFF4F6F9'] // soft blue-gray
                            ]
                        ]);
                    }
                }
            }
        ];
    }
}