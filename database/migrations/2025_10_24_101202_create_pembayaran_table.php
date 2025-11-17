<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_pembayaran_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users_siswa')->onDelete('cascade'); // Siswa yang bayar
            $table->string('no_pendaftaran');
            $table->string('nama_siswa');
            $table->string('jenis_pembayaran'); // formulir, daftar_ulang, spp
            $table->decimal('jumlah', 15, 2);
            $table->string('metode_pembayaran')->nullable(); // transfer, tunai
            $table->string('bukti_pembayaran')->nullable();
            $table->date('tanggal_bayar')->nullable();
            $table->enum('status', ['pending', 'diverifikasi', 'ditolak'])->default('pending');
            $table->text('catatan')->nullable();
            
            // verified_by seharusnya merujuk ke admin (users), bukan siswa
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran');
    }
};