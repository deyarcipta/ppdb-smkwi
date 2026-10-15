<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplatePesan extends Model
{
    use HasFactory;

    protected $table = 'template_pesan';

    protected $fillable = [
        'jenis_pesan',
        'judul',
        'isi_pesan',
        'status',
    ];

    // Label readable untuk form tambah/edit (Hanya jenis pesan yang aktif digunakan sistem)
    public static function jenisList()
    {
        return [
            'pendaftaran_baru' => 'Pesan Pendaftaran Baru',
            'verifikasi_formulir' => 'Pesan Verifikasi Pembayaran Formulir',
            'verifikasi_ppdb' => 'Pesan Verifikasi Pembayaran PPDB',
            'pendaftar_diterima' => 'Pesan Pendaftar Diterima',
        ];
    }

    // Label readable untuk tabel list (termasuk legacy / data lama jika ada)
    public static function getLabel($jenis)
    {
        $all = self::jenisList() + [
            'aktifasi_akun' => 'Pesan Aktivasi Akun (Tidak Digunakan)',
            'data_lengkap' => 'Pesan Data Sudah Lengkap (Tidak Digunakan)',
            'belum_bayar' => 'Pesan Belum Bayar Formulir (Tidak Digunakan)',
            'sudah_bayar_belum_isi' => 'Pesan Sudah Bayar Belum Isi Data (Tidak Digunakan)',
        ];

        return $all[$jenis] ?? ucwords(str_replace('_', ' ', $jenis));
    }
}
