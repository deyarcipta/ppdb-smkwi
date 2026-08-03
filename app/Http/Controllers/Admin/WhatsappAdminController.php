<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\WhatsappLog;
use App\Models\PengaturanAplikasi;
use App\Models\WhatsAppSession;
use App\Services\WhatsappService;

class WhatsappAdminController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function index()
    {
        $logs = WhatsappLog::latest()->limit(20)->get();
        $pengaturan = PengaturanAplikasi::getSettings();
        $waSessions = WhatsAppSession::all();
        return view('admin.whatsapp.index', compact('logs', 'pengaturan', 'waSessions'));
    }

    /**
     * Simpan pengaturan OpenWA API URL & API Key
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'wa_api_url' => 'nullable|url',
            'wa_api_key' => 'nullable|string',
        ]);

        $pengaturan = PengaturanAplikasi::getSettings();
        $pengaturan->update([
            'wa_api_url' => $request->wa_api_url,
            'wa_api_key' => $request->wa_api_key,
        ]);

        return redirect()->route('whatsapp.index')
            ->with('success', 'Konfigurasi Open-WA API Gateway berhasil diperbarui.');
    }

    public function status()
    {
        try {
            $setting = PengaturanAplikasi::getSettings();
            $baseUrl = ($setting && $setting->wa_api_url) ? $setting->wa_api_url : env('OPEN_WA_API_URL', 'http://localhost:2785/api');
            $apiKey = ($setting && $setting->wa_api_key) ? $setting->wa_api_key : env('OPEN_WA_API_KEY');

            $headers = ['Accept' => 'application/json'];
            if ($apiKey) {
                $headers['Authorization'] = 'Bearer ' . $apiKey;
                $headers['X-API-Key'] = $apiKey;
            }

            $response = Http::withHeaders($headers)->timeout(5)->get("{$baseUrl}/sessions");

            if ($response->successful()) {
                return response()->json([
                    'status' => 'connected',
                    'message' => 'Server Open-WA Gateway Online',
                    'sessions' => $response->json()
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Server Open-WA Gateway merespon dengan status: ' . $response->status()
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'offline',
                'message' => 'Server Open-WA Gateway Offline: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==========================================
    // WHATSAPP OPEN-WA SESSION MANAGEMENT
    // ==========================================

    public function listWhatsAppSessions()
    {
        return response()->json([
            'success' => true,
            'data' => WhatsAppSession::all()
        ]);
    }

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
            $waSessionName = (string) Str::uuid();

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

            $session = WhatsAppSession::create([
                'session_id' => $sessionId,
                'label' => $request->label,
                'status' => 'NOT_STARTED',
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sesi WhatsApp berhasil ditambahkan di server OpenWA. Silakan klik "Mulai Sesi" pada kartu untuk memunculkan QR Code.',
                'session' => $session
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungi OpenWA Server: ' . $e->getMessage()
            ], 500);
        }
    }

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
