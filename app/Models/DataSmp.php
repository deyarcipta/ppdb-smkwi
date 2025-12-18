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
}