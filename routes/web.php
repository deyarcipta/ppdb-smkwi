<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PendaftaranSiswaController;
use App\Http\Controllers\Admin\DashboardController as DashboardAdminController;
use App\Http\Controllers\Admin\AuthController as AuthAdminController;
use App\Http\Controllers\Admin\TahunAjaranController as TahunAjaranAdminController;
use App\Http\Controllers\Admin\JurusanController as JurusanAdminController;
use App\Http\Controllers\Admin\GelombangPendaftaranController as GelombangPendaftaranAdminController;
use App\Http\Controllers\Admin\KuotaJurusanController as KuotaJurusanAdminController;
use App\Http\Controllers\Admin\TemplatePesanController as TemplatePesanAdminController;
use App\Http\Controllers\Admin\VerifikasiPendaftarController as VerifikasiPendaftarAdminController;
use App\Http\Controllers\Admin\DataTerverifikasiController as DataTerverifikasiAdminController;
use App\Http\Controllers\Admin\DataDiterimaController as DataDiterimaAdminController;
use App\Http\Controllers\Admin\DataDitolakController as DataDitolakAdminController;
use App\Http\Controllers\Admin\PembayaranController as PembayaranAdminController;
use App\Http\Controllers\Admin\VerifikasiPembayaranController as VerifikasiPembayaranAdminController;
use App\Http\Controllers\Admin\MasterBiayaController as MasterBiayaAdminController;
use App\Http\Controllers\Admin\LaporanPembayaranController as LaporanPembayaranAdminController;
use App\Http\Controllers\Admin\StatistikController as StatistikAdminController;
use App\Http\Controllers\Admin\KontakPendaftaranController as KontakPendaftaranAdminController;
use App\Http\Controllers\Admin\InfoPembayaranController as InfoPembayaranAdminController;
use App\Http\Controllers\Admin\PersyaratanPendaftaranController as PersyaratanPendaftaranAdminController;
use App\Http\Controllers\Admin\TestimoniAlumniController as TestimoniAlumniAdminController;
use App\Http\Controllers\Admin\FaqController as FaqAdminController;
use App\Http\Controllers\Admin\PengumumanController as PengumumanAdminController; 
use App\Http\Controllers\Admin\UserManagementController as UserManagementAdminController;
use App\Http\Controllers\Admin\PengaturanAplikasiController as PengaturanAplikasiAdminController;

use App\Http\Controllers\Siswa\SiswaAuthController as SiswaAuthController;
use App\Http\Controllers\Siswa\DashboardController as DashboardSiswaController;
use App\Http\Controllers\Siswa\SiswaFormController as SiswaFormController;
use App\Http\Controllers\Siswa\SiswaPembayaranController as SiswaPembayaranController;
use App\Http\Controllers\Siswa\SiswaPengumumanController as SiswaPengumumanController;

// ===== FRONTEND =====
Route::get('/', [HomeController::class, 'index'])->name('frontend.home');
Route::get('/pendaftaran', [PendaftaranSiswaController::class, 'showForm'])->name('frontend.pendaftaran');
Route::post('/pendaftaran', [PendaftaranSiswaController::class, 'store'])->name('pendaftaran.store');
Route::get('/check-nisn/{nisn}', [PendaftaranSiswaController::class, 'checkNisn'])->name('pendaftaran.check-nisn');
Route::get('/check-email/{email}', [PendaftaranSiswaController::class, 'checkEmail'])->name('pendaftaran.check-email');

