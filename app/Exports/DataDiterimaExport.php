<?php

namespace App\Exports;

use App\Models\UserSiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Style;

class DataDiterimaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithDefaultStyles
{
    public function collection()
    {
        return UserSiswa::with([
            'dataSiswa', 
            'dataSiswa.gelombang', 
            'dataSiswa.jurusan',
            'pembayaran'
        ])
        ->whereHas('dataSiswa', function($query) {
            $query->where('status_pendaftar', 'diterima');
        })
        ->orderBy('created_at', 'desc')
        ->get();
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
            'AZ' => 18, // TANGGAL DAFTAR
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set row height untuk header (row 1)
        $sheet->getRowDimension(1)->setRowHeight(40);

        // Set wrap text untuk semua cell
        $sheet->getStyle('A:AZ')->getAlignment()->setWrapText(true);

        // Set vertical alignment to top untuk semua cell
        $sheet->getStyle('A:AZ')->getAlignment()->setVertical('top');

        return [
            // Style untuk header (row 1)
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE0E0E0']
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
            
            // Style untuk data rows
            'A2:AZ1000' => [
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
            
            // Style khusus untuk kolom angka
            'Z:AC' => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'top',
                ],
            ],
            
            // Style untuk kolom tanggal
            'K' => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'top',
                ],
            ],
            
            // Style untuk kolom status pembayaran
            'AY' => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'top',
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
}