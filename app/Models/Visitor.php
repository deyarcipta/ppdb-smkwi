<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Visitor extends Model
{
    use HasFactory;

    protected $table = 'visitors';
    
    protected $fillable = [
        'ip_address',
        'user_agent',
        'page_visited',
        'session_id',
        'visit_date',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    // Scope untuk hari ini
    public function scopeHariIni($query)
    {
        return $query->whereDate('visit_date', Carbon::today());
    }

    // Scope untuk bulan ini
    public function scopeBulanIni($query)
    {
        return $query->whereYear('visit_date', Carbon::now()->year)
                    ->whereMonth('visit_date', Carbon::now()->month);
    }

    // Scope untuk halaman tertentu
    public function scopeHalamanIni($query, $page = '/')
    {
        return $query->where('page_visited', $page);
    }

    // Scope untuk unique visitors (berdasarkan session_id)
    public function scopeUniqueVisitors($query)
    {
        return $query->distinct('session_id');
    }
}