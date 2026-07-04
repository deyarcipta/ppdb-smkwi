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
            $table->boolean('enable_cetak_kartu')->default(true)->after('maintenance_message');
            $table->string('kartu_username_contoh')->default('[Username Anda]')->after('enable_cetak_kartu');
            $table->string('kartu_password_contoh')->default('password123')->after('kartu_username_contoh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_aplikasi', function (Blueprint $table) {
            $table->dropColumn(['enable_cetak_kartu', 'kartu_username_contoh', 'kartu_password_contoh']);
        });
    }
};
