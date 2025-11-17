<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GelombangPendaftaran extends Model
{
    use HasFactory;

    protected $table = 'gelombang_pendaftaran';

    protected $fillable = [
        'tahun_ajaran_id',
        'nama_gelombang',
        'tanggal_mulai',
        'tanggal_selesai',
        'status'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        // 'status' => 'boolean'
    ];

    /**
     * Relasi ke tahun ajaran
     */
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id', 'id');
    }

    /**
     * Relasi ke kuota jurusan
     */
    public function kuotaJurusan()
    {
        return $this->hasMany(KuotaJurusan::class, 'gelombang_id');
    }

    /**
     * Relasi ke data siswa
     */
    public function dataSiswa()
    {
        return $this->hasMany(DataSiswa::class, 'gelombang_id');
    }

    /**
     * Scope untuk gelombang aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', true)
                    ->where('tanggal_mulai', '<=', now())
                    ->where('tanggal_selesai', '>=', now());
    }
}