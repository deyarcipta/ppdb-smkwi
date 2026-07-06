<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Models\WhatsappLog;

class WhatsappAdminController extends Controller
{
    private $waUrl = 'http://127.0.0.1:3000';

    public function index()
    {
        $logs = WhatsappLog::latest()->limit(20)->get();
        return view('admin.whatsapp.index', compact('logs'));
    }

    public function status()
    {
        try {
            $response = Http::timeout(3)->get($this->waUrl . '/health');

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'status' => 'error',
                'message' => 'WhatsApp server tidak merespon'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'offline',
                'message' => 'Server WhatsApp mati'
            ], 500);
        }
    }

    public function startServer()
    {
        try {
            // Cek port 3000 terlebih dahulu
            $connection = @fsockopen('127.0.0.1', 3000, $errno, $errstr, 1);
            if (is_resource($connection)) {
                fclose($connection);
                return response()->json([
                    'status' => 'already_running',
                    'message' => 'Server WhatsApp sudah berjalan di port 3000'
                ]);
            }

            $botPath = base_path('whatsapp-bot');
            
            // Cek OS (Windows vs Linux)
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows (Laragon/Local)
                pclose(popen("cd /d " . escapeshellarg($botPath) . " && start /B node server.js > bot.log 2>&1", "r"));
                $msg = 'Server WhatsApp (Windows) sedang dijalankan...';
            } else {
                // Linux (aaPanel dengan PM2 & NVM)
                // Jalankan dengan bash dan source nvm.sh dari beberapa kemungkinan lokasi
                $cmd = 'bash -c \'';
                $cmd .= 'for profile in "$HOME/.nvm/nvm.sh" "/www/server/nvm/nvm.sh" "/root/.nvm/nvm.sh"; do ';
                $cmd .= '  if [ -f "$profile" ]; then ';
                $cmd .= '    . "$profile" && nvm use 24.11.1 && pm2 restart whatsapp-bot 2>&1; ';
                $cmd .= '    exit $?; ';
                $cmd .= '  fi; ';
                $cmd .= 'done; ';
                // Fallback jika tidak ditemukan profile nvm
                $cmd .= 'pm2 restart whatsapp-bot 2>&1; ';
                $cmd .= '\'';

                $output = [];
                $returnVar = 0;
                exec("cd " . escapeshellarg($botPath) . " && " . $cmd, $output, $returnVar);

                // Jika pm2 restart gagal/tidak ditemukan, coba pm2 start baru
                if ($returnVar !== 0) {
                    $cmdStart = 'bash -c \'';
                    $cmdStart .= 'for profile in "$HOME/.nvm/nvm.sh" "/www/server/nvm/nvm.sh" "/root/.nvm/nvm.sh"; do ';
                    $cmdStart .= '  if [ -f "$profile" ]; then ';
                    $cmdStart .= '    . "$profile" && nvm use 24.11.1 && pm2 start server.js --name "whatsapp-bot" 2>&1; ';
                    $cmdStart .= '    exit $?; ';
                    $cmdStart .= '  fi; ';
                    $cmdStart .= 'done; ';
                    $cmdStart .= 'pm2 start server.js --name "whatsapp-bot" 2>&1; ';
                    $cmdStart .= '\'';

                    exec("cd " . escapeshellarg($botPath) . " && " . $cmdStart, $output, $returnVar);
                }

                // Jika pm2 gagal total, fallback ke nohup node
                if ($returnVar !== 0) {
                    $cmdNode = 'bash -c \'';
                    $cmdNode .= 'for profile in "$HOME/.nvm/nvm.sh" "/www/server/nvm/nvm.sh" "/root/.nvm/nvm.sh"; do ';
                    $cmdNode .= '  if [ -f "$profile" ]; then ';
                    $cmdNode .= '    . "$profile" && nvm use 24.11.1 && nohup node server.js > bot.log 2>&1 & ';
                    $cmdNode .= '    exit 0; ';
                    $cmdNode .= '  fi; ';
                    $cmdNode .= 'done; ';
                    $cmdNode .= 'nohup node server.js > bot.log 2>&1 & ';
                    $cmdNode .= '\'';
                    
                    exec("cd " . escapeshellarg($botPath) . " && " . $cmdNode);
                    $msg = 'Server WhatsApp dijalankan menggunakan fallback (nohup node)...';
                } else {
                    $msg = 'Server WhatsApp berhasil dijalankan menggunakan PM2!';
                }
            }

            return response()->json([
                'status' => 'starting',
                'message' => $msg
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menjalankan server: ' . $e->getMessage()
            ], 500);
        }
    }
}
