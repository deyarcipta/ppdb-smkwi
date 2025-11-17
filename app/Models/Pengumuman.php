<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';
    
    protected $fillable = [
        'judul',
        'isi',
        'gambar',
        'tanggal',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'tanggal' => 'date'
    ];

    // Scope untuk data aktif
    public function scopeAktif($query)
    {
        return $query->where('status', true);
    }

    // Scope untuk data terbaru
    public function scopeTerbaru($query)
    {
        return $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc');
    }
}