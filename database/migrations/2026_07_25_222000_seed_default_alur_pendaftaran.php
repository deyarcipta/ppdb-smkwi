<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $count = DB::table('persyaratan_pendaftaran')->where('tipe', 'alur')->count();
        if ($count === 0) {
            DB::table('persyaratan_pendaftaran')->insert([
                [
                    'judul' => 'Registrasi Awal',
                    'konten' => 'Siswa melakukan registrasi awal di website PPDB',
                    'sub_konten' => "Klik tombol \"Pendaftaran PPDB\"\nIsi data dasar (nama, email, no. telepon)\nSubmit formulir registrasi",
                    'tipe' => 'alur',
                    'urutan' => 1,
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'judul' => 'Menerima Akses Login',
                    'konten' => 'Siswa akan mendapatkan username dan password melalui WhatsApp',
                    'sub_konten' => "Username dan password dikirim via WhatsApp\nInformasi lengkap cara login\nSimpan baik-baik informasi login",
                    'tipe' => 'alur',
                    'urutan' => 2,
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'judul' => 'Login & Pembayaran Formulir',
                    'konten' => 'Siswa login dan melakukan pembayaran formulir pendaftaran',
                    'sub_konten' => "Login dengan username/password yang diterima\nLakukan pembayaran biaya formulir\nUpload bukti pembayaran formulir",
                    'tipe' => 'alur',
                    'urutan' => 3,
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'judul' => 'Pengisian Formulir Lengkap',
                    'konten' => 'Siswa mengisi formulir pendaftaran secara lengkap',
                    'sub_konten' => "Data pribadi lengkap\nData orang tua/wali\nData pendidikan sebelumnya\nPilihan program keahlian",
                    'tipe' => 'alur',
                    'urutan' => 4,
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'judul' => 'Pembayaran PPDB',
                    'konten' => 'Siswa melakukan pembayaran biaya PPDB',
                    'sub_konten' => "Pembayaran dapat dilakukan melalui transfer ataupun cash\nUpload bukti pembayaran PPDB",
                    'tipe' => 'alur',
                    'urutan' => 5,
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'judul' => 'Verifikasi & Pengumuman',
                    'konten' => 'Menunggu proses verifikasi oleh panitia PPDB',
                    'sub_konten' => "Panitia melakukan verifikasi data dan pembayaran\nPengumuman hasil seleksi via portal siswa\nCetak bukti penerimaan jika dinyatakan LULUS",
                    'tipe' => 'alur',
                    'urutan' => 6,
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down()
    {
        DB::table('persyaratan_pendaftaran')->where('tipe', 'alur')->delete();
    }
};
