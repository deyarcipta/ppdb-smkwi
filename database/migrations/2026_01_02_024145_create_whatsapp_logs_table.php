<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();

            $table->string('nomor_tujuan'); // nomor WA
            $table->string('jenis_pesan')->nullable(); // dari template_pesan
            $table->text('isi_pesan'); // isi pesan final

            $table->enum('status', ['sent', 'failed', 'pending'])->default('sent');
            $table->text('error_message')->nullable();

            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};
