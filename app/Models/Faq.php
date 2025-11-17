<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $table = 'faq';
    
    protected $fillable = [
        'pertanyaan',
        'jawaban',
        'urutan',
        'status'
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