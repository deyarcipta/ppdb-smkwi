<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\PengaturanAplikasi;
use App\Models\WhatsAppSession;
use App\Models\WhatsappLog;

class WhatsappService
{
    /**
     * Send WhatsApp message using Open-WA API or default session
     */
    public function sendMessage($phone, $message, $jenisPesan = 'Notifikasi')
    {
        try {
            $pengaturan = PengaturanAplikasi::getSettings();

            // Cek apakah fitur WhatsApp diaktifkan
            $waStatus = isset($pengaturan->wa_status) ? (int)$pengaturan->wa_status : ($pengaturan->enable_whatsapp ? 1 : 0);
            if ($waStatus === 0) {
                Log::info("Pengiriman WhatsApp dilewati karena fitur Notifikasi WhatsApp dinonaktifkan di Pengaturan Aplikasi.");
                return [
                    'success' => true,
                    'message' => 'Notifikasi WhatsApp dinonaktifkan di Pengaturan Aplikasi.'
                ];
            }

            // Format nomor telepon
            $formattedPhone = $this->formatPhoneNumber($phone);
            
            Log::info("Mengirim WhatsApp ke: {$formattedPhone}");

            $baseUrl = ($pengaturan && $pengaturan->wa_api_url) ? $pengaturan->wa_api_url : env('OPEN_WA_API_URL', 'http://localhost:2785/api');
            $apiKey = ($pengaturan && $pengaturan->wa_api_key) ? $pengaturan->wa_api_key : env('OPEN_WA_API_KEY');

            // Ambil sesi aktif yang berstatus CONNECTED
            $session = WhatsAppSession::where('is_active', true)
                ->where('status', 'CONNECTED')
                ->inRandomOrder()
                ->first();

            if (!$session) {
                $sessionId = ($pengaturan && $pengaturan->wa_session_id) ? $pengaturan->wa_session_id : env('OPEN_WA_SESSION_ID', 'default');
            } else {
                $sessionId = $session->session_id;
            }

            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ];

            if ($apiKey) {
                $headers['Authorization'] = 'Bearer ' . $apiKey;
                $headers['X-API-Key'] = $apiKey;
            }

            // Format chatId untuk WhatsApp
            $chatId = str_contains($formattedPhone, '@') ? $formattedPhone : $formattedPhone . '@c.us';

            // Coba kirim via Open-WA API REST endpoint
            $response = Http::withHeaders($headers)
                ->timeout(15)
                ->post("{$baseUrl}/sessions/{$sessionId}/messages/send-text", [
                    'chatId' => $chatId,
                    'text' => $message,
                ]);

            $statusLog = 'failed';
            $responseBody = $response->body();
            $result = $response->json();

            if ($response->successful()) {
                $statusLog = 'sent';
                Log::info("WhatsApp berhasil dikirim via OpenWA: " . json_encode($result));
            } else {
                Log::error("Gagal mengirim WhatsApp via OpenWA: {$responseBody}");
            }

            // Simpan log pengiriman WhatsApp jika model WhatsappLog ada
            try {
                WhatsappLog::create([
                    'no_hp' => $formattedPhone,
                    'pesan' => $message,
                    'jenis_pesan' => $jenisPesan,
                    'status' => $statusLog,
                    'response' => $responseBody
                ]);
            } catch (\Exception $logEx) {
                // Abaikan error penulisan log
            }

            return [
                'success' => $response->successful(),
                'data' => $result,
                'response' => $responseBody
            ];

        } catch (\Exception $e) {
            Log::error('Gagal mengirim WhatsApp: ' . $e->getMessage());

            try {
                WhatsappLog::create([
                    'no_hp' => $phone,
                    'pesan' => $message,
                    'jenis_pesan' => $jenisPesan,
                    'status' => 'failed',
                    'response' => $e->getMessage()
                ]);
            } catch (\Exception $logEx) {}

            return [
                'success' => false, 
                'error' => $e->getMessage(),
                'note' => 'Pastikan server Open-WA Gateway berjalan di API URL yang benar.'
            ];
        }
    }

    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        return $phone;
    }

    /**
     * Cek status OpenWA Gateway
     */
    public function checkStatus()
    {
        try {
            $pengaturan = PengaturanAplikasi::getSettings();
            $baseUrl = ($pengaturan && $pengaturan->wa_api_url) ? $pengaturan->wa_api_url : env('OPEN_WA_API_URL', 'http://localhost:2785/api');
            $apiKey = ($pengaturan && $pengaturan->wa_api_key) ? $pengaturan->wa_api_key : env('OPEN_WA_API_KEY');

            $headers = ['Accept' => 'application/json'];
            if ($apiKey) {
                $headers['Authorization'] = 'Bearer ' . $apiKey;
                $headers['X-API-Key'] = $apiKey;
            }

            $response = Http::withHeaders($headers)->timeout(5)->get("{$baseUrl}/sessions");
            return [
                'status' => $response->successful() ? 'online' : 'error',
                'data' => $response->json()
            ];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'message' => $e->getMessage()];
        }
    }
}