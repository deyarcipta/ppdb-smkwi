<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSiswa;
use App\Models\DataSiswa;
use App\Models\TemplatePesan;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Jurusan;
use App\Models\Gelombang;

class VerifikasiPendaftarController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function index(Request $request)
    {
        $query = UserSiswa::with(['dataSiswa.jurusan', 'dataSiswa.gelombang'])
            ->where('status_akun', 'belum_verifikasi');

        if ($request->filled('jurusan_id')) {
            $query->whereHas('dataSiswa', function($q) use ($request) {
                $q->where('jurusan_id', $request->jurusan_id);
            });
        }

        if ($request->filled('gelombang_id')) {
            $query->whereHas('dataSiswa', function($q) use ($request) {
                $q->where('gelombang_id', $request->gelombang_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhereHas('dataSiswa', function($sub) use ($search) {
                      $sub->where('nama_lengkap', 'like', "%{$search}%")
                          ->orWhere('no_hp', 'like', "%{$search}%")
                          ->orWhere('asal_sekolah', 'like', "%{$search}%");
                  });
            });
        }

        $data = $query->latest()->paginate(10)->withQueryString();
        $jurusanList = Jurusan::all();
        $gelombangList = Gelombang::all();

        return view('admin.verifikasi-pendaftar.index', compact('data', 'jurusanList', 'gelombangList'));
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

    /**
     * Bulk Action: Verifikasi / Jadikan Diterima Massal
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'required|exists:users_siswa,id',
            'status_pendaftar' => 'required|in:belum_verifikasi,sudah_verifikasi,diterima'
        ]);

        $status = $request->status_pendaftar;
        $count = 0;

        foreach ($request->user_ids as $id) {
            $user = UserSiswa::with('dataSiswa')->find($id);
            if ($user) {
                if ($status === 'sudah_verifikasi' || $status === 'diterima') {
                    $user->update(['status_akun' => 'aktif']);

                    if ($user->dataSiswa) {
                        $user->dataSiswa->update([
                            'status_pendaftar' => ($status === 'diterima') ? 'diterima' : 'sudah_verifikasi'
                        ]);
                    }

                    // Kirim pesan WhatsApp otomatis (queue work)
                    if ($user->dataSiswa && $user->dataSiswa->no_hp) {
                        $this->sendMessage(
                            $user->dataSiswa->no_hp,
                            $user->username,
                            $user->dataSiswa->nama_lengkap
                        );
                    }
                } else {
                    $user->update(['status_akun' => 'belum_verifikasi']);
                    if ($user->dataSiswa) {
                        $user->dataSiswa->update(['status_pendaftar' => 'belum_verifikasi']);
                    }
                }
                $count++;
            }
        }

        $label = ($status === 'diterima') ? 'diterima' : 'diverifikasi';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Berhasil memproses {$count} pendaftar menjadi {$label}!"
            ]);
        }

        return back()->with('success', "Berhasil memproses {$count} pendaftar menjadi {$label}!");
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

            $pengaturan = \App\Models\PengaturanAplikasi::getSettings();
            $namaSekolah = $pengaturan->nama_sekolah ?? 'SMK Wisata Indonesia';
            $tipeAplikasi = $pengaturan->tipe_aplikasi ?? 'PPDB';
            $tahunAjaranAktif = $pengaturan->tahunAjaranAktif;
            $tahunAjaranNama = $tahunAjaranAktif ? $tahunAjaranAktif->nama : (date('Y') . '/' . (date('Y') + 1));

            $placeholders = [
                '{nama}' => $namaLengkap,
                '{username}' => $username,
                '{password}' => 'password123',
                '{tahun_ajaran}' => $tahunAjaranNama,
                '{rekening}' => '1234567890',
                '{an}' => $namaSekolah,
                '{no_admin}' => $pengaturan->no_hp ?? '0852-1815-0720',
                '{url_sistem}' => url('/siswa'),
                '{tipe_aplikasi}' => $tipeAplikasi,
                '{nama_aplikasi}' => $pengaturan->nama_aplikasi ?? $tipeAplikasi,
            ];

            // Ganti placeholder dalam isi pesan
            $message = strtr($template->isi_pesan, $placeholders);

            $jenisPesan = strtr($template->judul, $placeholders);

            // Kirim pesan WhatsApp
            $result = $this->whatsappService->sendMessage($phoneNumber, $message, $jenisPesan);

        } catch (\Exception $e) {
            Log::error('Error sendMessage: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}