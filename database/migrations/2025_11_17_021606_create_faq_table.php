<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('faq', function (Blueprint $table) {
            $table->id();
            $table->text('pertanyaan');
            $table->text('jawaban');
            $table->integer('urutan')->default(1);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('faq');
    }
};