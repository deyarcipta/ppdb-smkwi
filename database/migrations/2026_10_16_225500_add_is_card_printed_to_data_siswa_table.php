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
            if (!Schema::hasColumn('data_siswa', 'is_card_printed')) {
                $table->boolean('is_card_printed')->default(false)->after('is_paid');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_siswa', function (Blueprint $table) {
            if (Schema::hasColumn('data_siswa', 'is_card_printed')) {
                $table->dropColumn('is_card_printed');
            }
        });
    }
};
