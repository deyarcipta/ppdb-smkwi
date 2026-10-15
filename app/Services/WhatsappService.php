<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\PengaturanAplikasi;
use App\Jobs\SendWhatsAppNotification;

class WhatsappService
{
    /**
     * Send WhatsApp message via Queue Worker (asynchronous) with SIAWI Anti-Ban architecture
     */
    public function sendMessage($phone, $message, $jenisPesan = 'Notifikasi', ?string $specificSessionId = null)
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

            // Masukkan pesan ke dalam antrean (Queue)
            SendWhatsAppNotification::dispatch($phone, $message, $jenisPesan, $specificSessionId);

            Log::info("Pesan WhatsApp untuk {$phone} berhasil dimasukkan ke Queue.");

            return [
                'success' => true,
                'message' => 'Pesan WhatsApp berhasil dimasukkan ke antrean pengiriman.'
            ];

        } catch (\Exception $e) {
            Log::error('Gagal memasukkan pesan WhatsApp ke antrean: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
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