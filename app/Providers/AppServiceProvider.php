<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            static $pengaturan = null;
            if ($pengaturan === null) {
                try {
                    $pengaturan = \App\Models\PengaturanAplikasi::getSettings();
                } catch (\Throwable $e) {
                    $pengaturan = null;
                }
            }

            if ($pengaturan) {
                $viewData = $view->getData();
                $view->with([
                    'pengaturan' => $viewData['pengaturan'] ?? $pengaturan,
                    'tipeAplikasi' => $viewData['tipeAplikasi'] ?? ($pengaturan->tipe_aplikasi ?? 'PPDB'),
                    'namaAplikasi' => $viewData['namaAplikasi'] ?? ($pengaturan->nama_aplikasi ?? 'PPDB'),
                ]);
            }
        });
    }
}
