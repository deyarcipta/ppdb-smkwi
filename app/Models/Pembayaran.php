<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'user_id',
        'no_pendaftaran',
        'nama_siswa',
        'jenis_pembayaran',
        'jumlah',
        'metode_pembayaran',
        'bukti_pembayaran',
        'tanggal_bayar',
        'status',
        'catatan',
        'verified_by',
        'verified_at'
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'verified_at' => 'datetime',
        'jumlah' => 'decimal:2'
    ];

    // Relasi ke user (siswa)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke admin yang memverifikasi
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Relasi ke data siswa
    public function dataSiswa()
    {
        return $this->hasOne(DataSiswa::class, 'user_id', 'user_id');
    }

    // Scope untuk filter status
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'diverifikasi');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    /**
     * Accessor untuk status_label
     * Mengubah nilai status dari database menjadi label yang lebih readable
     */
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'pending' => 'Menunggu Verifikasi',
            'diverifikasi' => 'Terverifikasi', 
            'ditolak' => 'Ditolak'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Accessor untuk status_badge_class
     * Memberikan class CSS berdasarkan status
     */
    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            'pending' => 'bg-warning text-dark',
            'diverifikasi' => 'bg-success text-white',
            'ditolak' => 'bg-danger text-white'
        ];

        return $classes[$this->status] ?? 'bg-secondary text-white';
    }
}