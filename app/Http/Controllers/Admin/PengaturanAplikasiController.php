<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanAplikasi;
use App\Models\WhatsAppSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PengaturanAplikasiController extends Controller
{
    public function index()
    {
        $pengaturan = PengaturanAplikasi::getSettings();
        $waSessions = WhatsAppSession::all();
        return view('admin.pengaturan-aplikasi.index', compact('pengaturan', 'waSessions'));
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
            'wa_status' => 'nullable|in:0,1',
            'wa_api_url' => 'nullable|url',
            'wa_api_key' => 'nullable|string',
            'wa_session_id' => 'nullable|string',
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

        if ($request->has('wa_status')) {
            $data['wa_status'] = (int)$request->wa_status;
            $data['enable_whatsapp'] = ($request->wa_status == 1);
        } else {
            $data['enable_whatsapp'] = $request->has('enable_whatsapp') ? (bool)$request->enable_whatsapp : false;
        }

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

    // ==========================================
    // WHATSAPP OPEN-WA SESSION MANAGEMENT
    // ==========================================

    /**
     * Mengambil daftar semua sesi WhatsApp.
     */
    public function listWhatsAppSessions()
    {
        return response()->json([
            'success' => true,
            'data' => WhatsAppSession::all()
        ]);
    }

    /**
     * Menambahkan sesi WhatsApp baru.
     */
    public function addWhatsAppSession(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
        ]);

        $setting = PengaturanAplikasi::getSettings();
        $baseUrl = ($setting && $setting->wa_api_url) ? $setting->wa_api_url : env('OPEN_WA_API_URL', 'http://localhost:2785/api');
        $apiKey = ($setting && $setting->wa_api_key) ? $setting->wa_api_key : env('OPEN_WA_API_KEY');

        $headers = [
            'Content-Type' => 'application/json',
        ];

        if ($apiKey) {
            $headers['Authorization'] = 'Bearer ' . $apiKey;
            $headers['X-API-Key'] = $apiKey;
        }

        try {
            // Generate UUID unik untuk OpenWA session name
            $waSessionName = (string) Str::uuid();

            // Daftarkan/Buat sesi baru terlebih dahulu di server OpenWA
            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->post("{$baseUrl}/sessions", [
                    'name' => $waSessionName,
                ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mendaftarkan sesi di server OpenWA: ' . $response->body()
                ], 400);
            }

            $responseData = $response->json();
            $sessionId = $responseData['id'] ?? null;

            if (!$sessionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mendapatkan ID Sesi dari server OpenWA.'
                ], 500);
            }

            // Simpan sesi ke database aplikasi
            $session = WhatsAppSession::create([
                'session_id' => $sessionId,
                'label' => $request->label,
                'status' => 'NOT_STARTED',
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sesi WhatsApp berhasil ditambahkan di server OpenWA. Silakan klik tombol "Mulai Sesi" pada kartu untuk memunculkan QR Code.',
                'session' => $session
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungi OpenWA Server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengaktifkan/menonaktifkan sesi WhatsApp secara manual.
     */
    public function toggleWhatsAppSession($id)
    {
        $session = WhatsAppSession::findOrFail($id);
        $session->is_active = !$session->is_active;
        $session->save();

        return response()->json([
            'success' => true,
            'message' => 'Status aktif sesi berhasil diubah.',
            'is_active' => $session->is_active
        ]);
    }

    /**
     * Menghapus sesi WhatsApp dari database dan logout dari server OpenWA.
     */
    public function deleteWhatsAppSession($id)
    {
        $session = WhatsAppSession::findOrFail($id);
        
        $setting = PengaturanAplikasi::getSettings();
        $baseUrl = ($setting && $setting->wa_api_url) ? $setting->wa_api_url : env('OPEN_WA_API_URL', 'http://localhost:2785/api');
        $apiKey = ($setting && $setting->wa_api_key) ? $setting->wa_api_key : env('OPEN_WA_API_KEY');

        $headers = [
            'Content-Type' => 'application/json',
        ];
        if ($apiKey) {
            $headers['Authorization'] = 'Bearer ' . $apiKey;
            $headers['X-API-Key'] = $apiKey;
        }

        try {
            // Hapus sesi secara permanen dari server OpenWA
            Http::withHeaders($headers)
                ->timeout(5)
                ->delete("{$baseUrl}/sessions/{$session->session_id}");
        } catch (\Exception $e) {
            // Abaikan jika server OpenWA tidak terjangkau
        }

        $session->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesi WhatsApp berhasil dihapus.'
        ]);
    }

    /**
     * Memeriksa status sesi WhatsApp spesifik di server OpenWA.
     */
    public function getWhatsAppSessionStatus($id)
    {
        $session = WhatsAppSession::findOrFail($id);
        $setting = PengaturanAplikasi::getSettings();
        $baseUrl = ($setting && $setting->wa_api_url) ? $setting->wa_api_url : env('OPEN_WA_API_URL', 'http://localhost:2785/api');
        $apiKey = ($setting && $setting->wa_api_key) ? $setting->wa_api_key : env('OPEN_WA_API_KEY');

        $headers = [
            'Content-Type' => 'application/json',
        ];
        if ($apiKey) {
            $headers['Authorization'] = 'Bearer ' . $apiKey;
            $headers['X-API-Key'] = $apiKey;
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout(5)
                ->get("{$baseUrl}/sessions/{$session->session_id}");

            $qrCode = null;
            $status = 'UNKNOWN';
            $message = '';
            $connected = false;
            $phoneNumber = $session->phone_number;

            if ($response->successful()) {
                $data = $response->json();
                $rawStatus = $data['status'] ?? 'CONNECTED';
                
                if ($rawStatus === 'created' || $rawStatus === 'NOT_STARTED') {
                    $status = 'NOT_STARTED';
                } else if ($rawStatus === 'ready' || $rawStatus === 'connected' || $rawStatus === 'WORKING') {
                    $status = 'CONNECTED';
                } else {
                    $status = $rawStatus;
                }
                
                $connected = ($status === 'CONNECTED');
                $message = $connected ? 'WhatsApp Terhubung!' : 'Sesi aktif tetapi belum terhubung (Status: ' . $status . ')';

                $phoneNumber = $data['phone'] ?? (isset($data['me']['id']) ? explode('@', $data['me']['id'])[0] : null);
                if ($phoneNumber) {
                    $session->update(['phone_number' => $phoneNumber]);
                }

                if (!$connected) {
                    $qrResponse = Http::withHeaders($headers)
                        ->timeout(5)
                        ->get("{$baseUrl}/sessions/{$session->session_id}/qr");
                    if ($qrResponse->successful()) {
                        $qrData = $qrResponse->json();
                        $qrCode = $qrData['qrCode'] ?? ($qrData['qr'] ?? null);
                    }
                }
            } else {
                $statusCode = $response->status();
                if ($statusCode === 404) {
                    $status = 'NOT_STARTED';
                    $message = 'Sesi belum terdaftar/dimulai di server OpenWA.';
                } else if ($statusCode === 409) {
                    $status = 'NOT_READY';
                    $message = 'WhatsApp belum terhubung (Conflict 409).';
                    
                    $qrResponse = Http::withHeaders($headers)
                        ->timeout(5)
                        ->get("{$baseUrl}/sessions/{$session->session_id}/qr");
                    if ($qrResponse->successful()) {
                        $qrData = $qrResponse->json();
                        $qrCode = $qrData['qrCode'] ?? ($qrData['qr'] ?? null);
                    }
                } else {
                    $status = 'ERROR';
                    $message = 'Server OpenWA merespon dengan status: ' . $statusCode;
                }
            }

            $session->update(['status' => $status]);

            return response()->json([
                'success' => true,
                'connected' => $connected,
                'status' => $status,
                'message' => $message,
                'qrCode' => $qrCode,
                'phone_number' => $phoneNumber,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungi OpenWA Server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memulai/menginisialisasi sesi WhatsApp spesifik di server OpenWA.
     */
    public function startWhatsAppSessionSpec($id)
    {
        $session = WhatsAppSession::findOrFail($id);
        $setting = PengaturanAplikasi::getSettings();
        $baseUrl = ($setting && $setting->wa_api_url) ? $setting->wa_api_url : env('OPEN_WA_API_URL', 'http://localhost:2785/api');
        $apiKey = ($setting && $setting->wa_api_key) ? $setting->wa_api_key : env('OPEN_WA_API_KEY');

        $headers = [
            'Content-Type' => 'application/json',
        ];
        if ($apiKey) {
            $headers['Authorization'] = 'Bearer ' . $apiKey;
            $headers['X-API-Key'] = $apiKey;
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->post("{$baseUrl}/sessions/{$session->session_id}/start");

            if ($response->successful()) {
                $session->update(['status' => 'STARTING']);
                return response()->json([
                    'success' => true,
                    'message' => 'Sesi berhasil dimulai. Silakan tunggu beberapa saat dan klik "Cek Koneksi".'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memulai sesi: ' . $response->body()
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungi OpenWA Server: ' . $e->getMessage()
            ], 500);
        }
    }
}