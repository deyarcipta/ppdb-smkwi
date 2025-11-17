<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterBiaya extends Model
{
    use HasFactory;

    protected $table = 'master_biaya';

    protected $fillable = [
        'gelombang_id',
        'jenis_biaya',
        'nama_biaya',
        'total_biaya',
        'diskon',
        'keterangan',
        'status',
    ];

    public function gelombang()
    {
        return $this->belongsTo(GelombangPendaftaran::class, 'gelombang_id');
    }
}
