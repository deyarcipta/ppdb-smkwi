<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\MasterBiaya;
use App\Models\DataSiswa;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\TemplatePesan;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VerifikasiPembayaranController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function index(Request $request)
    {
        $query = Pembayaran::with(['user', 'verifier', 'dataSiswa'])
            ->where('status', 'pending');

        // Filter berdasarkan jenis pembayaran
        if ($request->has('jenis_pembayaran') && $request->jenis_pembayaran != '') {
            $query->where('jenis_pembayaran', $request->jenis_pembayaran);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('nama_siswa', 'like', "%{$search}%");
            });
        }

        $data = $query->latest()->paginate(10);
        
        $jenisPembayaran = [
            'pendaftaran' => 'Biaya Pendaftaran',
            'formulir' => 'Biaya Formulir',
            'ppdb' => 'Biaya PPDB',
            'uang_pangkal' => 'Uang Pangkal',
            'spp' => 'SPP'
        ];

        // Hitung statistik
        $totalMenunggu = Pembayaran::where('status', 'pending')->count();
        $totalAmount = Pembayaran::where('status', 'pending')->sum('jumlah');

        return view('admin.verifikasi-pembayaran.index', compact('data', 'jenisPembayaran', 'totalMenunggu', 'totalAmount'));
    }

    public function verify(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diverifikasi,ditolak',
            'catatan' => 'nullable|string|max:500'
        ]);

        $pembayaran = Pembayaran::with(['dataSiswa'])->findOrFail($id);

        // Gunakan transaction untuk memastikan konsistensi data
        DB::beginTransaction();
        try {
            $pembayaran->update([
                'status' => $request->status,
                'catatan' => $request->catatan,
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);

            ActivityLog::logManual(
                "Memverifikasi pembayaran #{$pembayaran->id} - {$pembayaran->dataSiswa->nama_lengkap}",
                'verify'
            );

            // ✅ Jika status diverifikasi, cek apakah PPDB sudah lunas dan update is_paid
            if ($request->status === 'diverifikasi' && $pembayaran->dataSiswa) {
                $this->checkAndUpdatePaymentStatus($pembayaran->dataSiswa);
                
                // Kirim pesan WhatsApp sesuai jenis pembayaran
                $this->sendVerificationMessage(
                    $pembayaran->dataSiswa->no_hp,
                    $pembayaran->dataSiswa->nama_lengkap,
                    $pembayaran->jenis_pembayaran,
                    $pembayaran->jumlah,
                    $pembayaran->dataSiswa
                );
            }

            DB::commit();

            return redirect()->route('verifikasi-pembayaran.index')
                ->with('success', 'Pembayaran berhasil diverifikasi');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error verify pembayaran: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memverifikasi pembayaran');
        }
    }

    private function checkAndUpdatePaymentStatus($dataSiswa)
    {
        try {
            // Ambil total biaya PPDB dari master_biaya
            $masterBiaya = MasterBiaya::where('jenis_biaya', 'ppdb')
                ->where('status', '1')
                ->first();

            if (!$masterBiaya) {
                Log::warning('Master biaya PPDB tidak ditemukan');
                return false;
            }

            $totalBiayaPPDB = $masterBiaya->total_biaya;

            // Hitung total yang sudah dibayar untuk PPDB oleh siswa ini
            $totalDibayar = Pembayaran::where('user_id', $dataSiswa->user_id)
                ->where('jenis_pembayaran', 'ppdb')
                ->where('status', 'diverifikasi')
                ->sum('jumlah');

            // Cek apakah sudah lunas
            $isLunas = $totalDibayar >= $totalBiayaPPDB;

            // Update status is_paid di data_siswa
            if ($isLunas) {
                DataSiswa::where('user_id', $dataSiswa->user_id)->update([
                    'is_paid' => 1,
                ]);
                
                Log::info("Status pembayaran PPDB untuk siswa {$dataSiswa->nama_lengkap} diupdate menjadi LUNAS");
            } else {
                // Jika belum lunas, pastikan is_paid = 0
                DataSiswa::where('id', $dataSiswa->id)->update([
                    'is_paid' => 0
                ]);
                Log::info("Status pembayaran PPDB untuk siswa {$dataSiswa->nama_lengkap} diupdate menjadi tidak");
            }

            return $isLunas;

        } catch (\Exception $e) {
            Log::error('Error checkAndUpdatePaymentStatus: ' . $e->getMessage());
            return false;
        }
    }

    private function sendVerificationMessage($phoneNumber, $namaLengkap, $jenisPembayaran, $jumlah, $dataSiswa = null)
    {
        try {
            // Tentukan jenis pesan berdasarkan jenis pembayaran
            $jenisPesan = $this->getJenisPesan($jenisPembayaran);
            
            if (!$jenisPesan) {
                Log::warning("Jenis pesan tidak ditemukan untuk jenis pembayaran: {$jenisPembayaran}");
                return ['success' => false, 'error' => 'Jenis pesan tidak ditemukan'];
            }

            // Ambil template pesan berdasarkan jenis pesan
            $template = TemplatePesan::where('jenis_pesan', $jenisPesan)
                ->where('status', true)
                ->first();

            if (!$template) {
                Log::warning("Template pesan '{$jenisPesan}' tidak ditemukan atau nonaktif.");
                return ['success' => false, 'error' => 'Template pesan tidak ditemukan'];
            }

            // Hitung status pembayaran untuk PPDB
            $statusPembayaran = null;
            if ($jenisPembayaran === 'ppdb' && $dataSiswa) {
                $statusPembayaran = $this->calculateStatusPembayaranPPDB($dataSiswa->id);
            }

            // Ganti placeholder dengan data dinamis
            $placeholders = $this->getPlaceholders($namaLengkap, $jenisPembayaran, $jumlah, $dataSiswa, $statusPembayaran);

            $message = strtr($template->isi_pesan, $placeholders);

            // Kirim pesan
            return $this->whatsappService->sendMessage($phoneNumber, $message);

        } catch (\Exception $e) {
            Log::error('Error sendVerificationMessage: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function calculateStatusPembayaranPPDB($siswaId)
    {
        try {
            // Ambil total biaya PPDB dari master_biaya
            $masterBiaya = MasterBiaya::where('jenis_biaya', 'ppdb')
                ->where('status', '1')
                ->first();

            if (!$masterBiaya) {
                Log::warning('Master biaya PPDB tidak ditemukan');
                return 'belum_lunas'; // Default jika master biaya tidak ditemukan
            }

            $totalBiayaPPDB = $masterBiaya->total_biaya;

            // Hitung total yang sudah dibayar untuk PPDB oleh siswa ini
            $totalDibayar = Pembayaran::where('user_id', $siswaId)
                ->where('jenis_pembayaran', 'ppdb')
                ->where('status', 'diverifikasi')
                ->sum('jumlah');

            // Tentukan status berdasarkan perbandingan
            if ($totalDibayar >= $totalBiayaPPDB) {
                return 'lunas';
            } else {
                return 'belum_lunas';
            }

        } catch (\Exception $e) {
            Log::error('Error calculateStatusPembayaranPPDB: ' . $e->getMessage());
            return 'belum_lunas';
        }
    }

    private function getJenisPesan($jenisPembayaran)
    {
        $mapping = [
            'formulir' => 'verifikasi_formulir',
            'ppdb' => 'verifikasi_ppdb',
            'pendaftaran' => 'verifikasi_pendaftaran',
            'uang_pangkal' => 'verifikasi_uang_pangkal',
            'spp' => 'verifikasi_spp'
        ];

        return $mapping[$jenisPembayaran] ?? null;
    }

    private function getPlaceholders($namaLengkap, $jenisPembayaran, $jumlah, $dataSiswa = null, $statusPembayaran = null)
    {
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
        
        $placeholders = [
            '{nama}' => $namaLengkap,
            '{jenis_pembayaran}' => ucfirst(str_replace('_', ' ', $jenisPembayaran)),
            '{jumlah}' => number_format($jumlah, 0, ',', '.'),
            '{tahun_ajaran}' => "{$currentYear}/{$nextYear}",
            '{rekening}' => '1234567890',
            '{an}' => 'SMK Wisata Indonesia',
            '{no_admin}' => '0852-1815-0720',
            '{url_sistem}' => 'https://ppdb.wisataindonesia.sch.id',
        ];

        // Tambahkan placeholder khusus untuk PPDB
        if ($jenisPembayaran === 'ppdb') {
            $placeholders = array_merge($placeholders, $this->getPPDBPlaceholders($statusPembayaran, $dataSiswa));
        }

        // Tambahkan placeholder khusus untuk formulir
        if ($jenisPembayaran === 'formulir') {
            $placeholders = array_merge($placeholders, $this->getFormulirPlaceholders());
        }

        // Tambahkan data siswa jika tersedia
        if ($dataSiswa) {
            $placeholders['{jurusan}'] = $dataSiswa->jurusan->nama_jurusan ?? '-';
            $placeholders['{no_pendaftaran}'] = $dataSiswa->no_pendaftaran ?? '-';
            $placeholders['{nama_lengkap}'] = $dataSiswa->nama_lengkap ?? $namaLengkap;
        }

        return $placeholders;
    }

    private function getPPDBPlaceholders($statusPembayaran, $dataSiswa)
    {
        try {
            // Ambil data master biaya dan total dibayar
            $masterBiaya = MasterBiaya::where('jenis_biaya', 'ppdb')
                ->where('status', '1')
                ->first();

            $totalBiayaPPDB = $masterBiaya ? $masterBiaya->total_biaya : 0;

            $totalDibayar = 0;
            if ($dataSiswa) {
                $totalDibayar = Pembayaran::where('user_id', $dataSiswa->id)
                    ->where('jenis_pembayaran', 'ppdb')
                    ->where('status', 'diverifikasi')
                    ->sum('jumlah');
            }

            $sisaPembayaran = $totalBiayaPPDB - $totalDibayar;

            // Default untuk PPDB
            $ppdbPlaceholders = [
                '{status_judul}' => 'TELAH DIVERIFIKASI',
                '{status_badge}' => '✅',
                '{status_detail}' => 'TERVERIFIKASI',
                '{pesan_verifikasi}' => 'Selamat! Pembayaran PPDB SMK Wisata Indonesia telah *BERHASIL DIVERIFIKASI*.',
                '{tahap_selanjutnya}' => "1. *TUNGGU VERIFIKASI FINAL*\n   Data Anda akan diverifikasi oleh admin\n\n2. *PANTAU PENGUMUMAN*\n   Lihat hasil seleksi via sistem\n\n3. *DAFTAR ULANG*\n   Lakukan daftar ulang jika diterima",
                '{pesan_penutup}' => '*Proses pendaftaran Anda sedang berjalan!* 📝',
                '{total_biaya}' => number_format($totalBiayaPPDB, 0, ',', '.'),
                '{total_dibayar}' => number_format($totalDibayar, 0, ',', '.'),
                '{sisa_pembayaran}' => number_format($sisaPembayaran, 0, ',', '.')
            ];

            // Sesuaikan berdasarkan status pembayaran
            if ($statusPembayaran === 'lunas') {
                $ppdbPlaceholders = [
                    '{status_judul}' => 'TELAH LUNAS',
                    '{status_badge}' => '✅',
                    '{status_detail}' => 'LUNAS & TERVERIFIKASI',
                    '{pesan_verifikasi}' => 'Selamat! Pembayaran PPDB SMK Wisata Indonesia telah *DILUNASI SEPENUHNYA*.',
                    '{tahap_selanjutnya}' => "1. *TUNGGU VERIFIKASI FINAL*\n   Data Anda akan diverifikasi oleh admin\n\n2. *PANTAU PENGUMUMAN*\n   Lihat hasil seleksi via sistem\n\n3. *DAFTAR ULANG*\n   Lakukan daftar ulang jika diterima",
                    '{pesan_penutup}' => '*Proses pendaftaran Anda hampir selesai!* 🎓',
                    '{total_biaya}' => number_format($totalBiayaPPDB, 0, ',', '.'),
                    '{total_dibayar}' => number_format($totalDibayar, 0, ',', '.'),
                    '{sisa_pembayaran}' => number_format($sisaPembayaran, 0, ',', '.')
                ];
            } elseif ($statusPembayaran === 'belum_lunas') {
                $ppdbPlaceholders = [
                    '{status_judul}' => 'TELAH DIVERIFIKASI',
                    '{status_badge}' => '✅',
                    '{status_detail}' => 'TERVERIFIKASI (Belum Lunas)',
                    '{pesan_verifikasi}' => 'Selamat! Pembayaran PPDB SMK Wisata Indonesia telah *BERHASIL DIVERIFIKASI*.',
                    '{tahap_selanjutnya}' => "1. *LANJUTKAN PEMBAYARAN*\n   Lunasi sisa pembayaran: Rp " . number_format($sisaPembayaran, 0, ',', '.') . "\n\n2. *TUNGGU VERIFIKASI FINAL*\n   Data Anda akan diverifikasi oleh admin\n\n3. *PANTAU PENGUMUMAN*\n   Lihat hasil seleksi via sistem",
                    '{pesan_penutup}' => '*Segera lunasi pembayaran untuk menyelesaikan proses!* 💰',
                    '{total_biaya}' => number_format($totalBiayaPPDB, 0, ',', '.'),
                    '{total_dibayar}' => number_format($totalDibayar, 0, ',', '.'),
                    '{sisa_pembayaran}' => number_format($sisaPembayaran, 0, ',', '.')
                ];
            }

            return $ppdbPlaceholders;

        } catch (\Exception $e) {
            Log::error('Error getPPDBPlaceholders: ' . $e->getMessage());
            return [];
        }
    }

    private function getFormulirPlaceholders()
    {
        return [
            '{status_judul}' => 'TELAH DIVERIFIKASI',
            '{status_badge}' => '✅',
            '{status_detail}' => 'LUNAS & TERVERIFIKASI',
            '{pesan_verifikasi}' => 'Selamat! Pembayaran formulir PPDB SMK Wisata Indonesia telah *BERHASIL DIVERIFIKASI*.',
            '{tahap_selanjutnya}' => "1. *LENGKAPI FORMULIR*\n   Isi data formulir pendaftaran dengan lengkap\n\n2. *PEMBAYARAN PPDB*\n   Lakukan pembayaran biaya PPDB\n\n3. *TUNGGU PENGUMUMAN*\n   Pantau hasil seleksi via sistem",
            '{pesan_penutup}' => '*Lanjutkan pengisian formulir untuk menyelesaikan pendaftaran!* 📝'
        ];
    }

    public function downloadBukti($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        
        if (!$pembayaran->bukti_pembayaran) {
            return redirect()->back()->with('error', 'Bukti pembayaran tidak ditemukan');
        }

        $path = storage_path('app/public/' . $pembayaran->bukti_pembayaran);
        
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File bukti pembayaran tidak ditemukan');
        }

        return response()->download($path);
    }

    public function show($id)
    {
        $pembayaran = Pembayaran::with(['user', 'verifier', 'dataSiswa'])->findOrFail($id);
        return view('admin.verifikasi-pembayaran.show', compact('pembayaran'));
    }

    public function destroy($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        
        // Hapus file bukti pembayaran jika ada
        if ($pembayaran->bukti_pembayaran) {
            Storage::delete('public/' . $pembayaran->bukti_pembayaran);
        }

        ActivityLog::logManual(
                "Menghapus pembayaran #{$pembayaran->id} - {$pembayaran->dataSiswa->nama_lengkap}",
                'delete'
            );
        
        $pembayaran->delete();

        return redirect()->route('verifikasi-pembayaran.index')
            ->with('success', 'Data pembayaran berhasil dihapus');
    }
}