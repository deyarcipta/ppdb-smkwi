<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('persyaratan_pendaftaran', function (Blueprint $table) {
            if (!Schema::hasColumn('persyaratan_pendaftaran', 'sub_konten')) {
                $table->text('sub_konten')->nullable()->after('konten');
            }
        });

        // Mengubah kolom tipe agar bisa menerima 'alur'
        DB::statement("ALTER TABLE persyaratan_pendaftaran MODIFY COLUMN tipe VARCHAR(50) NOT NULL DEFAULT 'umum'");
    }

    public function down()
    {
        Schema::table('persyaratan_pendaftaran', function (Blueprint $table) {
            if (Schema::hasColumn('persyaratan_pendaftaran', 'sub_konten')) {
                $table->dropColumn('sub_konten');
            }
        });
    }
};
