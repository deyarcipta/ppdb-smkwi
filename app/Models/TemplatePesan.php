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

    // Optional: untuk label readable
    public static function jenisList()
    {
        return [
            'pendaftaran_baru' => 'Pesan Pendaftaran Baru',
            'aktifasi_akun' => 'Pesan Aktivasi Akun',
            'verifikasi_formulir' => 'Pesan Verifikasi Pembayaran Formulir',
            'verifikasi_ppdb' => 'Pesan Verifikasi Pembayaran PPDB',
            'pendaftar_diterima' => 'Pesan Pendaftar Diterima',
            'data_lengkap' => 'Pesan Data Sudah Lengkap',
            'belum_bayar' => 'Pesan Belum Bayar Formulir',
            'sudah_bayar_belum_isi' => 'Pesan Sudah Bayar Tapi Belum Isi Data',
        ];
    }
}
