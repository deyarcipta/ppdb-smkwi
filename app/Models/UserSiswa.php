<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserSiswa extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users_siswa';

    protected $fillable = [
        'username',
        'password',
        'email',
        'role',
        'status_akun',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Relasi ke data siswa - SESUAIKAN DENGAN FOREIGN KEY
     */
    public function dataSiswa()
    {
        return $this->hasOne(DataSiswa::class, 'user_id', 'id');
    }
    
    // Relationship ke Pembayaran
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'user_id', 'id');
    }
    
    // Pembayaran terbaru
    public function pembayaranTerbaru()
    {
        return $this->hasMany(Pembayaran::class, 'user_siswa_id', 'id')->latest();
    }

}