<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('page_visited')->nullable();
            $table->string('session_id')->nullable();
            $table->date('visit_date');
            $table->timestamps();

            $table->index('visit_date');
            $table->index('session_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('visitors');
    }
};