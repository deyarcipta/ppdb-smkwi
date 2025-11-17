<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GelombangPendaftaran;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'nama',
        'status',
    ];

    /**
     * Relasi ke gelombang pendaftaran
     */
    public function gelombangPendaftaran()
    {
        return $this->hasMany(GelombangPendaftaran::class, 'tahun_ajaran_id', 'id');
    }
}
