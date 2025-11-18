<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'admin_id',
        'action',
        'description',
        'model_type',
        'model_id',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relasi ke admin
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Scope untuk aktivitas terbaru
     */
    public function scopeTerbaru($query, $limit = 5)
    {
        return $query->with('admin')
                    ->latest()
                    ->limit($limit);
    }

    /**
     * Accessor untuk warna berdasarkan action
     */
    public function getColorAttribute()
    {
        return match($this->action) {
            'create' => 'success',
            'update' => 'info',
            'delete' => 'danger',
            'verify' => 'warning',
            'export' => 'primary',
            default => 'secondary'
        };
    }

    /**
     * Manual log untuk aksi khusus
     */
    public static function logManual($description, $action = 'info')
    {
        // Hanya log jika ada user yang login (admin)
        if (auth()->check()) {
            return self::create([
                'admin_id' => auth()->id(),
                'action' => $action,
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }
        
        return null;
    }
}