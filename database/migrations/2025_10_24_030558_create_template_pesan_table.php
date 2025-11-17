<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_pesan', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_pesan', [
                'pendaftaran_baru',
                'aktifasi_akun',
                'verifikasi_formulir',
                'verifikasi_ppdb',
                'pendaftar_diterima',
                'data_lengkap',
                'belum_bayar',
                'sudah_bayar_belum_isi',
            ]);
            $table->string('judul');
            $table->text('isi_pesan');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_pesan');
    }
};
