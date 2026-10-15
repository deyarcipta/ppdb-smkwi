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
        Schema::table('users_siswa', function (Blueprint $table) {
            if (!Schema::hasColumn('users_siswa', 'remember_token')) {
                $table->rememberToken()->after('status_akun');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_siswa', function (Blueprint $table) {
            if (Schema::hasColumn('users_siswa', 'remember_token')) {
                $table->dropRememberToken();
            }
        });
    }
};
