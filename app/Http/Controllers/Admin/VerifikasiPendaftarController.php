<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSiswa;
use App\Models\DataSiswa;
use App\Models\TemplatePesan;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifikasiPendaftarController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function index()
    {
        $data = UserSiswa::with('dataSiswa')
            ->where('status_akun', 'belum_verifikasi')
            ->paginate(10);

        return view('admin.verifikasi-pendaftar.index', compact('data'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users_siswa,id',
            'status_pendaftar' => 'required|in:belum_verifikasi,sudah_verifikasi',
        ]);

        $user = UserSiswa::findOrFail($request->user_id);

        // Langsung update status_akun di users_siswa
        if ($request->status_pendaftar === 'sudah_verifikasi') {
            $user->update(['status_akun' => 'aktif']);
            
            // Kirim pesan welcome WhatsApp jika ada data siswa
            if ($user->dataSiswa && $user->dataSiswa->no_hp) {
                $this->sendMessage(
                    $user->dataSiswa->no_hp, 
                    $user->username, 
                    $user->dataSiswa->nama_lengkap
                );
            }
            
            $message = 'Akun berhasil diverifikasi dan diaktifkan.';
        } else {
            $user->update(['status_akun' => 'belum_verifikasi']);
            $message = 'Status akun berhasil diubah menjadi belum verifikasi.';
        }

        return back()->with('success', $message);
    }

    public function destroy($id)
    {
        UserSiswa::findOrFail($id)->delete();
        return back()->with('success', 'Data pendaftar berhasil dihapus.');
    }

    /**
     * Kirim pesan welcome WhatsApp
     */
    private function sendMessage($phoneNumber, $username, $namaLengkap)
    {
        try {
            // Ambil template pesan dari database berdasarkan jenis_pesan
            $template = TemplatePesan::where('jenis_pesan', 'aktifasi_akun')
                ->where('status', true)
                ->first();

            if (!$template) {
                Log::warning('Template pesan "aktifasi_akun" tidak ditemukan atau nonaktif.');
                return ['success' => false, 'error' => 'Template pesan tidak ditemukan'];
            }

            // Buat variabel pengganti (placeholder)
            $currentYear = date('Y');
            $nextYear = $currentYear + 1;

            $placeholders = [
                '{nama}' => $namaLengkap,
                '{username}' => $username,
                '{password}' => 'password123',
                '{tahun_ajaran}' => "{$currentYear}/{$nextYear}",
                '{rekening}' => '1234567890',
                '{an}' => 'SMK Wisata Indonesia',
                '{no_admin}' => '0852-1234-5678',
                '{url_sistem}' => 'https://ppdb.wisataindonesia.sch.id',
            ];

            // Ganti placeholder dalam isi pesan
            $message = strtr($template->isi_pesan, $placeholders);

            // Kirim pesan WhatsApp
            return $this->whatsappService->sendMessage($phoneNumber, $message);

        } catch (\Exception $e) {
            Log::error('Error sendMessage: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}