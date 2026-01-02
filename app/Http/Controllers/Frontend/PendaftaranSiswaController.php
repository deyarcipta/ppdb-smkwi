<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\UserSiswa;
use App\Models\DataSiswa;
use App\Models\GelombangPendaftaran;
use App\Models\TahunAjaran;
use App\Models\DataSmp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Exception;

class PendaftaranSiswaController extends Controller
{
    protected $whatsappService;

    public function __construct()
    {
        $this->whatsappService = new WhatsAppService();
    }

    public function showForm()
    {
        $dataSmp = DataSmp::all();
        return view('frontend.pendaftaran', compact('dataSmp'));
    }

    public function store(Request $request)
    {
        // 1. CEK STATUS PENDAFTARAN TERLEBIH DAHULU
        $pendaftaranStatus = $this->checkPendaftaranStatus();
        
        if (!$pendaftaranStatus['bisa_daftar']) {
            return response()->json([
                'success' => false,
                'message' => $pendaftaranStatus['message'],
                'errors' => [
                    'gelombang' => [$pendaftaranStatus['message']]
                ]
            ], 422);
        }

        // 2. VALIDASI DATA
        $validated = $request->validate([
            // Data Pribadi
            'nisn' => 'required|string|max:20|unique:data_siswa,nisn',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'email' => 'required|email|unique:users_siswa,email',
            'no_hp' => 'required|string|max:15',
            'asal_sekolah' => 'required|string|max:255',
            
            // Data Orang Tua
            'no_hp_ayah' => 'required|string|max:15',
            'no_hp_ibu' => 'required|string|max:15',
            
            // Referensi
            'referensi' => 'required|string|max:255',
        ]);
        
        DB::beginTransaction();

        try {
            // 3. AMBIL GELOMBANG AKTIF DAN TAHUN AJARAN
            $gelombangAktif = $this->getGelombangAktif();
            
            if (!$gelombangAktif) {
                throw new \Exception('Tidak ada gelombang pendaftaran yang aktif');
            }

            // Cari tahun ajaran aktif
            $tahunAjaranAktif = TahunAjaran::where('status', 'aktif')->first();
            
            if (!$tahunAjaranAktif) {
                throw new \Exception('Tidak ada tahun ajaran yang aktif');
            }

            // Generate username otomatis
            $username = $this->generateUsername();
            
            // Generate password default
            $password = Hash::make('password123');

            // 4. SIMPAN KE TABEL USERS_SISWA
            $user = UserSiswa::create([
                'username' => $username,
                'email' => $validated['email'],
                'password' => $password,
                'role' => 'siswa',
                'status_akun' => 'aktif',
            ]);

            // 5. SIMPAN KE TABEL DATA_SISWA DENGAN GELOMBANG_ID DAN TAHUN_AJARAN_ID
            $dataSiswa = DataSiswa::create([
                'user_id' => $user->id,
                'gelombang_id' => $gelombangAktif->id,
                'tahun_ajaran_id' => $tahunAjaranAktif->id,
                'jurusan_id' => $this->getDefaultJurusanId(),
                
                // Data Pribadi
                'nisn' => $validated['nisn'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'no_hp' => $validated['no_hp'],
                'asal_sekolah' => $validated['asal_sekolah'],
                'referensi' => $validated['referensi'],
                // 'ket_pembayaran' => 'Belum Bayar',
                
                // Data Orang Tua
                'no_hp_ayah' => $validated['no_hp_ayah'],
                'no_hp_ibu' => $validated['no_hp_ibu'],
                
                // Status
                'status_pendaftar' => 'pending',
                'is_form_completed' => false,
            ]);

            // 6. KIRIM WHATSAPP NOTIFICATION
            $whatsappResult = $this->sendWelcomeMessage(
                $validated['no_hp'], 
                $username, 
                $validated['nama_lengkap'],
                $gelombangAktif
            );

            DB::commit();

            // Response sukses
            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil!',
                'data' => [
                    'username' => $username,
                    'user_id' => $user->id,
                    'no_hp' => $validated['no_hp'],
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Pendaftaran error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat pendaftaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * CEK STATUS PENDAFTARAN DENGAN DEBUG
     */
    private function checkPendaftaranStatus()
    {
        try {
            // Debug: Lihat semua gelombang aktif di database
            $semuaGelombang = GelombangPendaftaran::where('status', 'aktif')->get();
            Log::info("=== DEBUG GELOMBANG PENDAFTARAN ===");
            Log::info("Total gelombang aktif: " . $semuaGelombang->count());
            
            foreach ($semuaGelombang as $gelombang) {
                Log::info("Gelombang: " . $gelombang->nama_gelombang . 
                         " | Status: " . $gelombang->status .
                         " | Mulai: " . ($gelombang->tanggal_mulai ? $gelombang->tanggal_mulai->format('Y-m-d H:i:s') : 'null') .
                         " | Selesai: " . ($gelombang->tanggal_selesai ? $gelombang->tanggal_selesai->format('Y-m-d H:i:s') : 'null'));
            }

            // Cek apakah ada gelombang aktif
            $gelombangAktif = $this->getGelombangAktif();
            
            Log::info("Gelombang yang ditemukan oleh getGelombangAktif(): " . ($gelombangAktif ? $gelombangAktif->nama_gelombang : 'TIDAK ADA'));
            
            if (!$gelombangAktif) {
                Log::info("Tidak ada gelombang aktif yang memenuhi kriteria");
                return [
                    'bisa_daftar' => false,
                    'message' => 'Maaf, saat ini tidak ada gelombang pendaftaran yang aktif.',
                    'status' => 'tutup'
                ];
            }

            // Cek tanggal pendaftaran
            $sekarang = now();
            $tanggalMulai = $gelombangAktif->tanggal_mulai ? Carbon::parse($gelombangAktif->tanggal_mulai) : null;
            $tanggalSelesai = $gelombangAktif->tanggal_selesai ? Carbon::parse($gelombangAktif->tanggal_selesai) : null;

            // Debug: Log informasi tanggal
            Log::info("=== CEK TANGGAL ===");
            Log::info("Nama Gelombang: " . $gelombangAktif->nama_gelombang);
            Log::info("Status: " . $gelombangAktif->status);
            Log::info("Tahun Ajaran:" . $gelombangAktif->tahunAjaran->nama ?? 'null');
            Log::info("Tanggal Mulai: " . ($tanggalMulai ? $tanggalMulai->format('Y-m-d H:i:s') : 'null'));
            Log::info("Tanggal Selesai: " . ($tanggalSelesai ? $tanggalSelesai->format('Y-m-d H:i:s') : 'null'));
            Log::info("Sekarang: " . $sekarang->format('Y-m-d H:i:s'));

            if ($tanggalMulai && $sekarang->lt($tanggalMulai)) {
                Log::info("Pendaftaran belum dibuka - tanggal mulai masih di masa depan");
                return [
                    'bisa_daftar' => false,
                    'message' => "Pendaftaran akan dibuka pada " . $tanggalMulai->format('d F Y'),
                    'status' => 'belum_dibuka'
                ];
            }

            if ($tanggalSelesai && $sekarang->gt($tanggalSelesai)) {
                Log::info("Pendaftaran sudah ditutup - tanggal selesai sudah lewat");
                return [
                    'bisa_daftar' => false,
                    'message' => "Pendaftaran telah ditutup pada " . $tanggalSelesai->format('d F Y'),
                    'status' => 'sudah_ditutup'
                ];
            }

            // Semua kondisi terpenuhi, bisa daftar
            Log::info("Pendaftaran bisa dilakukan - semua kondisi terpenuhi");
            return [
                'bisa_daftar' => true,
                'message' => 'Pendaftaran sedang dibuka.',
                'status' => 'buka'
            ];

        } catch (\Exception $e) {
            Log::error('Error checkPendaftaranStatus: ' . $e->getMessage());
            
            return [
                'bisa_daftar' => false,
                'message' => 'Terjadi kesalahan saat memeriksa status pendaftaran.',
                'status' => 'error'
            ];
        }
    }

    /**
     * GET GELOMBANG AKTIF - QUERY SEDERHANA
     */
    private function getGelombangAktif()
    {
        // Cari gelombang dengan status aktif saja
        // Pengecekan tanggal dilakukan di checkPendaftaranStatus()
        return GelombangPendaftaran::where('status', 'aktif')->first();
    }

    /**
     * GET DEFAULT JURUSAN ID
     */
    private function getDefaultJurusanId()
    {
        return null;
    }

    /**
     * KIRIM PESAN WELCOME WHATSAPP
     */
    private function sendWelcomeMessage($phoneNumber, $username, $namaLengkap, $gelombangAktif)
    {
        try {
            $template = \App\Models\TemplatePesan::where('jenis_pesan', 'pendaftaran_baru')
                ->where('status', true)
                ->first();

            if (!$template) {
                Log::warning('Template pesan "pendaftaran_baru" tidak ditemukan atau nonaktif.');
                return ['success' => false, 'error' => 'Template pesan tidak ditemukan'];
            }

            $tahunAjaran = $gelombangAktif->tahunAjaran->nama ?? $gelombangAktif->nama;

            $placeholders = [
                '{nama}' => $namaLengkap,
                '{username}' => $username,
                '{password}' => 'password123',
                '{tahun_ajaran}' => $tahunAjaran,
                '{gelombang}' => $gelombangAktif->nama_gelombang,
                '{rekening}' => '1234567890',
                '{an}' => 'SMK Wisata Indonesia',
                '{no_admin}' => '0852-1815-0720',
                '{url_sistem}' => 'https://ppdb.wisataindonesia.sch.id/siswa',
            ];

            $message = strtr($template->isi_pesan, $placeholders);

            $jenisPesan = strtr($template->judul, $placeholders);

            // Kirim pesan WhatsApp
            $result = $this->whatsappService->sendMessage($phoneNumber, $message, $jenisPesan);

        } catch (\Exception $e) {
            Log::error('Error sendWelcomeMessage: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * METHOD UNTUK CHECK KETERSEDIAAN NISN
     */
    public function checkNisn($nisn)
    {
        $exists = DataSiswa::where('nisn', $nisn)->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'NISN sudah terdaftar' : 'NISN tersedia'
        ]);
    }

    /**
     * METHOD UNTUK CHECK KETERSEDIAAN EMAIL
     */
    public function checkEmail($email)
    {
        $exists = UserSiswa::where('email', $email)->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Email sudah terdaftar, isikan email yang lain' : 'Email tersedia'
        ]);
    }

    /**
     * GENERATE USERNAME OTOMATIS
     */
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
}

/**
 * WhatsApp Service Class
 */
class WhatsAppService
{
    private $client;
    
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost:3000',
            'timeout'  => 15.0,
            'verify' => false,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);
    }

    public function sendMessage($phone, $message)
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phone);
            
            Log::info("Mengirim WhatsApp ke: {$formattedPhone}");
            
            $response = $this->client->post('/send-message', [
                'json' => [
                    'phone' => $formattedPhone,
                    'message' => $message
                ]
            ]);
            
            $result = json_decode($response->getBody(), true);
            Log::info("WhatsApp berhasil dikirim: " . json_encode($result));
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Gagal mengirim WhatsApp: ' . $e->getMessage());
            return [
                'success' => false, 
                'error' => $e->getMessage(),
                'note' => 'Pastikan WhatsApp bot sedang running di localhost:3000'
            ];
        }
    }

    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        if (substr($phone, 0, 3) === '+62') {
            $phone = '62' . substr($phone, 3);
        }
        
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }
}