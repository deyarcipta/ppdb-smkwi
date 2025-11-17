<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusans';

    protected $fillable = [
        'kode_jurusan',
        'nama_jurusan',
        'deskripsi',
        'status'
    ];

    /**
     * Relasi ke kuota jurusan
     */
    public function kuota()
    {
        return $this->hasMany(KuotaJurusan::class, 'jurusan_id');
    }

    /**
     * Relasi ke data siswa
     */
    public function dataSiswa()
    {
        return $this->hasMany(DataSiswa::class, 'jurusan_id');
    }

    /**
     * Scope untuk jurusan aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', true);
    }
}