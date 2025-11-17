<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersyaratanPendaftaran extends Model
{
    use HasFactory;

    protected $table = 'persyaratan_pendaftaran';
    
    protected $fillable = [
        'judul',
        'konten',
        'tipe',
        'urutan',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    // Scope untuk filter berdasarkan tipe
    public function scopeTipe($query, $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', true);
    }
}