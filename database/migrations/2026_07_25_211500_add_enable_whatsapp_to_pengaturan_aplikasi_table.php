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
            $table->boolean('enable_whatsapp')->default(false)->after('enable_cetak_kartu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_aplikasi', function (Blueprint $table) {
            $table->dropColumn('enable_whatsapp');
        });
    }
};
