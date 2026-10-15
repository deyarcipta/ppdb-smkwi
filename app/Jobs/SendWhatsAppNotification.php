<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\PengaturanAplikasi;
use App\Models\WhatsAppSession;
use App\Models\WhatsappLog;

class SendWhatsAppNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $phoneNumber;
    public string $message;
    public ?string $jenisPesan;
    public ?string $specificSessionId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $phoneNumber, string $message, ?string $jenisPesan = 'Notifikasi', ?string $specificSessionId = null)
    {
        $this->phoneNumber = $this->formatWhatsAppNumber($phoneNumber);
        $this->message = $message;
        $this->jenisPesan = $jenisPesan ?: 'Notifikasi';
        $this->specificSessionId = ($specificSessionId && $specificSessionId !== 'auto') ? $specificSessionId : null;
    }

    /**
     * Tentukan batas waktu maksimal percobaan pengerjaan job (2 jam).
     */
    public function retryUntil(): \DateTime
    {
        return now()->addHours(2);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $setting = PengaturanAplikasi::getSettings();
        
        // Periksa toggle aktivasi fitur WhatsApp di pengaturan
        $waStatus = isset($setting->wa_status) ? (int)$setting->wa_status : ($setting->enable_whatsapp ? 1 : 0);
        if ($waStatus === 0) {
            Log::info("WA Queue: Notifikasi WA dinonaktifkan di Pengaturan Aplikasi. Pekerjaan dibatalkan.");
            return;
        }

        // Deteksi duplikasi pesan berdasarkan MD5 fingerprint (anti-duplicate 5 menit)
        $fingerprint = md5($this->phoneNumber . '_' . $this->message);
        $cacheKey = 'wa-sent-' . $fingerprint;
        if (Cache::has($cacheKey)) {
            Log::info("WA Queue: Pesan duplikat terdeteksi untuk {$this->phoneNumber}. Mengabaikan pekerjaan.");
            return;
        }

        $baseUrl = ($setting && $setting->wa_api_url) ? $setting->wa_api_url : env('OPEN_WA_API_URL', 'http://localhost:2785/api');
        $apiKey = ($setting && $setting->wa_api_key) ? $setting->wa_api_key : env('OPEN_WA_API_KEY');

        // Ambil semua sesi aktif yang berstatus CONNECTED dari database (Multi-Session Load Balancing)
        $activeSessions = WhatsAppSession::where('is_active', true)
            ->where('status', 'CONNECTED')
            ->get();

        // Auto-Healing: Cek sesi aktif yang berstatus offline jika sudah lebih dari 5 menit sejak update terakhir
        $needsHealingSessions = WhatsAppSession::where('is_active', true)
            ->where('status', '!=', 'CONNECTED')
            ->where('updated_at', '<', now()->subMinutes(5))
            ->get();

        if ($needsHealingSessions->isNotEmpty()) {
            $headers = ['Content-Type' => 'application/json', 'Accept' => 'application/json'];
            if ($apiKey) {
                $headers['Authorization'] = 'Bearer ' . $apiKey;
                $headers['X-API-Key'] = $apiKey;
            }

            foreach ($needsHealingSessions as $s) {
                try {
                    $statusResponse = Http::withHeaders($headers)
                        ->timeout(3)
                        ->get("{$baseUrl}/sessions/{$s->session_id}");

                    if ($statusResponse->successful()) {
                        $statusData = $statusResponse->json();
                        $rawStatus = $statusData['status'] ?? 'CONNECTED';
                        $isConnected = in_array(strtolower($rawStatus), ['connected', 'working', 'ready']);
                        
                        $newStatus = $isConnected ? 'CONNECTED' : $rawStatus;
                        $s->update(['status' => $newStatus]);
                        
                        if ($isConnected) {
                            Log::info("WA Queue Auto-Healing: Sesi '{$s->label}' ({$s->session_id}) berhasil pulih dan terhubung kembali.");
                            $activeSessions->push($s);
                        }
                    } else {
                        $s->touch();
                    }
                } catch (\Exception $e) {
                    $s->touch();
                }
            }
        }

        $session = null;
        $sessionId = null;
        $delaySeconds = rand(12, 18); // Jeda acak 12-18 detik per pesan (Anti-Ban Delay)

        // Load balancing setting (default true)
        $loadBalancingEnabled = ($setting && isset($setting->wa_load_balancing)) ? (bool)$setting->wa_load_balancing : true;

        if ($this->specificSessionId) {
            // Jika ada sesi khusus yang ditentukan
            $specificSession = WhatsAppSession::where('session_id', $this->specificSessionId)->first();
            $sessionId = $this->specificSessionId;
            $session = $specificSession;

            // Periksa apakah sesi khusus sedang dalam mode istirahat (sleep)
            if (Cache::has('whatsapp-sleep-lock-' . $sessionId)) {
                $this->release(rand(10, 20));
                return;
            }

            // Lock cache untuk jeda anti-ban
            $lockKey = 'whatsapp-send-lock-' . $sessionId;
            $lock = Cache::lock($lockKey, $delaySeconds);

            if (!$lock->get()) {
                $this->release(rand(3, 5));
                return;
            }
        } elseif ($loadBalancingEnabled && $activeSessions->isNotEmpty()) {
            // Acak urutan sesi untuk load balancing yang merata
            $shuffledSessions = $activeSessions->shuffle();

            foreach ($shuffledSessions as $s) {
                // Lewati jika sesi sedang istirahat (sleep)
                if (Cache::has('whatsapp-sleep-lock-' . $s->session_id)) {
                    continue;
                }

                $lockKey = 'whatsapp-send-lock-' . $s->session_id;
                $lock = Cache::lock($lockKey, $delaySeconds);

                if ($lock->get()) {
                    $session = $s;
                    $sessionId = $s->session_id;
                    break;
                }
            }

            // Jika semua sesi sedang terkunci/mengirim
            if (!$session) {
                $this->release(rand(3, 5));
                return;
            }
        } else {
            // Sesi default jika load balancing dinonaktifkan atau belum ada sesi CONNECTED
            $sessionId = ($setting && $setting->wa_session_id) ? $setting->wa_session_id : env('OPEN_WA_SESSION_ID', 'default');
            $session = WhatsAppSession::where('session_id', $sessionId)->first();

            if (Cache::has('whatsapp-sleep-lock-' . $sessionId)) {
                $this->release(rand(10, 20));
                return;
            }

            $lockKey = 'whatsapp-send-lock-' . $sessionId;
            $lock = Cache::lock($lockKey, $delaySeconds);

            if (!$lock->get()) {
                $this->release(rand(3, 5));
                return;
            }
        }

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        if ($apiKey) {
            $headers['Authorization'] = 'Bearer ' . $apiKey;
            $headers['X-API-Key'] = $apiKey;
        }

        try {
            $targetChatId = $this->phoneNumber;

            // Resolusi JID otomatis (antisipasi migrasi format nomor / LID)
            try {
                $rawNumber = explode('@', $this->phoneNumber)[0];
                $chkContactResponse = Http::withHeaders($headers)
                    ->timeout(10)
                    ->get("{$baseUrl}/sessions/{$sessionId}/contacts/check/{$rawNumber}");

                if ($chkContactResponse->successful()) {
                    $chkData = $chkContactResponse->json();
                    if (isset($chkData['whatsappId'])) {
                        $targetChatId = $chkData['whatsappId'];
                    }
                } else {
                    $chkData = $chkContactResponse->json();
                    $chkMsg = is_array($chkData) ? ($chkData['message'] ?? ($chkData['error'] ?? '')) : $chkContactResponse->body();

                    $isSessionError = $chkContactResponse->status() === 409 || 
                                      ($chkContactResponse->status() === 400 && (
                                          str_contains(strtolower($chkMsg), 'not active') ||
                                          str_contains(strtolower($chkMsg), 'not started') ||
                                          str_contains(strtolower($chkMsg), 'start the session first')
                                      )) ||
                                      str_contains(strtolower($chkMsg), 'session is not connected') || 
                                      str_contains(strtolower($chkMsg), 'not connected') ||
                                      str_contains(strtolower($chkMsg), 'session offline') ||
                                      str_contains(strtolower($chkMsg), 'auth');

                    if ($isSessionError) {
                        if ($session) {
                            $session->update(['status' => 'NOT_READY']);
                            Log::warning("WA Queue: Sesi '{$session->label}' ({$sessionId}) terputus saat kontak check. Beralih ke sesi lain.");
                        }
                        $this->release($session ? 10 : 300);
                        return;
                    }
                }
            } catch (\Exception $diagEx) {
                if ($diagEx instanceof \Illuminate\Queue\MaxAttemptsExceededException || $diagEx instanceof \Illuminate\Queue\ReleasedException) {
                    throw $diagEx;
                }
                Log::warning("WA Queue Diagnostic: Resolusi JID kontak dilewati. Error: " . $diagEx->getMessage());
            }

            Log::info("WA Queue: Mengirim pesan ke {$targetChatId} via sesi {$sessionId}");

            // Kirim pesan WhatsApp
            $response = Http::withHeaders($headers)
                ->timeout(15)
                ->post("{$baseUrl}/sessions/{$sessionId}/messages/send-text", [
                    'chatId' => $targetChatId,
                    'text' => $this->message,
                ]);

            $responseData = $response->json();
            $isHttpSuccess = $response->successful();
            $isWaSuccess = is_array($responseData) && ($responseData['success'] ?? true) === true;
            $isSuccess = $isHttpSuccess && $isWaSuccess;
            $isOpenWaTimeout = isset($responseData['statusCode']) && $responseData['statusCode'] == 500;

            if ($isOpenWaTimeout) {
                // Periksa apakah sesi masih benar-benar terhubung
                try {
                    $statusResponse = Http::withHeaders($headers)->timeout(5)->get("{$baseUrl}/sessions/{$sessionId}");
                    if ($statusResponse->successful()) {
                        $statusData = $statusResponse->json();
                        $status = $statusData['status'] ?? 'CONNECTED';
                        $isConnected = in_array(strtolower($status), ['connected', 'working', 'ready']);

                        if (!$isConnected) {
                            if ($session) {
                                $session->update(['status' => 'NOT_READY']);
                            }
                            Log::warning("WA Queue: Error 500 OpenWA & sesi '{$sessionId}' tidak terhubung. Merilis ulang.");
                            $this->release($session ? 10 : 300);
                            return;
                        }

                        if ($this->attempts() < 2) {
                            Log::warning("WA Queue: Timeout 500 dari OpenWA untuk {$this->phoneNumber}. Mencoba ulang (Percobaan ke-{$this->attempts()}).");
                            $this->release(30);
                            return;
                        }
                    }
                } catch (\Exception $checkEx) {
                    if ($checkEx instanceof \Illuminate\Queue\MaxAttemptsExceededException || $checkEx instanceof \Illuminate\Queue\ReleasedException) {
                        throw $checkEx;
                    }
                }
            }

            if ($isSuccess || $isOpenWaTimeout) {
                Log::info("WA Queue: Berhasil mengirim WhatsApp ke {$this->phoneNumber} via sesi {$sessionId}");

                // Simpan cache anti-duplicate 5 menit
                Cache::put($cacheKey, true, 300);

                // Sleep Mode Tracker per sesi: tiap 50 pesan, istirahat 60-120 detik
                $count = Cache::increment('whatsapp-sent-count-' . $sessionId);
                if ($count >= 50) {
                    $sleepSeconds = rand(60, 120);
                    Cache::put('whatsapp-sleep-lock-' . $sessionId, true, $sleepSeconds);
                    Cache::put('whatsapp-sent-count-' . $sessionId, 0);
                    Log::warning("WA Queue: Sesi '{$sessionId}' telah mengirim 50 pesan. Masuk Mode Istirahat (Sleep Mode) selama {$sleepSeconds} detik.");
                }

                // Catat ke whatsapp_logs
                try {
                    WhatsappLog::create([
                        'nomor_tujuan' => $this->phoneNumber,
                        'jenis_pesan' => $this->jenisPesan,
                        'isi_pesan' => $this->message,
                        'status' => 'sent',
                        'error_message' => null,
                        'sent_at' => now(),
                    ]);
                } catch (\Exception $logEx) {}

            } else {
                $errorMessage = $responseData['message'] ?? ($responseData['error'] ?? 'Unknown Error');

                // Jika nomor tidak terdaftar di WhatsApp
                $isInvalidNumber = str_contains(strtolower($errorMessage), 'not registered') || 
                                   str_contains(strtolower($errorMessage), 'does not exist') ||
                                   str_contains(strtolower($errorMessage), 'invalid chatid') ||
                                   str_contains(strtolower($errorMessage), 'invalid number');

                if ($isInvalidNumber) {
                    Log::warning("WA Queue: Nomor {$this->phoneNumber} tidak terdaftar di WhatsApp. Pengiriman dibatalkan.");
                    try {
                        WhatsappLog::create([
                            'nomor_tujuan' => $this->phoneNumber,
                            'jenis_pesan' => $this->jenisPesan,
                            'isi_pesan' => $this->message,
                            'status' => 'failed',
                            'error_message' => 'Nomor tidak terdaftar di WhatsApp',
                            'sent_at' => now(),
                        ]);
                    } catch (\Exception $logEx) {}
                    return;
                }

                // Jika sesi terputus
                $isSessionError = $response->status() === 409 || 
                                  ($response->status() === 400 && (
                                      str_contains(strtolower($errorMessage), 'not active') ||
                                      str_contains(strtolower($errorMessage), 'not started') ||
                                      str_contains(strtolower($errorMessage), 'start the session first')
                                  )) ||
                                  str_contains(strtolower($errorMessage), 'session is not connected') || 
                                  str_contains(strtolower($errorMessage), 'not connected') ||
                                  str_contains(strtolower($errorMessage), 'session offline') ||
                                  str_contains(strtolower($errorMessage), 'auth');

                if ($isSessionError) {
                    if ($session) {
                        $session->update(['status' => 'NOT_READY']);
                        Log::warning("WA Queue: Sesi '{$session->label}' ({$sessionId}) terputus. Menandai NOT_READY dan mengalihkan job.");
                    }
                    $this->release($session ? 10 : 300);
                    return;
                }

                throw new \Exception("Gagal mengirim WhatsApp via OpenWA: " . $response->body());
            }

        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Queue\MaxAttemptsExceededException || $e instanceof \Illuminate\Queue\ReleasedException) {
                throw $e;
            }

            Log::error("WA Queue Exception: " . $e->getMessage());
            $this->release(60);
        }
    }

    /**
     * Dipanggil ketika job gagal secara permanen.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("WA Queue: Pengiriman pesan ke {$this->phoneNumber} GAGAL PERMANEN. Error: " . $exception->getMessage());

        try {
            WhatsappLog::create([
                'nomor_tujuan' => $this->phoneNumber,
                'jenis_pesan' => $this->jenisPesan,
                'isi_pesan' => $this->message,
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
                'sent_at' => now(),
            ]);
        } catch (\Exception $logEx) {}
    }

    /**
     * Format phone number to open-wa format
     */
    protected function formatWhatsAppNumber(string $number): string
    {
        $clean = preg_replace('/[^0-9]/', '', $number);
        
        if (str_starts_with($clean, '620')) {
            $clean = '62' . substr($clean, 3);
        } elseif (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62' . $clean;
        }
        
        if (!str_contains($clean, '@c.us')) {
            $clean .= '@c.us';
        }
        
        return $clean;
    }
}
