<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSiswa;
use App\Models\PembayaranSiswa;
use App\Models\MasterBiaya;
use App\Models\TemplatePesan;
use App\Models\ActivityLog;
use App\Services\WhatsAppService;
use App\Models\InfoPembayaran;
use App\Models\DataSiswa;
use App\Models\GelombangPendaftaran;
use App\Models\Jurusan;
use App\Models\DataSmp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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

        // Ambil gelombang dan jurusan aktif untuk modal tambah
        $gelombangs = GelombangPendaftaran::where('status', 'aktif')->get();
        $jurusans = Jurusan::where('status', 1)->get();
        $dataSmp = DataSmp::all();

        return view('admin.data-terverifikasi.index', compact('data', 'totalBiaya', 'gelombangs', 'jurusans', 'dataSmp'));
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

    public function resetPassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users_siswa,id'
        ]);

        try {
            $user = UserSiswa::findOrFail($request->user_id);
            
            // Reset password ke random 6 digit
            $newPassword = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $user->password = Hash::make($newPassword);
            $user->password_plain = $newPassword;
            $user->save();

            return back()->with([
                'success' => 'Password berhasil direset',
                'password_reset' => true,
                'new_password' => $newPassword
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mereset password: ' . $e->getMessage());
        }
    }
    
public function kirimUlang($id)
{
    try {

        $user = UserSiswa::with(['dataSiswa.gelombang.tahunAjaran'])
            ->findOrFail($id);

        if (!$user->dataSiswa) {
            return back()->with('error', 'Data siswa tidak ditemukan');
        }

        $template = TemplatePesan::where('jenis_pesan', 'pendaftaran_baru')
            ->where('status', true)
            ->first();

        if (!$template) {
            return back()->with('error', 'Template pesan tidak ditemukan');
        }

        $dataSiswa = $user->dataSiswa;
        $gelombang = $dataSiswa->gelombang ?? null;

        $info = InfoPembayaran::first();

        $tahunAjaran = optional($gelombang?->tahunAjaran)->nama
            ?? ($gelombang->nama_gelombang ?? '-');

        $placeholders = [
            '{nama}'         => $dataSiswa->nama_lengkap ?? '-',
            '{username}'     => $user->username ?? '-',
            '{password}'     => 'password123',
            '{tahun_ajaran}' => $tahunAjaran,
            '{gelombang}'    => $gelombang->nama_gelombang ?? '-',

            '{rekening}'     => $info->nomor_rekening ?? '-',
            '{an}'           => $info->atas_nama ?? '-',
            '{no_admin}'     => '0852-1815-0720',
            '{url_sistem}'   => 'https://ppdb.smkwisataindonesia.sch.id/siswa',
        ];

        $message = strtr($template->isi_pesan, $placeholders);
        $jenisPesan = strtr($template->judul, $placeholders);

        // kirim WA
        $result = $this->whatsappService->sendMessage(
            $dataSiswa->no_hp,
            $message,
            $jenisPesan
        );

        // ✅ jangan tampilkan JSON ke user
        if (isset($result['success']) && $result['success']) {
            return back()->with('success', 'Pesan WhatsApp berhasil dikirim ulang');
        }

        return back()->with('error', 'Gagal mengirim pesan WhatsApp');

    } catch (\Exception $e) {
        Log::error('kirimUlang error: ' . $e->getMessage());

        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

    private function sendPenerimaanMessage($phoneNumber, $dataSiswa)
    {
        try {
            $template = TemplatePesan::where('jenis_pesan', 'pendaftar_diterima')
                ->where('status', true)
                ->first();

            if (!$template) {
                Log::warning('Template pesan "pendaftar_diterima" tidak ditemukan atau nonaktif.');
                return;
            }

            $placeholders = $this->getPenerimaanPlaceholders($dataSiswa);

            $message = strtr($template->isi_pesan, $placeholders);
            $jenisPesan = strtr($template->judul, $placeholders);

            // Kirim pesan WhatsApp
            $this->whatsappService->sendMessage($phoneNumber, $message, $jenisPesan);

        } catch (\Exception $e) {
            Log::error('Error sendPenerimaanMessage: ' . $e->getMessage());
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
            '{no_admin}' => '0852-1815-0720',
            '{email_sekolah}' => "ppdb@smkwisataindonesia.sch.id",
            '{url_sistem}' => "https://ppdb.smkwisataindonesia.sch.id/siswa",
            
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

    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|max:20|unique:data_siswa,nisn',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'email' => 'required|email|unique:users_siswa,email',
            'no_hp' => 'required|string|max:15',
            'asal_sekolah' => 'required|string|max:255',
            'gelombang_id' => 'required|exists:gelombang_pendaftaran,id',
            'jurusan_id' => 'required|exists:jurusans,id',
            'no_hp_ayah' => 'nullable|string|max:15',
            'no_hp_ibu' => 'nullable|string|max:15',
            'referensi' => 'required|string|max:255',
        ], [
            'nisn.unique' => 'NISN ini sudah terdaftar di sistem.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
        ]);

        DB::beginTransaction();
        try {
            $gelombang = GelombangPendaftaran::findOrFail($request->gelombang_id);
            $tahunAjaranId = $gelombang->tahun_ajaran_id;

            // Generate username otomatis
            $username = $this->generateUsername();
            
            // Generate password default (random 6 digit)
            $plainPassword = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $password = Hash::make($plainPassword);

            // 1. Simpan ke tabel users_siswa
            $user = UserSiswa::create([
                'username' => $username,
                'email' => $request->email,
                'password' => $password,
                'password_plain' => $plainPassword,
                'role' => 'siswa',
                'status_akun' => 'aktif',
            ]);

            // Generate nomor pendaftaran
            $noPendaftaran = $this->generateNoPendaftaran($request->jurusan_id, $request->gelombang_id, $username);

            // 2. Simpan ke tabel data_siswa
            $dataSiswa = DataSiswa::create([
                'user_id' => $user->id,
                'gelombang_id' => $request->gelombang_id,
                'tahun_ajaran_id' => $tahunAjaranId,
                'jurusan_id' => $request->jurusan_id,
                'no_pendaftaran' => $noPendaftaran,
                'nisn' => $request->nisn,
                'nama_lengkap' => $request->nama_lengkap,
                'jenis_kelamin' => $request->jenis_kelamin,
                'no_hp' => $request->no_hp,
                'asal_sekolah' => $request->asal_sekolah,
                'no_hp_ayah' => $request->no_hp_ayah,
                'no_hp_ibu' => $request->no_hp_ibu,
                'referensi' => $request->referensi,
                'status_pendaftar' => 'pending',
                'is_form_completed' => true,
            ]);

            // Kirim pesan WhatsApp Selamat Datang
            $this->sendWelcomeMessage($request->no_hp, $username, $request->nama_lengkap, $gelombang, $plainPassword);

            DB::commit();

            ActivityLog::logManual(
                "Mendaftarkan siswa secara manual #{$dataSiswa->id} - {$dataSiswa->nama_lengkap} (Username: {$username})",
                'create'
            );

            return back()->with('success', 'Siswa berhasil didaftarkan secara manual dengan username: ' . $username . ' dan password: password123');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Manual Pendaftaran error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mendaftarkan siswa: ' . $e->getMessage());
        }
    }

    private function generateUsername()
    {
        $tahun = date('Y');
        $prefix = "PPDB{$tahun}";
        
        $lastUser = UserSiswa::where('username', 'like', $prefix . '%')
            ->orderBy('username', 'desc')
            ->first();

        if ($lastUser) {
            $lastNumber = intval(str_replace($prefix, '', $lastUser->username));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        
        return $prefix . $formattedNumber;
    }

    private function generateNoPendaftaran($jurusanId, $gelombangId, $username)
    {
        try {
            $jurusan = Jurusan::find($jurusanId);
            $kodeJurusan = $jurusan ? $jurusan->kode_jurusan : 'UMUM';

            $gelombang = GelombangPendaftaran::find($gelombangId);
            $tahunAjaran = $gelombang->tahunAjaran ? $gelombang->tahunAjaran->nama : date('Y');
            
            $namaGelombang = $gelombang ? $gelombang->nama_gelombang : 'Gelombang 1';
            $angkaGelombang = $this->convertToRoman($namaGelombang);

            $lastThreeDigits = substr($username, -3);

            $noPendaftaran = "PPDB/{$kodeJurusan}/{$tahunAjaran}/{$angkaGelombang}/{$lastThreeDigits}";
            return $noPendaftaran;
        } catch (\Exception $e) {
            Log::error('Error generating no_pendaftaran: ' . $e->getMessage());
            return "PPDB/UMUM/" . date('Y') . "/I/001";
        }
    }

    private function convertToRoman($namaGelombang)
    {
        preg_match('/\d+/', $namaGelombang, $matches);
        $angka = $matches[0] ?? 1;

        $romawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X'
        ];

        return $romawi[$angka] ?? 'I';
    }

    private function sendWelcomeMessage($phoneNumber, $username, $namaLengkap, $gelombang, $plainPassword)
    {
        try {
            $template = TemplatePesan::where('jenis_pesan', 'pendaftaran_baru')
                ->where('status', true)
                ->first();

            if (!$template) {
                Log::warning('Template pesan "pendaftaran_baru" tidak ditemukan atau nonaktif.');
                return;
            }

            $tahunAjaran = $gelombang->tahunAjaran->nama ?? $gelombang->nama;
            $info = InfoPembayaran::first();

            $placeholders = [
                '{nama}' => $namaLengkap,
                '{username}' => $username,
                '{password}' => $plainPassword,
                '{tahun_ajaran}' => $tahunAjaran,
                '{gelombang}' => $gelombang->nama_gelombang,
                '{rekening}' => $info->nomor_rekening ?? '-',
                '{an}' => $info->atas_nama ?? '-',
                '{no_admin}' => '0852-1815-0720',
                '{url_sistem}' => 'https://ppdb.smkwisataindonesia.sch.id/siswa',
            ];

            $message = strtr($template->isi_pesan, $placeholders);
            $jenisPesan = strtr($template->judul, $placeholders);

            // Kirim pesan WhatsApp
            $this->whatsappService->sendMessage($phoneNumber, $message, $jenisPesan);

        } catch (\Exception $e) {
            Log::error('Error sendWelcomeMessage: ' . $e->getMessage());
        }
    }
}