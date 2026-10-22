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
        'tipe_aplikasi',
        'tahun_ajaran_id',
        'logo',
        'favicon',
        'email',
        'telepon',
        'no_hp',
        'no_hp_admin',
        'alamat',
        'jam_operasional',
        'facebook',
        'instagram',
        'youtube',
        'tiktok',
        'meta_description',
        'meta_keywords',
        'maintenance_mode',
        'maintenance_message',
        'enable_cetak_kartu',
        'enable_whatsapp',
        'kartu_username_contoh',
        'kartu_password_contoh',
        'ttd_stempel',
        'hero_bg',
        'warna_utama',
        'warna_sekunder',
        'warna_header',
        'warna_teks_hero',
        'warna_motto_hero',
        'wa_status',
        'wa_api_url',
        'wa_api_key',
        'wa_session_id',
        'wa_load_balancing',
    ];

    protected $casts = [
        'maintenance_mode' => 'boolean',
        'enable_cetak_kartu' => 'boolean',
        'enable_whatsapp' => 'boolean',
        'wa_load_balancing' => 'boolean',
    ];

    /**
     * Relasi ke Tahun Ajaran aktif yang dikonfigurasikan
     */
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    /**
     * Helper untuk mendapatkan tipe aplikasi (default: PPDB)
     */
    public function getTipeAplikasiAttribute($value)
    {
        return $value ?: 'PPDB';
    }

    /**
     * Helper untuk mendapatkan nama panjang tipe aplikasi
     */
    public function getNamaPanjangAplikasiAttribute()
    {
        return ($this->tipe_aplikasi === 'SPMB') 
            ? 'Sistem Penerimaan Murid Baru' 
            : 'Penerimaan Peserta Didik Baru';
    }

    /**
     * Helper untuk mendapatkan model Tahun Ajaran aktif yang tersinkron
     */
    public function getTahunAjaranAktifAttribute()
    {
        if ($this->tahun_ajaran_id && $this->tahunAjaran) {
            return $this->tahunAjaran;
        }
        return TahunAjaran::where('status', 'aktif')->first();
    }

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
                'enable_cetak_kartu' => true,
                'enable_whatsapp' => false,
                'kartu_username_contoh' => '[Username Anda]',
                'kartu_password_contoh' => 'password123',
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