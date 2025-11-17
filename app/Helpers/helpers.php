<?php

use App\Models\PengaturanAplikasi;

if (!function_exists('pengaturan')) {
    function pengaturan($key = null, $default = null)
    {
        try {
            // Cek jika tabel exists
            if (!\Illuminate\Support\Facades\Schema::hasTable('pengaturan_aplikasi')) {
                return $default;
            }

            $pengaturan = PengaturanAplikasi::first();
            
            if (!$pengaturan) {
                return $default;
            }
            
            if ($key) {
                return $pengaturan->$key ?? $default;
            }
            
            return $pengaturan;
        } catch (\Exception $e) {
            return $default;
        }
    }
}