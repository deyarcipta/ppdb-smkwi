<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_logs';

    protected $fillable = [
        'nomor_tujuan',
        'jenis_pesan',
        'isi_pesan',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'sent_at'    => 'datetime',
];
}
