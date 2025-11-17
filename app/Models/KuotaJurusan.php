<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KuotaJurusan extends Model
{
    use HasFactory;

    protected $table = 'kuota_jurusan';

    protected $fillable = [
        'jurusan_id',
        'gelombang_id',
        'kuota',
        'status'
    ];

    /**
     * Relasi ke jurusan
     */
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    /**
     * Relasi ke gelombang
     */
    public function gelombang()
    {
        return $this->belongsTo(GelombangPendaftaran::class, 'gelombang_id');
    }
}