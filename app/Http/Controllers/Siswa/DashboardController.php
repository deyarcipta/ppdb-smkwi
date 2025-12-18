<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\InfoPembayaran;
use App\Models\Jurusan;
use App\Models\DataSiswa;
use App\Models\MasterBiaya;
use App\Models\PengaturanAplikasi;
use App\Services\ProgressService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    protected $progressService;

    public function __construct(ProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    public function index()
    {
        $user = Auth::user();
        $dataSiswa = $user->dataSiswa ?? null;

        // Ambil data dari master pembayaran dengan jenis pembayaran ppdb
        $masterPPDB = MasterBiaya::where('jenis_biaya', 'ppdb')
            ->first();
        
        // Ambil data pembayaran formulir terbaru
        $pembayaran = Pembayaran::where('user_id', $user->id)
            ->where('jenis_pembayaran', 'formulir')
            ->latest()
            ->first();

        $pembayaranPPDB = Pembayaran::where('user_id', $user->id)
            ->where('jenis_pembayaran', 'ppdb')
            ->get();

        // Tentukan apakah harus menampilkan form upload
        $showUploadForm = !$pembayaran || $pembayaran->status == 'ditolak';

        // Ambil data info pembayaran
        $infoPembayaran = InfoPembayaran::where('status', 1)->first();

        // Ambil data setting
        $pengaturan_aplikasi = PengaturanAplikasi::first();

        // Tentukan step berdasarkan progress
        $currentStep = $this->getCurrentStep($pembayaran, $dataSiswa);
        
        // Tentukan status text dan color
        list($statusText, $statusColor) = $this->getStatusInfo($currentStep, $pembayaran, $dataSiswa);

        return view('siswa.dashboard', compact(
            'user', 
            'dataSiswa', 
            'masterPPDB',
            'pembayaran',
            'pembayaranPPDB',
            'infoPembayaran',
            'pengaturan_aplikasi',
            'showUploadForm',
            'currentStep', 
            'statusText', 
            'statusColor'
        ));
    }

    /**
     * Handle upload bukti pembayaran formulir
     */
    public function uploadBuktiFormulir(Request $request)
    {
        // Validasi dengan pesan custom
        $validator = Validator::make($request->all(), [
            'metode_pembayaran' => 'required|string',
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'keterangan' => 'nullable|string|max:500'
        ], [
            'bukti_pembayaran.required' => 'Bukti pembayaran harus diupload.',
            'bukti_pembayaran.file' => 'File yang diupload harus berupa file.',
            'bukti_pembayaran.mimes' => 'Format file harus JPG, JPEG, PNG, atau PDF.',
            'bukti_pembayaran.max' => 'Ukuran file tidak boleh lebih dari 2MB.',
            'keterangan.max' => 'Keterangan tidak boleh lebih dari 500 karakter.'
        ]);

        // Cek jika validasi gagal
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            $errorMessage = implode('<br>', $errorMessages);
            
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }

        try {
            $user = Auth::user();
            $dataSiswa = $user->dataSiswa; // Relasi ke tabel data_siswa
            
            // Upload file bukti pembayaran
            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                
                // Cek ukuran file lagi (double check)
                if ($file->getSize() > 2097152) { // 2MB in bytes
                    return redirect()->back()
                        ->with('error', 'Ukuran file terlalu besar. Maksimal 2MB.')
                        ->withInput();
                }

                // Cek ekstensi file
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
                $extension = strtolower($file->getClientOriginalExtension());
                
                if (!in_array($extension, $allowedExtensions)) {
                    return redirect()->back()
                        ->with('error', 'Format file tidak didukung. Gunakan JPG, JPEG, PNG, atau PDF.')
                        ->withInput();
                }

                $fileName = 'bukti_formulir_' . time() . '_' . $user->username . '.' . $extension;
                $filePath = $file->storeAs('bukti_pembayaran/formulir', $fileName, 'public');
            }

            $tanggalSekarang = now()->format('Y-m-d H:i:s');

            // Data untuk pembayaran
            $pembayaranData = [
                'user_id' => $user->id,
                'no_pendaftaran' => $user->username,
                'nama_siswa' => $dataSiswa->nama_lengkap ?? 'Belum diisi', // Ambil dari data_siswa
                'jenis_pembayaran' => 'formulir',
                'bukti_pembayaran' => $filePath,
                'tanggal_bayar' => $tanggalSekarang,
                'jumlah' => 100000,
                'metode_pembayaran' => $request->metode_pembayaran,
                'keterangan' => $request->keterangan,
                'status' => 'pending',
                'tanggal_pembayaran' => now()
            ];

            // Cek apakah sudah ada pembayaran sebelumnya
            $pembayaranExist = Pembayaran::where('user_id', $user->id)
                ->where('jenis_pembayaran', 'formulir')
                ->first();

            if ($pembayaranExist) {
                // Hapus file lama jika ada
                if ($pembayaranExist->bukti_pembayaran && Storage::disk('public')->exists($pembayaranExist->bukti_pembayaran)) {
                    Storage::disk('public')->delete($pembayaranExist->bukti_pembayaran);
                }

                // Update pembayaran yang sudah ada
                $pembayaranExist->update($pembayaranData);
                
                $pembayaran = $pembayaranExist;
            } else {
                // Buat record pembayaran baru
                $pembayaran = Pembayaran::create($pembayaranData);
            }

            return redirect()->route('siswa.dashboard')
                ->with('success', 'Bukti pembayaran formulir berhasil diupload! Menunggu verifikasi admin.');

        } catch (\Exception $e) {
            \Log::error('Upload bukti pembayaran error: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            
            // Cek jenis error
            $errorMessage = 'Terjadi kesalahan saat upload bukti pembayaran.';
            
            if (str_contains($e->getMessage(), 'disk')) {
                $errorMessage = 'Terjadi kesalahan pada sistem penyimpanan file.';
            } elseif (str_contains($e->getMessage(), 'SQL')) {
                $errorMessage = 'Terjadi kesalahan pada database.';
            } elseif (str_contains($e->getMessage(), 'allowed memory')) {
                $errorMessage = 'File terlalu besar untuk diproses.';
            }
            
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    /**
     * Get daftar jurusan - VERSI DIPERBAIKI dengan pengecekan status dari kedua tabel
     */
    public function getJurusan(Request $request)
    {
        try {
            Log::info('=== GET JURUSAN METHOD CALLED ===');

            // Ambil data jurusan aktif dari database dengan JOIN ke kuota_jurusan
            $jurusans = DB::table('jurusans')
                ->join('kuota_jurusan', 'jurusans.id', '=', 'kuota_jurusan.jurusan_id')
                ->join('gelombang_pendaftaran', 'kuota_jurusan.gelombang_id', '=', 'gelombang_pendaftaran.id')
                ->where('jurusans.status', 1)
                ->where('kuota_jurusan.status', 1)
                ->where('gelombang_pendaftaran.status', 'aktif')
                ->select(
                    'jurusans.id',
                    'jurusans.kode_jurusan',
                    'jurusans.nama_jurusan',
                    'jurusans.deskripsi',
                    'kuota_jurusan.kuota',
                    'kuota_jurusan.gelombang_id'
                )
                ->get();

            Log::info('Data jurusan aktif dengan kuota:', ['count' => $jurusans->count()]);

            $transformedData = [];
            foreach ($jurusans as $jurusan) {
                try {
                    // Hitung jumlah pendaftar berdasarkan jurusan dan gelombang
                    $jumlahPendaftar = DataSiswa::where('jurusan_id', $jurusan->id)
                        ->where('gelombang_id', $jurusan->gelombang_id)
                        ->count();

                    $kuota = (int) $jurusan->kuota;
                    $kuotaTersedia = max(0, $kuota - $jumlahPendaftar);

                    $transformedData[] = [
                        'id' => $jurusan->id,
                        'kode_jurusan' => $jurusan->kode_jurusan,
                        'nama_jurusan' => $jurusan->nama_jurusan,
                        'deskripsi' => $jurusan->deskripsi,
                        'kuota' => $kuota,
                        'kuota_tersedia' => $kuotaTersedia,
                        'jumlah_pendaftar' => $jumlahPendaftar,
                        'gelombang_id' => $jurusan->gelombang_id
                    ];

                    Log::info("Jurusan {$jurusan->id}", [
                        'nama' => $jurusan->nama_jurusan,
                        'kuota' => $kuota,
                        'gelombang_id' => $jurusan->gelombang_id,
                        'jumlah_pendaftar' => $jumlahPendaftar,
                        'kuota_tersedia' => $kuotaTersedia
                    ]);

                } catch (\Exception $e) {
                    Log::error("Error processing jurusan {$jurusan->id}: " . $e->getMessage());
                    
                    // Fallback untuk jurusan ini
                    $transformedData[] = [
                        'id' => $jurusan->id,
                        'kode_jurusan' => $jurusan->kode_jurusan,
                        'nama_jurusan' => $jurusan->nama_jurusan,
                        'deskripsi' => $jurusan->deskripsi,
                        'kuota' => $jurusan->kuota,
                        'kuota_tersedia' => $jurusan->kuota,
                        'jumlah_pendaftar' => 0,
                        'gelombang_id' => $jurusan->gelombang_id
                    ];
                }
            }

            // Jika tidak ada jurusan aktif, kembalikan array kosong
            if (empty($transformedData)) {
                Log::warning('Tidak ada jurusan aktif dengan kuota tersedia');
            }

            return response()->json([
                'success' => true,
                'data' => $transformedData,
                'message' => 'Data jurusan berhasil diambil'
            ]);

        } catch (\Exception $e) {
            Log::error('Get jurusan error: ' . $e->getMessage());
            
            // Fallback ke data static
            return response()->json([
                'success' => true,
                'data' => $this->getStaticJurusanData(),
                'message' => 'Data jurusan (fallback)'
            ]);
        }
    }

    /**
     * Handle pilih jurusan - VERSI DIPERBAIKI dengan pengecekan status dari kedua tabel
     */
    public function pilihJurusan(Request $request)
    {
        try {
            $request->validate([
                'jurusan_id' => 'required|exists:jurusans,id',
                'gelombang_id' => 'required|integer'
            ]);

            $user = Auth::user();
            $dataSiswa = $user->dataSiswa;

            if (!$dataSiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data siswa tidak ditemukan'
                ], 404);
            }

            // Cek apakah jurusan dan kuota aktif dengan JOIN kedua tabel
            $jurusanAktif = DB::table('jurusans')
                ->join('kuota_jurusan', 'jurusans.id', '=', 'kuota_jurusan.jurusan_id')
                ->where('jurusans.id', $request->jurusan_id)
                ->where('jurusans.status', 1)
                ->where('kuota_jurusan.gelombang_id', $request->gelombang_id)
                ->where('kuota_jurusan.status', 1)
                ->select(
                    'jurusans.id as jurusan_id',
                    'jurusans.nama_jurusan',
                    'kuota_jurusan.kuota',
                    'kuota_jurusan.gelombang_id'
                )
                ->first();

            if (!$jurusanAktif) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jurusan tidak aktif atau kuota tidak tersedia untuk gelombang ini'
                ], 404);
            }

            // Hitung jumlah pendaftar berdasarkan jurusan dan gelombang
            $jumlahPendaftar = DataSiswa::where('jurusan_id', $request->jurusan_id)
                ->where('gelombang_id', $request->gelombang_id)
                ->count();

            Log::info('Data kuota', [
                'jurusan_id' => $request->jurusan_id,
                'gelombang_id' => $request->gelombang_id,
                'kuota' => $jurusanAktif->kuota,
                'jumlah_pendaftar' => $jumlahPendaftar
            ]);

            if ($jumlahPendaftar >= $jurusanAktif->kuota) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maaf, kuota untuk jurusan ini sudah penuh'
                ], 400);
            }

            // Update jurusan siswa dengan gelombang_id
            $dataSiswa->update([
                'jurusan_id' => $request->jurusan_id,
                'gelombang_id' => $request->gelombang_id
            ]);

            Log::info('Jurusan dipilih', [
                'user_id' => $user->id,
                'jurusan_id' => $request->jurusan_id,
                'gelombang_id' => $request->gelombang_id,
                'jurusan_nama' => $jurusanAktif->nama_jurusan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jurusan ' . $jurusanAktif->nama_jurusan . ' (Gelombang ' . $request->gelombang_id . ') berhasil dipilih!'
            ]);

        } catch (\Exception $e) {
            Log::error('Pilih jurusan error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memilih jurusan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Data static sebagai fallback
     */
    private function getStaticJurusanData()
    {
        return [
            [
                'id' => 1,
                'kode_jurusan' => 'TJKT',
                'nama_jurusan' => 'Teknik Komputer dan Jaringan',
                'deskripsi' => 'Jurusan TJKT mempelajari instalasi, konfigurasi, pemeliharaan jaringan komputer.',
                'kuota' => 40,
                'kuota_tersedia' => 25,
                'jumlah_pendaftar' => 15,
                'gelombang_id' => 1
            ],
            [
                'id' => 2,
                'kode_jurusan' => 'PH',
                'nama_jurusan' => 'Perhotelan', 
                'deskripsi' => 'Jurusan Perhotelan mempelajari pelayanan tamu dan manajemen hotel.',
                'kuota' => 35,
                'kuota_tersedia' => 20,
                'jumlah_pendaftar' => 15,
                'gelombang_id' => 1
            ],
            [
                'id' => 3,
                'kode_jurusan' => 'KUL',
                'nama_jurusan' => 'Kuliner',
                'deskripsi' => 'Jurusan Kuliner mempelajari teknik memasak dan manajemen dapur.',
                'kuota' => 30,
                'kuota_tersedia' => 10, 
                'jumlah_pendaftar' => 20,
                'gelombang_id' => 1
            ]
        ];
    }

    /**
     * Tentukan step berdasarkan progress dengan urutan baru
     */
    private function getCurrentStep($pembayaran, $dataSiswa)
    {
        // Step 1: Upload Bukti Pembayaran Formulir
        if (!$pembayaran || $pembayaran->status != 'diverifikasi') {
            return 1;
        }
        
        // Step 2: Pilih Jurusan
        if ($pembayaran->status == 'diverifikasi' && (!$dataSiswa || !$dataSiswa->jurusan_id)) {
            return 2;
        }
        
        // Step 3: Pengisian Formulir
        if ($dataSiswa && $dataSiswa->jurusan_id) {
            $progress = $this->progressService->hitungProgressDataDiri($dataSiswa);
            
            // Jika progress kurang dari 100% (belum lengkap)
            // if ($progress['total'] < 100) {
            //     return 3;
            // }
            
            // Atau jika ingin threshold tertentu, misalnya 95%
            if ($progress['total'] < 80) {
                return 3;
            }
        }
        
        // Step 4: Pembayaran PPDB (DIPINDAH KE POSISI INI)
        if ($dataSiswa && $dataSiswa->is_form_completed && !$dataSiswa->is_paid) {
            return 4;
        }
        
        // Step 5: Review/Verifikasi Data (DIPINDAH KE POSISI INI)
        if ($dataSiswa && $dataSiswa->is_paid && $dataSiswa->status_pendaftar != 'diterima') {
            return 5;
        }
        
        // Step 6: Selesai
        return 6;
    }

    /**
     * Tentukan status text dan color berdasarkan step
     */
    private function getStatusInfo($currentStep, $pembayaran, $dataSiswa = null)
    {
        switch ($currentStep) {
            case 1:
                // Jika sudah upload bukti formulir tapi status masih pending
                if ($pembayaran && $pembayaran->status == 'pending') {
                    return ['Menunggu Verifikasi Pembayaran', 'info'];
                }
                // Jika bukti ditolak
                if ($pembayaran && $pembayaran->status == 'ditolak') {
                    return ['Upload Ulang Bukti Formulir', 'danger'];
                }
                // Jika belum upload sama sekali
                return ['Upload Bukti Formulir', 'warning'];
                
            case 2:
                return ['Pilih Jurusan', 'warning'];
                
            case 3:
                return ['Isi Formulir', 'warning'];
                
            case 4:
                // Cek apakah sudah ada pembayaran PPDB
                $pembayaranPPDB = Pembayaran::where('user_id', Auth::id())
                    ->where('jenis_pembayaran', 'ppdb')
                    ->latest()
                    ->first();
                    
                if ($pembayaranPPDB) {
                    if ($pembayaranPPDB->status == 'pending') {
                        return ['Menunggu Verifikasi Pembayaran PPDB', 'info'];
                    }
                    if ($pembayaranPPDB->status == 'ditolak') {
                        return ['Upload Ulang Bukti Pembayaran PPDB', 'danger'];
                    }
                    if ($pembayaranPPDB->status == 'diverifikasi') {
                        return ['Pembayaran PPDB Terverifikasi', 'success'];
                    }
                }
                return ['Pembayaran PPDB', 'warning'];
                
            case 5:
                return ['Menunggu Verifikasi Data', 'info'];
                
            case 6:
                return ['Selesai', 'success'];
                
            default:
                return ['Menunggu', 'warning'];
        }
    }
}