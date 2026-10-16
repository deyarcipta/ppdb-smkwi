<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSiswa;
use App\Models\DataSiswa;
use App\Models\GelombangPendaftaran;
use App\Models\Jurusan;
use App\Models\TahunAjaran;
use App\Models\MasterBiaya;
use App\Models\TemplatePesan;
use App\Models\WhatsAppSession;
use App\Models\PengaturanAplikasi;
use App\Models\ActivityLog;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsappBlastController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Tampilkan halaman Broadcast / Blast WhatsApp
     */
    public function index()
    {
        $tahunAjaranList = TahunAjaran::orderBy('id', 'desc')->get();
        $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        $gelombangList = GelombangPendaftaran::with('tahunAjaran')->orderBy('id', 'asc')->get();
        $jurusanList = Jurusan::orderBy('nama_jurusan', 'asc')->get();
        $templatePesanList = TemplatePesan::orderBy('id', 'desc')->get();
        $waSessions = WhatsAppSession::where('is_active', true)->get();
        $pengaturan = PengaturanAplikasi::getSettings();

        // Hitung total penerima default (tahun ajaran aktif)
        $defaultCount = UserSiswa::where('status_akun', 'aktif')->count();

        return view('admin.whatsapp.blast', compact(
            'tahunAjaranList',
            'activeTahunAjaran',
            'gelombangList',
            'jurusanList',
            'templatePesanList',
            'waSessions',
            'pengaturan',
            'defaultCount'
        ));
    }

    /**
     * Helper untuk mengambil dan memfilter data siswa berdasarkan kriteria
     */
    protected function getFilteredStudents(Request $request)
    {
        $tahunAjaranId = $request->get('tahun_ajaran_id');
        $gelombangId = $request->get('gelombang_id');
        $jurusanId = $request->get('jurusan_id');
        $statusPendaftar = $request->get('status_pendaftar', 'semua');
        $statusPembayaran = $request->get('status_pembayaran', 'semua');

        // Master Biaya per gelombang & default
        $defaultTotalBiaya = MasterBiaya::where('status', 1)->sum('total_biaya');
        $totalBiayaPerGelombang = MasterBiaya::where('status', 1)
            ->get()
            ->groupBy('gelombang_id')
            ->map(function ($items) {
                return $items->sum('total_biaya');
            });

        // Query akun siswa yang aktif
        $query = UserSiswa::with([
            'dataSiswa.gelombang.tahunAjaran',
            'dataSiswa.jurusan',
            'pembayaran' => function ($q) {
                $q->orderBy('created_at', 'desc');
            }
        ])->where('status_akun', 'aktif');

        // Filter Tahun Ajaran
        if (!empty($tahunAjaranId)) {
            $query->whereHas('dataSiswa.gelombang', function ($q) use ($tahunAjaranId) {
                $q->where('tahun_ajaran_id', $tahunAjaranId);
            });
        }

        // Filter Gelombang
        if (!empty($gelombangId)) {
            $query->whereHas('dataSiswa', function ($q) use ($gelombangId) {
                $q->where('gelombang_id', $gelombangId);
            });
        }

        // Filter Jurusan
        if (!empty($jurusanId)) {
            $query->whereHas('dataSiswa', function ($q) use ($jurusanId) {
                $q->where('jurusan_id', $jurusanId);
            });
        }

        // Filter Status Pendaftar (diterima, ditolak, pending)
        if (!empty($statusPendaftar) && $statusPendaftar !== 'semua') {
            $query->whereHas('dataSiswa', function ($q) use ($statusPendaftar) {
                if ($statusPendaftar === 'pending') {
                    $q->where(function ($sub) {
                        $sub->whereNull('status_pendaftar')
                            ->orWhere('status_pendaftar', 'pending')
                            ->orWhere('status_pendaftar', '');
                    });
                } else {
                    $q->where('status_pendaftar', $statusPendaftar);
                }
            });
        }

        $allStudents = $query->orderBy('id', 'desc')->get();

        // Filter lanjutan Status Pembayaran & perhitungan tagihan
        $filtered = $allStudents->filter(function ($siswa) use ($statusPembayaran, $totalBiayaPerGelombang, $defaultTotalBiaya) {
            $ds = $siswa->dataSiswa;
            $gelId = $ds->gelombang_id ?? null;
            $targetBiaya = ($gelId && isset($totalBiayaPerGelombang[$gelId]) && $totalBiayaPerGelombang[$gelId] > 0)
                ? $totalBiayaPerGelombang[$gelId]
                : $defaultTotalBiaya;

            $totalBayar = $siswa->pembayaran ? $siswa->pembayaran->where('status', 'diverifikasi')->sum('jumlah') : 0;
            $hasPending = $siswa->pembayaran ? $siswa->pembayaran->where('status', 'pending')->count() > 0 : false;

            // Tentukan status bayar siswa
            $statusBayar = 'belum_bayar';
            if ($totalBayar >= $targetBiaya && $targetBiaya > 0) {
                $statusBayar = 'lunas';
            } elseif ($totalBayar > 0) {
                $statusBayar = 'belum_lunas';
            }

            // Simpan properti terhitung ke model sementara
            $siswa->calculated_target_biaya = $targetBiaya;
            $siswa->calculated_total_bayar = $totalBayar;
            $siswa->calculated_sisa_tagihan = max(0, $targetBiaya - $totalBayar);
            $siswa->calculated_status_bayar = $statusBayar;
            $siswa->calculated_has_pending = $hasPending;

            if (empty($statusPembayaran) || $statusPembayaran === 'semua') {
                return true;
            }

            if ($statusPembayaran === 'belum_bayar') {
                return $totalBayar == 0;
            }

            if ($statusPembayaran === 'belum_lunas') {
                return $totalBayar > 0 && $totalBayar < $targetBiaya;
            }

            if ($statusPembayaran === 'lunas') {
                return $totalBayar >= $targetBiaya && $targetBiaya > 0;
            }

            if ($statusPembayaran === 'menunggu_verifikasi') {
                return $hasPending;
            }

            return true;
        });

        return $filtered->values();
    }

    /**
     * AJAX Preview Target Penerima
     */
    public function preview(Request $request)
    {
        try {
            $students = $this->getFilteredStudents($request);
            $targetPenerima = $request->get('target_penerima', 'siswa');

            $validCount = 0;
            $receivers = [];

            foreach ($students as $siswa) {
                $ds = $siswa->dataSiswa;
                $phoneSiswa = $ds->no_hp ?? null;
                $phoneOrtu = $ds->no_hp_ibu ?: ($ds->no_hp_ayah ?: ($ds->no_hp_wali ?: null));

                $targetPhones = [];
                if ($targetPenerima === 'siswa') {
                    if ($phoneSiswa) $targetPhones[] = $phoneSiswa;
                } elseif ($targetPenerima === 'orang_tua') {
                    if ($phoneOrtu) $targetPhones[] = $phoneOrtu;
                } elseif ($targetPenerima === 'semua') {
                    if ($phoneSiswa) $targetPhones[] = $phoneSiswa;
                    if ($phoneOrtu && $phoneOrtu !== $phoneSiswa) $targetPhones[] = $phoneOrtu;
                }

                $hasValidPhone = count($targetPhones) > 0;
                if ($hasValidPhone) {
                    $validCount++;
                }

                // Masukkan sampel preview maksimal 100 data agar respons cepat
                if (count($receivers) < 100) {
                    $receivers[] = [
                        'id' => $siswa->id,
                        'no_daftar' => $siswa->username ?? '-',
                        'no_pendaftaran' => $ds->no_pendaftaran ?? $siswa->username,
                        'nama' => $ds->nama_lengkap ?? $siswa->name,
                        'gelombang' => $ds->gelombang->nama_gelombang ?? '-',
                        'jurusan' => $ds->jurusan->nama_jurusan ?? '-',
                        'no_hp_siswa' => $phoneSiswa ?? '-',
                        'no_hp_ortu' => $phoneOrtu ?? '-',
                        'phones_to_send' => implode(', ', $targetPhones),
                        'has_valid_phone' => $hasValidPhone,
                        'total_bayar' => 'Rp ' . number_format($siswa->calculated_total_bayar ?? 0, 0, ',', '.'),
                        'total_biaya' => 'Rp ' . number_format($siswa->calculated_target_biaya ?? 0, 0, ',', '.'),
                        'sisa_tagihan' => 'Rp ' . number_format($siswa->calculated_sisa_tagihan ?? 0, 0, ',', '.'),
                        'status_bayar' => $siswa->calculated_status_bayar ?? 'belum_bayar',
                        'status_pendaftar' => $ds->status_pendaftar ?? 'pending',
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'total_target' => $students->count(),
                'total_valid_phone' => $validCount,
                'receivers' => $receivers,
            ]);

        } catch (\Exception $e) {
            Log::error('Error WA Blast Preview: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat pratinjau penerima: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eksekusi Kirim Broadcast / Blast WhatsApp
     */
    public function send(Request $request)
    {
        $request->validate([
            'pesan' => 'required|string|min:5',
            'target_penerima' => 'required|in:siswa,orang_tua,semua',
        ]);

        $pesanTemplate = $request->pesan;
        $sessionId = $request->session_id && $request->session_id !== 'auto' ? $request->session_id : null;
        
        $jenisBlast = $request->jenis_blast ?: null;
        if (!$jenisBlast) {
            $pesanLower = strtolower($pesanTemplate);
            if (str_contains($pesanLower, 'tagihan') || str_contains($pesanLower, 'pembayaran') || str_contains($pesanLower, 'biaya')) {
                $jenisBlast = 'Pengingat Pembayaran';
            } elseif (str_contains($pesanLower, 'diterima') || str_contains($pesanLower, 'lulus')) {
                $jenisBlast = 'Pemberitahuan Kelulusan';
            } else {
                $jenisBlast = 'Pengumuman / Informasi';
            }
        }

        $pengaturan = PengaturanAplikasi::getSettings();
        $namaSekolah = $pengaturan->nama_sekolah ?? 'SMK Wikrama';
        $tipeAplikasi = $pengaturan->tipe_aplikasi ?? 'PPDB';
        $linkLogin = route('siswa.login');
        $noAdmin = $pengaturan->telepon ?? '-';

        $students = $this->getFilteredStudents($request);

        if ($students->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data siswa yang memenuhi kriteria filter.');
        }

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($students as $siswa) {
            $ds = $siswa->dataSiswa;
            $nama = $ds->nama_lengkap ?? ($siswa->name ?? 'Siswa');
            $noPendaftaran = $ds->no_pendaftaran ?? ($siswa->username ?? '-');
            $noDaftar = $siswa->username ?? '-';
            $gelombang = $ds->gelombang->nama_gelombang ?? '-';
            $jurusan = $ds->jurusan->nama_jurusan ?? '-';
            $asalSekolah = $ds->asal_sekolah ?? '-';

            $totalBiaya = 'Rp ' . number_format($siswa->calculated_target_biaya ?? 0, 0, ',', '.');
            $totalBayar = 'Rp ' . number_format($siswa->calculated_total_bayar ?? 0, 0, ',', '.');
            $sisaTagihan = 'Rp ' . number_format($siswa->calculated_sisa_tagihan ?? 0, 0, ',', '.');

            $statusBayarLabel = 'Belum Bayar';
            if (($siswa->calculated_status_bayar ?? '') === 'lunas') {
                $statusBayarLabel = 'Lunas';
            } elseif (($siswa->calculated_status_bayar ?? '') === 'belum_lunas') {
                $statusBayarLabel = 'Belum Lunas (Masih ada sisa pembayaran)';
            }

            $statusPendaftarLabel = 'Menunggu Verifikasi/Pengumuman';
            if (($ds->status_pendaftar ?? '') === 'diterima') {
                $statusPendaftarLabel = 'DITERIMA';
            } elseif (($ds->status_pendaftar ?? '') === 'ditolak') {
                $statusPendaftarLabel = 'DITOLAK';
            }

            // Ganti tag / variabel dinamis
            $personalizedMessage = str_replace(
                [
                    '{nama}',
                    '{no_pendaftaran}',
                    '{no_daftar}',
                    '{username}',
                    '{gelombang}',
                    '{jurusan}',
                    '{asal_sekolah}',
                    '{total_biaya}',
                    '{total_bayar}',
                    '{total_dibayar}',
                    '{sisa_tagihan}',
                    '{sisa_pembayaran}',
                    '{status_pembayaran}',
                    '{status_pendaftar}',
                    '{nama_sekolah}',
                    '{tipe_aplikasi}',
                    '{link_login}',
                    '{url_sistem}',
                    '{no_admin}'
                ],
                [
                    $nama,
                    $noPendaftaran,
                    $noDaftar,
                    $noDaftar,
                    $gelombang,
                    $jurusan,
                    $asalSekolah,
                    $totalBiaya,
                    $totalBayar,
                    $totalBayar,
                    $sisaTagihan,
                    $sisaTagihan,
                    $statusBayarLabel,
                    $statusPendaftarLabel,
                    $namaSekolah,
                    $tipeAplikasi,
                    $linkLogin,
                    $linkLogin,
                    $noAdmin
                ],
                $pesanTemplate
            );

            // Kumpulkan nomor tujuan
            $phoneSiswa = $ds->no_hp ?? null;
            $phoneOrtu = $ds->no_hp_ibu ?: ($ds->no_hp_ayah ?: ($ds->no_hp_wali ?: null));

            $phones = [];
            if ($targetPenerima === 'siswa' && $phoneSiswa) {
                $phones[] = $phoneSiswa;
            } elseif ($targetPenerima === 'orang_tua' && $phoneOrtu) {
                $phones[] = $phoneOrtu;
            } elseif ($targetPenerima === 'semua') {
                if ($phoneSiswa) $phones[] = $phoneSiswa;
                if ($phoneOrtu && $phoneOrtu !== $phoneSiswa) $phones[] = $phoneOrtu;
            }

            if (empty($phones)) {
                $skippedCount++;
                continue;
            }

            foreach ($phones as $phone) {
                $this->whatsappService->sendMessage(
                    $phone,
                    $personalizedMessage,
                    'Broadcast ' . $jenisBlast,
                    $sessionId
                );
                $sentCount++;
            }
        }

        // Catat di ActivityLog jika ada
        if (class_exists(ActivityLog::class)) {
            try {
                ActivityLog::create([
                    'admin_id' => auth()->id() ?? 1,
                    'action' => 'Kirim Broadcast WhatsApp',
                    'description' => "Broadcast WhatsApp ({$jenisBlast}) berhasil dijadwalkan ke {$sentCount} nomor penerima. ({$skippedCount} siswa dilewati karena nomor tidak ada).",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
            } catch (\Exception $e) {
                // Abaikan jika table error
            }
        }

        return redirect()->route('whatsapp.blast.index')->with(
            'success',
            "Broadcast berhasil dikirim ke antrean Queue! Sebanyak {$sentCount} pesan WhatsApp sedang diproses secara bertahap dengan Anti-Ban Delay." . 
            ($skippedCount > 0 ? " ({$skippedCount} siswa dilewati karena tidak memiliki nomor WhatsApp)." : "")
        );
    }
}
