<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestimoniAlumni extends Model
{
    use HasFactory;

    protected $table = 'testimoni_alumni';
    
    protected $fillable = [
        'headline',
        'nama_alumni',
        'jurusan',
        'pekerjaan',
        'testimoni',
        'foto',
        'status',
        'urutan'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    // Scope untuk data aktif
    public function scopeAktif($query)
    {
        return $query->where('status', true);
    }

    // Scope untuk urutan
    public function scopeUrutan($query)
    {
        return $query->orderBy('urutan', 'asc');
    }
}