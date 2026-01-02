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
        $logs = WhatsappLog::latest()->limit(50)->get();
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
}
