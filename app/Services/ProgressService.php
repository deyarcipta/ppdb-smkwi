<?php
// app/Services/ProgressService.php

namespace App\Services;

use App\Models\DataSiswa;

class ProgressService
{
    /**
     * Hitung progress pengisian data diri dan keluarga
     */
    public function hitungProgressDataDiri($dataSiswa)
    {
        if (!$dataSiswa) {
            return [
                'total' => 0,
                'data_diri' => 0,
                'alamat' => 0,
                'orangtua' => 0
            ];
        }

        // ======== KATEGORI DATA DIRI ========
        $dataDiriFields = [
            'nisn', 'nik', 'no_kk', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir',
            'jenis_kelamin', 'no_hp', 'asal_sekolah', 'agama', 'ukuran_baju', 'hobi',
            'cita_cita', 'anak_ke', 'jumlah_saudara', 'tinggi_badan', 'berat_badan',
            'status_dalam_keluarga', 'tinggal_bersama', 'jarak_kesekolah', 'waktu_tempuh',
            'transportasi', 'no_kip', 'referensi', 'ket_referensi',
        ];

        // ======== KATEGORI ALAMAT ========
        $alamatFields = [
            'alamat', 'rt', 'rw', 'desa', 'kecamatan', 'kota', 'provinsi', 'kode_pos',
        ];

        // ======== KATEGORI DATA ORANGTUA ========
        $orangtuaFields = [
            // Data Ayah
            'nik_ayah', 'nama_ayah', 'tempat_lahir_ayah', 'tanggal_lahir_ayah',
            'pendidikan_ayah', 'pekerjaan_ayah', 'penghasilan_ayah', 'no_hp_ayah',
            
            // Data Ibu
            'nik_ibu', 'nama_ibu', 'tempat_lahir_ibu', 'tanggal_lahir_ibu',
            'pendidikan_ibu', 'pekerjaan_ibu', 'penghasilan_ibu', 'no_hp_ibu',
            
            // Data Wali
            'nik_wali', 'nama_wali', 'tempat_lahir_wali', 'tanggal_lahir_wali',
            'pendidikan_wali', 'pekerjaan_wali', 'penghasilan_wali', 'no_hp_wali',
        ];

        // Hitung progress per kategori
        $progressDataDiri = $this->hitungProgressKategori($dataSiswa, $dataDiriFields);
        $progressAlamat = $this->hitungProgressKategori($dataSiswa, $alamatFields);
        $progressOrangtua = $this->hitungProgressKategori($dataSiswa, $orangtuaFields);

        // Hitung progress total
        $totalFields = count($dataDiriFields) + count($alamatFields) + count($orangtuaFields);
        $filledTotal = ($progressDataDiri['filled'] + $progressAlamat['filled'] + $progressOrangtua['filled']);
        
        $progressTotal = $totalFields > 0 ? round(($filledTotal / $totalFields) * 100) : 0;

        return [
            'total' => min($progressTotal, 100),
            'data_diri' => $progressDataDiri['percentage'],
            'alamat' => $progressAlamat['percentage'],
            'orangtua' => $progressOrangtua['percentage'],
            'details' => [
                'data_diri' => $progressDataDiri,
                'alamat' => $progressAlamat,
                'orangtua' => $progressOrangtua
            ]
        ];
    }

    /**
     * Helper method untuk menghitung progress per kategori
     */
    private function hitungProgressKategori($dataSiswa, $fields)
    {
        $totalFields = count($fields);
        $filledFields = 0;

        foreach ($fields as $field) {
            if (!empty($dataSiswa->$field)) {
                $filledFields++;
            }
        }

        $percentage = $totalFields > 0 ? round(($filledFields / $totalFields) * 100) : 0;

        return [
            'total_fields' => $totalFields,
            'filled_fields' => $filledFields,
            'filled' => $filledFields,
            'percentage' => min($percentage, 100)
        ];
    }
}