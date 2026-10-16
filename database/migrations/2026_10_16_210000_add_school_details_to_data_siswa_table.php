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
            $table->string('jenjang_sekolah_asal', 50)->nullable()->after('asal_sekolah');
            $table->string('status_sekolah_asal', 50)->nullable()->after('jenjang_sekolah_asal');
            $table->string('kota_sekolah_asal', 100)->nullable()->after('status_sekolah_asal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_siswa', function (Blueprint $table) {
            $table->dropColumn(['jenjang_sekolah_asal', 'status_sekolah_asal', 'kota_sekolah_asal']);
        });
    }
};
