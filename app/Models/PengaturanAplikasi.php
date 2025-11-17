<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanAplikasi extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_aplikasi';
    
    protected $fillable = [
        'nama_sekolah',
        'nama_aplikasi',
        'logo',
        'favicon',
        'email',
        'telepon',
        'no_hp',
        'alamat',
        'facebook',
        'instagram',
        'youtube',
        'tiktok',
        'meta_description',
        'meta_keywords',
        'maintenance_mode',
        'maintenance_message'
    ];

    protected $casts = [
        'maintenance_mode' => 'boolean'
    ];

    /**
     * Get application settings
     */
    public static function getSettings()
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = self::create([
                'nama_aplikasi' => 'PPDB SMK WI',
                'logo' => 'sneat/img/logowi.png',
                'favicon' => 'sneat/img/logowi.png',
                'email' => 'info@smkwi.sch.id',
                'telepon' => '+62 21 1234567',
                'alamat' => 'Jl. Contoh Alamat No. 123',
                'maintenance_mode' => false,
            ]);
        }
        
        return $settings;
    }

    /**
     * Accessor untuk logo - memastikan path lengkap
     */
    public function getLogoAttribute($value)
    {
        // Jika sudah path lengkap, return as is
        if (str_starts_with($value, 'storage/') || str_starts_with($value, 'http')) {
            return $value;
        }
        
        // Jika hanya nama file, tambahkan path default
        return $value ? 'storage/pengaturan/' . $value : 'sneat/img/logowi.png';
    }

    /**
     * Accessor untuk favicon - memastikan path lengkap
     */
    public function getFaviconAttribute($value)
    {
        // Jika sudah path lengkap, return as is
        if (str_starts_with($value, 'storage/') || str_starts_with($value, 'http')) {
            return $value;
        }
        
        // Jika hanya nama file, tambahkan path default
        return $value ? 'storage/pengaturan/' . $value : 'sneat/img/logowi.png';
    }
}