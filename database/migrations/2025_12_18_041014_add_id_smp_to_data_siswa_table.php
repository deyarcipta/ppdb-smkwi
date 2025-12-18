<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('data_siswa', function (Blueprint $table) {
            // Tambah kolom id_smp
            $table->unsignedBigInteger('id_smp')->nullable()->after('asal_sekolah');
            
            // Tambah foreign key constraint
            $table->foreign('id_smp')
                  ->references('id_smp')
                  ->on('data_smp')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
            
            // Index untuk performa
            $table->index('id_smp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_siswa', function (Blueprint $table) {
            // Hapus foreign key dan index
            $table->dropForeign(['id_smp']);
            $table->dropIndex(['id_smp']);
            
            // Hapus kolom
            $table->dropColumn('id_smp');
        });
    }
};