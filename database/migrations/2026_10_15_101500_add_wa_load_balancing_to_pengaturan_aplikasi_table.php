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
            if (!Schema::hasColumn('pengaturan_aplikasi', 'wa_load_balancing')) {
                $table->boolean('wa_load_balancing')->default(true)->after('wa_session_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_aplikasi', function (Blueprint $table) {
            if (Schema::hasColumn('pengaturan_aplikasi', 'wa_load_balancing')) {
                $table->dropColumn('wa_load_balancing');
            }
        });
    }
};
