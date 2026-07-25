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
        Schema::table('pengaturan_aplikasi', function (Blueprint $table) {
            $table->string('hero_bg')->nullable()->after('ttd_stempel');
            $table->string('warna_utama')->default('#6b21a8')->after('hero_bg');
            $table->string('warna_sekunder')->default('#16a34a')->after('warna_utama');
            $table->string('warna_header')->default('#a948ea')->after('warna_sekunder');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_aplikasi', function (Blueprint $table) {
            $table->dropColumn(['hero_bg', 'warna_utama', 'warna_sekunder', 'warna_header']);
        });
    }
};
