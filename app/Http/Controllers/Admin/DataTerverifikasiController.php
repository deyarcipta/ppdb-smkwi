<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSiswa;
use App\Models\PembayaranSiswa;
use App\Models\MasterBiaya;
use App\Models\TemplatePesan;
use App\Models\ActivityLog;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DataTerverifikasiController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function index()
    {
        // Ambil hanya akun siswa yang sudah aktif
        $data = UserSiswa::with(['dataSiswa', 'pembayaran' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])
            ->where('status_akun', 'aktif')
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Ambil total biaya dari master biaya
        $totalBiaya = MasterBiaya::where('jenis_biaya', 'ppdb')
            ->first()->total_biaya ?? 0;

        return view('admin.data-terverifikasi.index', compact('data', 'totalBiaya'));
    }

     public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users_siswa,id',
            'status_pendaftar' => 'required|in:pending,diterima,ditolak',
            'ket_pendaftaran' => 'nullable|string|max:500'
        ]);

        try {
            $user = UserSiswa::with('dataSiswa')->find($request->id);
            
            if (!$user) {
                Log::error("User not found: {$request->id}");
                return back()->with('error', 'User tidak ditemukan!');
            }

            $statusSebelumnya = $user->dataSiswa->status_pendaftar ?? 'pending';

            if ($user->dataSiswa) {
                $updateData = [
                    'status_pendaftar' => $request->status_pendaftar
                ];

                // Jika status berubah menjadi ditolak dan ada catatan, simpan catatan
                if ($request->status_pendaftar === 'ditolak' && $request->ket_pendaftaran) {
                    $updateData['ket_pendaftaran'] = $request->ket_pendaftaran;
                }

                // Jika status berubah dari ditolak ke status lain, hapus catatan
                if ($statusSebelumnya === 'ditolak' && $request->status_pendaftar !== 'ditolak') {
                    $updateData['ket_pendaftaran'] = null;
                }

                $updated = $user->dataSiswa->update($updateData);
                
                // Refresh data untuk memastikan
                $user->dataSiswa->refresh();

                ActivityLog::logManual(
                    "Memverifikasi pendaftaran #{$user->dataSiswa->id} - {$user->dataSiswa->nama_lengkap}",
                    'verify'
                );

                // ✅ Kirim pesan WhatsApp hanya jika status berubah menjadi "diterima"
                if ($request->status_pendaftar === 'diterima' && $statusSebelumnya !== 'diterima') {
                    $this->sendPenerimaanMessage(
                        $user->dataSiswa->no_hp,
                        $user->dataSiswa
                    );
                }

            } else {
                return back()->with('error', 'Data siswa tidak ditemukan!');
            }

            return back()->with('success', 'Status pendaftar berhasil diperbarui!');
            
        } catch (\Exception $e) {
            Log::error('Update status error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function sendPenerimaanMessage($phoneNumber, $dataSiswa)
    {
        try {
            // Ambil template pesan dari database
            $template = TemplatePesan::where('jenis_pesan', 'pendaftar_diterima')
                ->where('status', true)
                ->first();

            if (!$template) {
                Log::warning('Template pesan "pendaftar_diterima" tidak ditemukan atau nonaktif');
                return false;
            }

            // Siapkan placeholder data
            $placeholders = $this->getPenerimaanPlaceholders($dataSiswa);

            // Ganti placeholder dengan data aktual
            $message = strtr($template->isi_pesan, $placeholders);

            // Kirim pesan WhatsApp
            $result = $this->whatsappService->sendMessage($phoneNumber, $message);

            if ($result['success']) {
                Log::info("Pesan penerimaan berhasil dikirim ke: {$phoneNumber}");
            } else {
                Log::error("Gagal mengirim pesan penerimaan: " . ($result['error'] ?? 'Unknown error'));
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Error sendPenerimaanMessage: ' . $e->getMessage());
            return false;
        }
    }

    private function getPenerimaanPlaceholders($dataSiswa)
    {
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;

        return [
            '{nama}' => $dataSiswa->nama_panggilan ?? explode(' ', $dataSiswa->nama_lengkap)[0] ?? $dataSiswa->nama_lengkap,
            '{nama_lengkap}' => $dataSiswa->nama_lengkap,
            '{no_pendaftaran}' => $dataSiswa->no_pendaftaran ?? '-',
            '{jurusan}' => $dataSiswa->jurusan->nama_jurusan ?? '-',
            '{gelombang}' => $dataSiswa->gelombang->nama_gelombang ?? 'Gelombang 1',
            '{tahun_ajaran}' => "{$currentYear}/{$nextYear}",
            '{batas_daftar_ulang}' => "31 Juli {$currentYear}",
            '{periode_daftar_ulang}' => "15 - 31 Juli {$currentYear}",
            '{tanggal_mpls}' => "1 Agustus {$currentYear}",
            '{tanggal_awal_semester}' => "5 Agustus {$currentYear}",
            '{alamat_sekolah}' => "Jl. Pendidikan No. 123, Jakarta",
            '{no_admin}' => "0852-1234-5678",
            '{email_sekolah}' => "ppdb@smkwisataindonesia.sch.id",
            '{url_sistem}' => "https://ppdb.wisataindonesia.sch.id",
            
            // Tambahan placeholder untuk fleksibilitas
            '{sekolah}' => 'SMK Wisata Indonesia',
            '{telepon_sekolah}' => '(021) 1234567',
            '{website}' => 'https://smkwisataindonesia.sch.id',
        ];
    }

    public function destroy($id)
    {
        $user = UserSiswa::findOrFail($id);
        // Hapus pembayaran siswa terkait (misal punya relasi 'pembayaran')
        $user->pembayaran()->delete();

        ActivityLog::logManual(
            "Menghapus pendaftaran #{$user->id} - {$user->dataSiswa->nama_lengkap}",
            'delete'
        );
        $user->delete();

        return back()->with('success', 'Data pendaftar terverifikasi berhasil dihapus!');
    }
}