// ===== ADMIN DASHBOARD =====
// Route untuk halaman login admin
Route::prefix('w1s4t4')->group(function () {
    Route::get('/', [AuthAdminController::class, 'showLoginForm'])->name('backend.login');
    Route::post('/login', [AuthAdminController::class, 'login'])->name('backend.login.submit');
    Route::post('/logout', [AuthAdminController::class, 'logout'])->name('backend.logout');

    // Dashboard hanya untuk admin
    Route::middleware(['auth', 'role:admin,superadmin'])->group(function () {

            // Dashboard
            Route::get('/dashboard', [DashboardAdminController::class, 'index'])
                ->name('admin.dashboard');

            // Tahun Ajaran
            Route::resource('tahun-ajaran', TahunAjaranAdminController::class)
                ->except(['show', 'create', 'edit']);
            Route::get('tahun-ajaran/{id}/aktifkan', [TahunAjaranAdminController::class, 'aktifkan'])
                ->name('tahun-ajaran.aktifkan');
            Route::get('tahun-ajaran/{id}/nonaktifkan', [TahunAjaranAdminController::class, 'nonaktifkan'])
                ->name('tahun-ajaran.nonaktifkan');

            // Jurusan
            Route::resource('jurusan', JurusanAdminController::class)
                ->except(['show', 'create', 'edit']);
            Route::get('jurusan/{id}/aktifkan', [JurusanAdminController::class, 'aktifkan'])
                ->name('jurusan.aktifkan');
            Route::get('jurusan/{id}/nonaktifkan', [JurusanAdminController::class, 'nonaktifkan'])
                ->name('jurusan.nonaktifkan');

            // Gelombang Pendaftaran
            Route::resource('gelombang', GelombangPendaftaranAdminController::class)
                ->except(['show', 'create', 'edit']);
            Route::get('gelombang/{id}/aktifkan', [GelombangPendaftaranAdminController::class, 'aktifkan'])
                ->name('gelombang.aktifkan');
            Route::get('gelombang/{id}/nonaktifkan', [GelombangPendaftaranAdminController::class, 'nonaktifkan'])
                ->name('gelombang.nonaktifkan');

            // Kuota Jurusan
            Route::resource('kuota-jurusan', KuotaJurusanAdminController::class)
                ->except(['show', 'create', 'edit']);
            Route::get('kuota-jurusan/{id}/aktifkan', [KuotaJurusanAdminController::class, 'aktifkan'])
                ->name('kuota-jurusan.aktifkan');
            Route::get('kuota-jurusan/{id}/nonaktifkan', [KuotaJurusanAdminController::class, 'nonaktifkan'])
                ->name('kuota-jurusan.nonaktifkan');

            // Kuota Template Pesan
            Route::resource('template-pesan', TemplatePesanAdminController::class)
                ->except(['show', 'create', 'edit']);
            Route::get('template-pesan/{id}/aktifkan', [TemplatePesanAdminController::class, 'aktifkan'])
                ->name('template-pesan.aktifkan');
            Route::get('template-pesan/{id}/nonaktifkan', [TemplatePesanAdminController::class, 'nonaktifkan'])
                ->name('template-pesan.nonaktifkan');
            
            // Verifikasi Pendaftar
            Route::get('verifikasi-pendaftar', [VerifikasiPendaftarAdminController::class, 'index'])->name('verifikasi-pendaftar.index');
            Route::post('verifikasi-pendaftar/update', [VerifikasiPendaftarAdminController::class, 'update'])->name('verifikasi-pendaftar.update');
            Route::delete('verifikasi-pendaftar/{id}', [VerifikasiPendaftarAdminController::class, 'destroy'])->name('verifikasi-pendaftar.destroy');

            // Data Diterima
            Route::get('data-diterima', [DataDiterimaAdminController::class, 'index'])->name('data-diterima.index');
            Route::delete('data-diterima/{id}', [DataDiterimaAdminController::class, 'destroy'])->name('data-diterima.destroy');
            Route::post('data-diterima/update', [DataDiterimaAdminController::class, 'update'])->name('data-diterima.update');
            Route::get('data-diterima/export', [DataDiterimaAdminController::class, 'export'])->name('data-diterima.export');

            // Data Diterima
            Route::get('data-ditolak', [DataDitolakAdminController::class, 'index'])->name('data-ditolak.index');
            Route::delete('data-ditolak/{id}', [DataDitolakAdminController::class, 'destroy'])->name('data-ditolak.destroy');
            Route::post('data-ditolak/update', [DataDitolakAdminController::class, 'update'])->name('data-ditolak.update');
            Route::get('data-ditolak/export', [DataDitolakAdminController::class, 'export'])->name('data-ditolak.export');

            // Data Terverifikasi
            Route::get('data-terverifikasi', [DataTerverifikasiAdminController::class, 'index'])->name('data-terverifikasi.index');
            Route::post('data-terverifikasi/update', [DataTerverifikasiAdminController::class, 'update'])->name('data-terverifikasi.update');
            Route::delete('data-terverifikasi/{id}', [DataTerverifikasiAdminController::class, 'destroy'])->name('data-terverifikasi.destroy');

            // Kuota Master Biaya
            Route::resource('master-biaya', MasterBiayaAdminController::class)
                ->except(['show', 'create', 'edit']);
            Route::get('master-biaya/{id}/aktifkan', [MasterBiayaAdminController::class, 'aktifkan'])
                ->name('master-biaya.aktifkan');
            Route::get('master-biaya/{id}/nonaktifkan', [MasterBiayaAdminController::class, 'nonaktifkan'])
                ->name('master-biaya.nonaktifkan');

            // Data Pembayaran
            Route::resource('pembayaran', PembayaranAdminController::class)
                ->except(['show', 'create', 'edit']);
            Route::post('pembayaran/{id}/verify', [PembayaranAdminController::class, 'verify'])
                ->name('pembayaran.verify');
            Route::get('pembayaran/{id}/download-bukti', [PembayaranAdminController::class, 'downloadBukti'])
                ->name('pembayaran.download-bukti');
            
            // Verifikasi Pembayaran
            Route::resource('verifikasi-pembayaran', VerifikasiPembayaranAdminController::class)
                ->except(['show', 'create', 'edit']);
            Route::post('verifikasi-pembayaran/{id}/verify', [VerifikasiPembayaranAdminController::class, 'verify'])
                ->name('verifikasi-pembayaran.verify');
            Route::get('verifikasi-pembayaran/{id}/download-bukti', [VerifikasiPembayaranAdminController::class, 'downloadBukti'])
                ->name('verifikasi-pembayaran.download-bukti');

            // Verifikasi Pembayaran
            Route::get('/laporan-pembayaran', [LaporanPembayaranAdminController::class, 'index'])->name('laporan-pembayaran.index');
            Route::get('/laporan-pembayaran/filter', [LaporanPembayaranAdminController::class, 'filter'])->name('laporan-pembayaran.filter');
            Route::get('/laporan-pembayaran/detail/{id}', [LaporanPembayaranAdminController::class, 'detail'])->name('laporan-pembayaran.detail');
            Route::get('/laporan-pembayaran/export', [LaporanPembayaranAdminController::class, 'exportExcel'])->name('laporan-pembayaran.export');

            // Route untuk menu statistik
            Route::get('/statistik', [StatistikAdminController::class, 'index'])->name('statistik.index');

            // Kuota Kontak Pendaftaran
            Route::resource('kontak-pendaftaran', KontakPendaftaranAdminController::class)
                ->except(['show', 'create', 'edit']);
            Route::get('kontak-pendaftaran/{id}/aktifkan', [KontakPendaftaranAdminController::class, 'aktifkan'])
                ->name('kontak-pendaftaran.aktifkan');
            Route::get('kontak-pendaftaran/{id}/nonaktifkan', [KontakPendaftaranAdminController::class, 'nonaktifkan'])
                ->name('kontak-pendaftaran.nonaktifkan');

            //Info Pembayaran
            Route::resource('info-pembayaran', InfoPembayaranAdminController::class)
                ->except(['show', 'create', 'edit']);
            Route::get('info-pembayaran/{id}/aktifkan', [InfoPembayaranAdminController::class, 'aktifkan'])
                ->name('info-pembayaran.aktifkan');
            Route::get('info-pembayaran/{id}/nonaktifkan', [InfoPembayaranAdminController::class, 'nonaktifkan'])
                ->name('info-pembayaran.nonaktifkan');

            //Info Persyaratan Pendaftaran
            Route::resource('persyaratan-pendaftaran', PersyaratanPendaftaranAdminController::class)
                ->except(['show', 'create', 'edit']);
            Route::get('persyaratan-pendaftaran/{id}/aktifkan', [PersyaratanPendaftaranAdminController::class, 'aktifkan'])
                ->name('persyaratan-pendaftaran.aktifkan');
            Route::get('persyaratan-pendaftaran/{id}/nonaktifkan', [PersyaratanPendaftaranAdminController::class, 'nonaktifkan'])
                ->name('persyaratan-pendaftaran.nonaktifkan');

            //Info Testimoni Alumni
            Route::resource('testimoni-alumni', 'App\Http\Controllers\Admin\TestimoniAlumniController')
                ->except(['show', 'create', 'edit']);
            Route::get('testimoni-alumni/{id}/aktifkan', ['App\Http\Controllers\Admin\TestimoniAlumniController', 'aktifkan'])
                ->name('testimoni-alumni.aktifkan');
            Route::get('testimoni-alumni/{id}/nonaktifkan', ['App\Http\Controllers\Admin\TestimoniAlumniController', 'nonaktifkan'])
                ->name('testimoni-alumni.nonaktifkan');

            // Info FAQ
            Route::resource('faq', FaqAdminController::class)
                ->except(['show', 'create', 'edit']);
            Route::get('faq/{id}/aktifkan', [FaqAdminController::class, 'aktifkan'])
                ->name('faq.aktifkan');
            Route::get('faq/{id}/nonaktifkan', [FaqAdminController::class, 'nonaktifkan'])
                ->name('faq.nonaktifkan');

            // Info Pengumuman
            Route::resource('pengumuman', PengumumanAdminController::class)
                ->except(['show', 'create', 'edit']);
            Route::get('pengumuman/{id}/aktifkan', [PengumumanAdminController::class, 'aktifkan'])
                ->name('pengumuman.aktifkan');
            Route::get('pengumuman/{id}/nonaktifkan', [PengumumanAdminController::class, 'nonaktifkan'])
                ->name('pengumuman.nonaktifkan');
            
            // User Management - Hanya untuk superadmin
            Route::middleware(['role:superadmin'])->group(function () {
                Route::resource('user-management', UserManagementAdminController::class)
                    ->except(['show', 'create', 'edit']);
                Route::get('user-management/{id}/aktifkan', [UserManagementAdminController::class, 'aktifkan'])
                    ->name('user-management.aktifkan');
                Route::get('user-management/{id}/nonaktifkan', [UserManagementAdminController::class, 'nonaktifkan'])
                    ->name('user-management.nonaktifkan');
                
                Route::get('pengaturan-aplikasi', [PengaturanAplikasiAdminController::class, 'index'])
                    ->name('pengaturan-aplikasi.index');
                Route::put('pengaturan-aplikasi', [PengaturanAplikasiAdminController::class, 'update'])
                    ->name('pengaturan-aplikasi.update');
                Route::post('pengaturan-aplikasi/toggle-maintenance', [PengaturanAplikasiAdminController::class, 'toggleMaintenance'])
                    ->name('pengaturan-aplikasi.toggle-maintenance');
            });



        });
});

