<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfoPembayaran extends Model
{
    use HasFactory;

    protected $table = 'info_pembayaran';
    
    protected $fillable = [
        'nama_bank',
        'nomor_rekening',
        'atas_nama',
        'keterangan',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];
}