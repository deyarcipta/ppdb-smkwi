<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kuota_jurusan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurusan_id')
                ->constrained('jurusans')
                ->onDelete('cascade');
            $table->foreignId('gelombang_id')
                ->constrained('gelombang_pendaftaran')
                ->onDelete('cascade');
            $table->integer('kuota');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kuota_jurusan');
    }
};
