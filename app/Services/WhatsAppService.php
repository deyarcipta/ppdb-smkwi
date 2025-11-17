<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

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

    public function sendMessage($phone, $message)
    {
        try {
            // Format nomor telepon
            $formattedPhone = $this->formatPhoneNumber($phone);
            
            Log::info("Mengirim WhatsApp ke: {$formattedPhone}");
            
            $response = $this->client->post('/send-message', [
                'json' => [
                    'phone' => $formattedPhone,
                    'message' => $message
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]);
            
            $result = json_decode($response->getBody(), true);
            Log::info("WhatsApp berhasil dikirim: " . json_encode($result));
            
            return $result;
            
        } catch (\Exception $e) {
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