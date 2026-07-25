<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanAplikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanAplikasiController extends Controller
{
    public function index()
    {
        $pengaturan = PengaturanAplikasi::getSettings();
        return view('admin.pengaturan-aplikasi.index', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'nama_aplikasi' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:1024',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:20',
            'no_hp' => 'nullable|string|max:20',
            'no_hp_admin' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'maintenance_message' => 'nullable|string|max:500',
            'enable_cetak_kartu' => 'required|boolean',
            'enable_whatsapp' => 'nullable|boolean',
            'kartu_username_contoh' => 'nullable|string|max:255',
            'kartu_password_contoh' => 'nullable|string|max:255',
            'ttd_stempel' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'hero_bg' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'warna_utama' => 'nullable|string|max:10',
            'warna_sekunder' => 'nullable|string|max:10',
            'warna_header' => 'nullable|string|max:10',
        ]);

        $pengaturan = PengaturanAplikasi::getSettings();
        $data = $request->except(['logo', 'favicon', 'ttd_stempel', 'hero_bg']);
        $data['enable_whatsapp'] = $request->has('enable_whatsapp') ? (bool)$request->enable_whatsapp : false;
        $data['no_hp_admin'] = '-';

        // Upload logo jika ada
        if ($request->hasFile('logo')) {
            if ($pengaturan->logo && Storage::exists($pengaturan->logo)) {
                Storage::delete($pengaturan->logo);
            }

            $logo = $request->file('logo');
            $logoName = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
            $logo->storeAs('public/pengaturan', $logoName);
            $data['logo'] = 'storage/pengaturan/' . $logoName;
        }

        // Upload favicon jika ada
        if ($request->hasFile('favicon')) {
            if ($pengaturan->favicon && Storage::exists($pengaturan->favicon)) {
                Storage::delete($pengaturan->favicon);
            }

            $favicon = $request->file('favicon');
            $faviconName = 'favicon_' . time() . '.' . $favicon->getClientOriginalExtension();
            $favicon->storeAs('public/pengaturan', $faviconName);
            $data['favicon'] = 'storage/pengaturan/' . $faviconName;
        }

        // Upload TTD & Stempel jika ada
        if ($request->hasFile('ttd_stempel')) {
            if ($pengaturan->ttd_stempel && Storage::exists($pengaturan->ttd_stempel)) {
                Storage::delete($pengaturan->ttd_stempel);
            }

            $ttdFile = $request->file('ttd_stempel');
            $ttdName = 'ttd_stempel_' . time() . '.' . $ttdFile->getClientOriginalExtension();
            $ttdFile->storeAs('public/pengaturan', $ttdName);
            $data['ttd_stempel'] = 'storage/pengaturan/' . $ttdName;
        }

        // Upload Hero Background jika ada
        if ($request->hasFile('hero_bg')) {
            if ($pengaturan->hero_bg && Storage::exists($pengaturan->hero_bg)) {
                Storage::delete($pengaturan->hero_bg);
            }

            $heroFile = $request->file('hero_bg');
            $heroName = 'hero_bg_' . time() . '.' . $heroFile->getClientOriginalExtension();
            $heroFile->storeAs('public/pengaturan', $heroName);
            $data['hero_bg'] = 'storage/pengaturan/' . $heroName;
        }

        $pengaturan->update($data);

        return redirect()->route('pengaturan-aplikasi.index')
            ->with('success', 'Pengaturan aplikasi berhasil diperbarui.');
    }

    public function toggleMaintenance(Request $request)
    {
        $pengaturan = PengaturanAplikasi::getSettings();
        $pengaturan->update([
            'maintenance_mode' => !$pengaturan->maintenance_mode
        ]);

        $status = $pengaturan->maintenance_mode ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('pengaturan-aplikasi.index')
            ->with('success', "Maintenance mode berhasil $status.");
    }
}