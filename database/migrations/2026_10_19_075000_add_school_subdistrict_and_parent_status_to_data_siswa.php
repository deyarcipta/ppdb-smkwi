<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('data_siswa', function (Blueprint $table) {
            if (!Schema::hasColumn('data_siswa', 'kecamatan_desa_sekolah_asal')) {
                $table->string('kecamatan_desa_sekolah_asal', 100)->nullable()->after('status_sekolah_asal');
            }
            if (!Schema::hasColumn('data_siswa', 'status_ayah')) {
                $table->string('status_ayah', 50)->default('Masih Hidup')->nullable()->after('nama_ayah');
            }
            if (!Schema::hasColumn('data_siswa', 'status_ibu')) {
                $table->string('status_ibu', 50)->default('Masih Hidup')->nullable()->after('nama_ibu');
            }
        });

        // Salin data kota_sekolah_asal yang ada ke kecamatan_desa_sekolah_asal
        if (Schema::hasColumn('data_siswa', 'kota_sekolah_asal') && Schema::hasColumn('data_siswa', 'kecamatan_desa_sekolah_asal')) {
            DB::statement("UPDATE data_siswa SET kecamatan_desa_sekolah_asal = kota_sekolah_asal WHERE kecamatan_desa_sekolah_asal IS NULL AND kota_sekolah_asal IS NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_siswa', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('data_siswa', 'kecamatan_desa_sekolah_asal')) {
                $columnsToDrop[] = 'kecamatan_desa_sekolah_asal';
            }
            if (Schema::hasColumn('data_siswa', 'status_ayah')) {
                $columnsToDrop[] = 'status_ayah';
            }
            if (Schema::hasColumn('data_siswa', 'status_ibu')) {
                $columnsToDrop[] = 'status_ibu';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
