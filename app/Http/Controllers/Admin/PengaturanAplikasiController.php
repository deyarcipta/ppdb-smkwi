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
            'alamat' => 'nullable|string|max:500',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'maintenance_message' => 'nullable|string|max:500',
        ]);

        $pengaturan = PengaturanAplikasi::getSettings();
        $data = $request->except(['logo', 'favicon']);

        // Upload logo jika ada
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($pengaturan->logo && Storage::exists($pengaturan->logo)) {
                Storage::delete($pengaturan->logo);
            }

            $logo = $request->file('logo');
            $logoName = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
            
            // Simpan dengan path lengkap
            $logoPath = $logo->storeAs('public/pengaturan', $logoName);
            
            // Simpan path lengkap untuk akses public
            $data['logo'] = 'storage/pengaturan/' . $logoName;
        }

        // Upload favicon jika ada
        if ($request->hasFile('favicon')) {
            // Hapus favicon lama jika ada
            if ($pengaturan->favicon && Storage::exists($pengaturan->favicon)) {
                Storage::delete($pengaturan->favicon);
            }

            $favicon = $request->file('favicon');
            $faviconName = 'favicon_' . time() . '.' . $favicon->getClientOriginalExtension();
            
            // Simpan dengan path lengkap
            $faviconPath = $favicon->storeAs('public/pengaturan', $faviconName);
            
            // Simpan path lengkap untuk akses public
            $data['favicon'] = 'storage/pengaturan/' . $faviconName;
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