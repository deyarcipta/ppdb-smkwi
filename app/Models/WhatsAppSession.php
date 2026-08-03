<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppSession extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_sessions';

    protected $fillable = [
        'session_id',
        'label',
        'phone_number',
        'status',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
