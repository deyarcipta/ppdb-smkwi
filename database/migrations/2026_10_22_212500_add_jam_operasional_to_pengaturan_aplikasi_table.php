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
        Schema::table('pengaturan_aplikasi', function (Blueprint $table) {
            if (!Schema::hasColumn('pengaturan_aplikasi', 'jam_operasional')) {
                $table->text('jam_operasional')->nullable()->after('alamat');
            }
        });

        // Set default jam operasional jika masih null
        $defaultJam = "Senin - Jumat: 08.00 - 16.00 WIB\nSabtu: 08.00 - 14.00 WIB";
        DB::table('pengaturan_aplikasi')
            ->whereNull('jam_operasional')
            ->orWhere('jam_operasional', '')
            ->update(['jam_operasional' => $defaultJam]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_aplikasi', function (Blueprint $table) {
            if (Schema::hasColumn('pengaturan_aplikasi', 'jam_operasional')) {
                $table->dropColumn('jam_operasional');
            }
        });
    }
};
