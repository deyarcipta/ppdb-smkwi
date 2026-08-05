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
            $table->string('warna_teks_hero')->default('#2E004F')->after('warna_header');
            $table->string('warna_motto_hero')->default('#6b21a8')->after('warna_teks_hero');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_aplikasi', function (Blueprint $table) {
            $table->dropColumn(['warna_teks_hero', 'warna_motto_hero']);
        });
    }
};
