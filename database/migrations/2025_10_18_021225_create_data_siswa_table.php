<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_siswa', function (Blueprint $table) {
            $table->id();

            // Relasi ke users_siswa (akun login)
            $table->foreignId('user_id')->constrained('users_siswa')->onDelete('cascade');

            // Relasi ke jurusan dan gelombang
            $table->foreignId('jurusan_id')
                  ->nullable()
                  ->constrained('jurusans')
                  ->onDelete('set null');
                  
            $table->unsignedBigInteger('gelombang_id')->nullable();
            $table->foreign('gelombang_id')
                  ->references('id')
                  ->on('gelombang_pendaftaran')
                  ->onDelete('set null');

            // Status pendaftaran
            $table->boolean('is_form_completed')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_paid')->default(false);
            $table->string('no_pendaftaran')->nullable();

            // ======== DATA PRIBADI SISWA ========
            $table->string('nisn')->nullable();
            $table->string('nik')->nullable();
            $table->string('no_kk')->nullable();
            $table->string('nama_lengkap');
            $table->enum('status_pendaftar', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->string('ket_pendaftaran')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-Laki', 'Perempuan'])->nullable();
            $table->string('no_hp')->nullable();
            $table->string('asal_sekolah')->nullable();
            $table->string('agama')->nullable();
            $table->string('ukuran_baju')->nullable();
            $table->string('hobi')->nullable();
            $table->string('cita_cita')->nullable();
            $table->text('alamat')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos')->nullable();
            $table->integer('anak_ke')->nullable();
            $table->integer('jumlah_saudara')->nullable();
            $table->integer('tinggi_badan')->nullable();
            $table->integer('berat_badan')->nullable();
            $table->string('status_dalam_keluarga')->nullable();
            $table->string('tinggal_bersama')->nullable();
            $table->integer('jarak_kesekolah')->nullable();
            $table->integer('waktu_tempuh')->nullable();
            $table->string('transportasi')->nullable();
            $table->string('no_kip')->nullable();
            $table->string('referensi')->nullable();
            $table->string('ket_referensi')->nullable();

            // ======== DATA AYAH ========
            $table->string('nik_ayah')->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('tempat_lahir_ayah')->nullable();
            $table->date('tanggal_lahir_ayah')->nullable();
            $table->string('pendidikan_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('penghasilan_ayah')->nullable();
            $table->string('no_hp_ayah')->nullable();

            // ======== DATA IBU ========
            $table->string('nik_ibu')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('tempat_lahir_ibu')->nullable();
            $table->date('tanggal_lahir_ibu')->nullable();
            $table->string('pendidikan_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('penghasilan_ibu')->nullable();
            $table->string('no_hp_ibu')->nullable();

            // ======== DATA WALI (opsional) ========
            $table->string('nik_wali')->nullable();
            $table->string('nama_wali')->nullable();
            $table->string('tempat_lahir_wali')->nullable();
            $table->date('tanggal_lahir_wali')->nullable();
            $table->string('pendidikan_wali')->nullable();
            $table->string('pekerjaan_wali')->nullable();
            $table->string('penghasilan_wali')->nullable();
            $table->string('no_hp_wali')->nullable();

            $table->timestamps();
            
            // Index untuk performa
            $table->index('gelombang_id');
            $table->index('jurusan_id');
            $table->index('no_pendaftaran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_siswa');
    }
};