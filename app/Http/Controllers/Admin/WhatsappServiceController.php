<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\TemplatePesan;
use App\Models\WhatsappLog;

class WhatsappServiceController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'tujuan'      => 'required',
            'jenis_pesan' => 'required',
            'data'        => 'array'
        ]);

        // 1. Ambil template pesan
        $template = TemplatePesan::where('jenis_pesan', $request->jenis_pesan)
            ->where('status', 'aktif')
            ->firstOrFail();

        // 2. Render pesan
        $pesan = $this->renderTemplate(
            $template->isi_pesan,
            $request->data ?? []
        );

        try {
            // 3. Kirim ke Node.js
            $response = Http::post('http://localhost:3000/send-message', [
                'phone'   => $request->tujuan,
                'message' => $pesan
            ]);

            $result = $response->json();

            // 4. Cek hasil pengiriman
            if (!($result['success'] ?? false)) {
                throw new \Exception($result['error'] ?? 'Gagal mengirim WhatsApp');
            }

            // 5. Simpan log BERHASIL
            WhatsappLog::create([
                'nomor_tujuan' => $request->tujuan,
                'jenis_pesan'  => $request->jenis_pesan,
                'isi_pesan'    => $pesan,
                'status'       => 'sent',
                'sent_at'      => now(),
            ]);

            dd($log);

            return response()->json([
                'success' => true,
                'pesan'   => $pesan
            ]);

        } catch (\Exception $e) {

            // 6. Simpan log GAGAL
            WhatsappLog::create([
                'nomor_tujuan'  => $request->tujuan,
                'jenis_pesan'   => $request->jenis_pesan,
                'isi_pesan'     => $pesan,
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    private function renderTemplate(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            $template = str_replace(
                '{' . $key . '}',
                $value,
                $template
            );
        }

        return $template;
    }
}
