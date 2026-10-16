<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\PengaturanAplikasi;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $pengaturan = PengaturanAplikasi::first();
        $tipe = $pengaturan->tipe_aplikasi ?? 'PPDB';
        $otherTipe = ($tipe === 'SPMB') ? 'PPDB' : 'SPMB';

        DB::statement("
            UPDATE data_siswa 
            SET no_pendaftaran = CONCAT(?, SUBSTRING(no_pendaftaran, 5)) 
            WHERE no_pendaftaran LIKE ?
        ", [$tipe, $otherTipe . '%']);

        DB::statement("
            UPDATE pembayaran 
            SET no_pendaftaran = CONCAT(?, SUBSTRING(no_pendaftaran, 5)) 
            WHERE no_pendaftaran LIKE ?
        ", [$tipe, $otherTipe . '%']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu reverse
    }
};
