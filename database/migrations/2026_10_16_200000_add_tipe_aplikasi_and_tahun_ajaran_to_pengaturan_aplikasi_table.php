<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaturan_aplikasi', function (Blueprint $table) {
            if (!Schema::hasColumn('pengaturan_aplikasi', 'tipe_aplikasi')) {
                $table->string('tipe_aplikasi', 20)->default('PPDB')->after('nama_aplikasi');
            }
            if (!Schema::hasColumn('pengaturan_aplikasi', 'tahun_ajaran_id')) {
                $table->unsignedBigInteger('tahun_ajaran_id')->nullable()->after('tipe_aplikasi');
                $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajaran')->onDelete('set null');
            }
        });

        // Inisialisasi data yang sudah ada
        $activeTa = DB::table('tahun_ajaran')->where('status', 'aktif')->first();
        $pengaturan = DB::table('pengaturan_aplikasi')->first();

        if ($pengaturan) {
            $tipe = (stripos($pengaturan->nama_aplikasi ?? '', 'SPMB') !== false) ? 'SPMB' : 'PPDB';
            DB::table('pengaturan_aplikasi')->where('id', $pengaturan->id)->update([
                'tipe_aplikasi' => $tipe,
                'tahun_ajaran_id' => $activeTa ? $activeTa->id : null,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('pengaturan_aplikasi', function (Blueprint $table) {
            if (Schema::hasColumn('pengaturan_aplikasi', 'tahun_ajaran_id')) {
                $table->dropForeign(['tahun_ajaran_id']);
                $table->dropColumn('tahun_ajaran_id');
            }
            if (Schema::hasColumn('pengaturan_aplikasi', 'tipe_aplikasi')) {
                $table->dropColumn('tipe_aplikasi');
            }
        });
    }
};
