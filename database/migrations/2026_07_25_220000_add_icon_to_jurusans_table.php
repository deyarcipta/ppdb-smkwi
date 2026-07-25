<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('jurusans', function (Blueprint $table) {
            if (!Schema::hasColumn('jurusans', 'icon')) {
                $table->string('icon')->nullable()->after('deskripsi');
            }
        });
    }

    public function down()
    {
        Schema::table('jurusans', function (Blueprint $table) {
            if (Schema::hasColumn('jurusans', 'icon')) {
                $table->dropColumn('icon');
            }
        });
    }
};
