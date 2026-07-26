<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataSmp extends Model
{
    use HasFactory;

    protected $table = 'data_smp';
    protected $primaryKey = 'id_smp';
    
    protected $fillable = [
        'nama_smp',
    ];

    public function dataSiswa()
    {
        return $this->hasMany(DataSiswa::class, 'id_smp', 'id_smp');
    }

    /**
     * Hitung total siswa yang mendaftar dari SMP ini
     * Mencocokkan berdasarkan id_smp ATAU pencocokan teks asal_sekolah
     */
    public function getTotalSiswaCountAttribute()
    {
        return DataSiswa::where('id_smp', $this->id_smp)
            ->orWhere(function ($q) {
                $q->whereNotNull('asal_sekolah')
                  ->whereRaw('LOWER(TRIM(asal_sekolah)) = ?', [strtolower(trim($this->nama_smp))]);
            })->count();
    }
}