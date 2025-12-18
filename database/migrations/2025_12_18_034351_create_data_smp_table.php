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
        Schema::create('data_smp', function (Blueprint $table) {
            $table->id('id_smp');
            $table->string('nama_smp', 200);
            $table->timestamps();

            // Index
            $table->index('nama_smp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_smp');
    }
};
