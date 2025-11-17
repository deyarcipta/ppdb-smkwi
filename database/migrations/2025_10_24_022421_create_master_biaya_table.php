<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_biaya', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gelombang_id')->constrained('gelombang_pendaftaran')->onDelete('cascade');
            $table->enum('jenis_biaya', ['formulir', 'ppdb'])->default('ppdb');
            $table->string('nama_biaya');
            $table->decimal('total_biaya', 12, 2);
            $table->decimal('diskon', 12, 2)->default(0);
            $table->longText('keterangan')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_biaya');
    }
};
