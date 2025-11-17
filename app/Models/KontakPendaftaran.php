<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontakPendaftaran extends Model
{
    use HasFactory;

    protected $table = 'kontak_pendaftaran';
    
    protected $fillable = [
        'nama_kontak',
        'no_kontak',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    // Tambahkan scope aktif di sini
    public function scopeAktif($query)
    {
        return $query->where('status', true);
    }
}