// ===== ROUTE SISWA =====
Route::prefix('siswa')->group(function () {
    Route::get('/', [SiswaAuthController::class, 'showLoginForm'])->name('siswa.login');
    Route::post('/login', [SiswaAuthController::class, 'login'])->name('siswa.login.submit');
    Route::post('/logout', [SiswaAuthController::class, 'logout'])->name('siswa.logout');

    Route::middleware(['auth:siswa'])->group(function () {
        Route::get('/dashboard', [DashboardSiswaController::class, 'index'])->name('siswa.dashboard');

        // Upload Bukti Pembayaran Formulir
        Route::post('/upload-bukti-formulir', [DashboardSiswaController::class, 'uploadBuktiFormulir'])->name('siswa.upload-bukti-formulir');

        // Routes untuk jurusan
        Route::get('/jurusan', [DashboardSiswaController::class, 'getJurusan'])->name('siswa.get-jurusan');
        Route::post('/pilih-jurusan', [DashboardSiswaController::class, 'pilihJurusan'])->name('siswa.pilih-jurusan');

        Route::get('/create', [SiswaFormController::class, 'create'])->name('siswa.formulir');
        Route::post('/store', [SiswaFormController::class, 'store'])->name('siswa.formulir.store');

         // Routes Pembayaran - Semua dalam satu halaman index dengan modal
        Route::get('/pembayaran', [SiswaPembayaranController::class, 'index'])->name('siswa.pembayaran.index');
        Route::post('/pembayaran', [SiswaPembayaranController::class, 'store'])->name('siswa.pembayaran.store');
        Route::put('/pembayaran/{id}', [SiswaPembayaranController::class, 'update'])->name('siswa.pembayaran.update');
        Route::delete('/pembayaran/{id}', [SiswaPembayaranController::class, 'destroy'])->name('siswa.pembayaran.destroy');

        // Routes Pengumuman
        Route::get('/pengumuman', [SiswaPengumumanController::class, 'index'])->name('siswa.pengumuman.index');
        Route::get('/pengumuman/{id}', [SiswaPengumumanController::class, 'show'])->name('siswa.pengumuman.show');
        Route::get('/pengumuman/search', [SiswaPengumumanController::class, 'search'])->name('siswa.pengumuman.search');
        
    });
});
