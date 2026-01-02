<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Models\WhatsappLog;

class WhatsAppService
{
    private $client;
    
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost:3000', // Localhost untuk Windows
            'timeout'  => 15.0, // Timeout lebih lama untuk Windows
            'verify' => false, // Nonaktifkan SSL verify untuk localhost
        ]);
    }

    public function sendMessage($phone, $message, $jenisPesan)
    {
        try {
            // Format nomor telepon
            $formattedPhone = $this->formatPhoneNumber($phone);
            
            Log::info("Mengirim WhatsApp ke: {$formattedPhone}");
            
            $response = $this->client->post('/send-message', [
                'json' => [
                    'phone' => $formattedPhone,
                    'message' => $message,
                    'jenis_pesan' => $jenisPesan,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]);
            // ✅ SIMPAN LOG BERHASIL
            WhatsappLog::create([
                'nomor_tujuan' => $phone,
                'jenis_pesan'  => $jenisPesan,
                'isi_pesan'    => $message,
                'status'       => 'sent',
                'sent_at'      => now(),
            ]);
            
            $result = json_decode($response->getBody(), true);
            Log::info("WhatsApp berhasil dikirim: " . json_encode($result));
            
            return $result;
            
        } catch (\Exception $e) {
             // ❌ SIMPAN LOG GAGAL
            WhatsappLog::create([
                'nomor_tujuan'  => $phone,
                'jenis_pesan'   => $jenisPesan,
                'isi_pesan'     => $message,
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            Log::error('Gagal mengirim WhatsApp: ' . $e->getMessage());
            return [
                'success' => false, 
                'error' => $e->getMessage(),
                'note' => 'Pastikan WhatsApp bot sedang running di localhost:3000'
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
     * Cek status WhatsApp bot
     */
    public function checkStatus()
    {
        try {
            $response = $this->client->get('/health');
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}