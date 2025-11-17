<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('testimoni_alumni', function (Blueprint $table) {
            $table->id();
            $table->string('headline');
            $table->string('nama_alumni');
            $table->string('jurusan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->text('testimoni');
            $table->string('foto')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('testimoni_alumni');
    }
};