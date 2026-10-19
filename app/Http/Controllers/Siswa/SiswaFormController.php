<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\DataSiswa;
use App\Models\UserSiswa;
use App\Models\Jurusan;
use App\Models\GelombangPendaftaran;
use App\Models\Pembayaran;
use App\Services\ProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SiswaFormController extends Controller
{

    protected $progressService;

    public function __construct(ProgressService $progressService)
    {
        $this->progressService = $progressService;
    }
    /**
     * Menampilkan form data siswa (multi-step)
     */
    public function create()
    {
        // Ambil data yang sudah ada (jika ada)
        $existingData = DataSiswa::where('user_id', Auth::id())->first();
        
        // Ambil data jurusan dan gelombang untuk dropdown (jika diperlukan untuk display)
        $jurusans = Jurusan::where('status', 'active')->get();
        $gelombangs = GelombangPendaftaran::where('status', 'active')->get();
        $progress = $this->progressService->hitungProgressDataDiri($existingData);
        
        return view('siswa.formulir.create', compact('existingData', 'jurusans', 'gelombangs','progress'));
    }

    /**
     * Menyimpan/memperbarui data formulir (semua step)
     */
    public function store(Request $request)
    {
        try {
            $validated = $this->validateForm($request);
            Log::info('Validated Data:', $validated);

            // Sinkronkan kota_sekolah_asal untuk kompatibilitas data lama
            if (!empty($validated['kecamatan_desa_sekolah_asal'])) {
                $validated['kota_sekolah_asal'] = $validated['kecamatan_desa_sekolah_asal'];
            }

            // Tambahkan user_id dari user yang login
            $validated['user_id'] = Auth::id();
            $validated['status_pendaftar'] = 'pending';
            $validated['is_form_completed'] = true;

            // Cek apakah data sudah ada, jika ya update, jika tidak create
            $existingData = DataSiswa::where('user_id', Auth::id())->first();
            
            if ($existingData) {
                // Untuk data existing, pastikan no_pendaftaran resmi sudah ada dan terformat benar
                if (empty($existingData->no_pendaftaran) || !str_contains($existingData->no_pendaftaran, '/')) {
                    $validated['no_pendaftaran'] = DataSiswa::generateNoPendaftaranStatic(
                        $existingData->jurusan_id,
                        $existingData->gelombang_id,
                        Auth::user()->username
                    );
                    Log::info('Generated new no_pendaftaran for existing data: ' . $validated['no_pendaftaran']);
                } else {
                    // Pastikan prefix no_pendaftaran sesuai dengan tipe_aplikasi saat ini (SPMB atau PPDB)
                    $pengaturan = \App\Models\PengaturanAplikasi::first();
                    $tipeAplikasi = $pengaturan->tipe_aplikasi ?? 'PPDB';
                    $validated['no_pendaftaran'] = preg_replace('/^(PPDB|SPMB)/i', $tipeAplikasi, $existingData->no_pendaftaran);
                }
                
                // Sinkronkan ke data pembayaran siswa
                Pembayaran::where('user_id', Auth::id())->update([
                    'no_pendaftaran' => $validated['no_pendaftaran']
                ]);
                
                // Update data yang sudah ada
                $existingData->update($validated);
                Log::info('Data updated successfully for user: ' . Auth::id());
                $message = 'Formulir berhasil diperbarui!';
            } else {
                // Untuk data baru, perlu menentukan jurusan dan gelombang
                $jurusanId = $this->getDefaultJurusanId();
                $gelombangId = $this->getDefaultGelombangId();
                
                // Generate nomor pendaftaran untuk data baru
                $noPendaftaran = $this->generateNoPendaftaran($jurusanId, $gelombangId);
                Log::info('Generated no_pendaftaran for new data: ' . $noPendaftaran);
                
                $validated['jurusan_id'] = $jurusanId;
                $validated['gelombang_id'] = $gelombangId;
                $validated['no_pendaftaran'] = $noPendaftaran;
                
                DataSiswa::create($validated);
                Log::info('Data created successfully for user: ' . Auth::id());
                $message = 'Formulir berhasil dikirim! Status: Pending';
            }

            return redirect()->route('siswa.dashboard')
                ->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Formulir belum lengkap. Silakan lengkapi seluruh data wajib yang ditandai.');
        } catch (\Exception $e) {
            Log::error('Error saving data: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get default jurusan ID (misalnya jurusan pertama yang active)
     */
    private function getDefaultJurusanId()
    {
        $defaultJurusan = Jurusan::where('status', 'active')->first();
        return $defaultJurusan ? $defaultJurusan->id : 1;
    }

    /**
     * Get default gelombang ID (misalnya gelombang pertama yang active)
     */
    private function getDefaultGelombangId()
    {
        $defaultGelombang = GelombangPendaftaran::where('status', 'active')->first();
        return $defaultGelombang ? $defaultGelombang->id : 1;
    }

    /**
     * Generate nomor pendaftaran format: PPDB/TKJ/2025/I/002
     */
    private function generateNoPendaftaran($jurusanId, $gelombangId)
    {
        try {
            // Ambil data jurusan
            $jurusan = Jurusan::find($jurusanId);
            $kodeJurusan = $jurusan ? $jurusan->kode_jurusan : 'UMUM';
            Log::info('Kode Jurusan: ' . $kodeJurusan);

            // Ambil data gelombang
            $gelombang = GelombangPendaftaran::find($gelombangId);
            $tahunAjaran = $gelombang->tahunAjaran ? $gelombang->tahunAjaran->nama : 'Tahun Ajaran';
            Log::info('Tahun Ajaran: ' . $tahunAjaran);
            
            // Konversi nama gelombang ke angka romawi
            $namaGelombang = $gelombang ? $gelombang->nama_gelombang : 'Gelombang 1';
            $angkaGelombang = $this->convertToRoman($namaGelombang);
            Log::info('Angka Gelombang: ' . $angkaGelombang);

            // Ambil 3 digit terakhir dari username
            $username = Auth::user()->username;
            $lastThreeDigits = substr($username, -3);
            Log::info('Last Three Digits: ' . $lastThreeDigits);

            // Hitung nomor urut berdasarkan jumlah pendaftar dengan jurusan dan gelombang yang sama
            $jumlahPendaftar = DataSiswa::where('gelombang_id', $gelombangId)
                ->where('jurusan_id', $jurusanId)
                ->count();
            $nomorUrut = str_pad($jumlahPendaftar + 1, 3, '0', STR_PAD_LEFT);
            Log::info('Nomor Urut: ' . $nomorUrut);

            // Format: PPDB/TKJ/2025/I/001 atau SPMB/TKJ/2025/I/001
            $pengaturan = \App\Models\PengaturanAplikasi::first();
            $tipeAplikasi = $pengaturan->tipe_aplikasi ?? 'PPDB';
            $tahunAjaranClean = str_replace('/', '-', $tahunAjaran);
            $noPendaftaran = "{$tipeAplikasi}/{$kodeJurusan}/{$tahunAjaranClean}/{$angkaGelombang}/{$nomorUrut}";
            Log::info('Generated No Pendaftaran: ' . $noPendaftaran);

            return $noPendaftaran;

        } catch (\Exception $e) {
            Log::error('Error generating no_pendaftaran: ' . $e->getMessage());
            // Fallback format jika ada error
            $tipeFallback = \App\Models\PengaturanAplikasi::first()->tipe_aplikasi ?? 'PPDB';
            return "{$tipeFallback}/UMUM/" . date('Y') . "/I/001";
        }
    }

    /**
     * Konversi nama gelombang ke angka romawi
     * Contoh: "Gelombang 1" -> "I", "Gelombang 2" -> "II"
     */
    private function convertToRoman($namaGelombang)
    {
        // Ekstrak angka dari nama gelombang
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

    /**
     * Validasi form - HAPUS validasi untuk jurusan_id dan gelombang_id
     */
    private function validateForm(Request $request)
    {
        return $request->validate([
            // DATA PRIBADI SISWA (Semua wajib kecuali: no_kip, hobi, cita_cita, referensi, ket_referensi)
            'nisn' => 'required|string|max:20',
            'nik' => 'required|string|max:20',
            'no_kk' => 'required|string|max:20',
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'no_hp' => 'required|string|max:20',
            'asal_sekolah' => 'required|string|max:255',
            'jenjang_sekolah_asal' => 'required|string|in:SMP,MTs,PKBM,Lainnya',
            'status_sekolah_asal' => 'required|string|in:Negeri,Swasta',
            'kecamatan_desa_sekolah_asal' => 'required|string|max:255',
            'kota_sekolah_asal' => 'nullable|string|max:255',
            'agama' => 'required|string|max:50',
            'ukuran_baju' => 'required|string|max:10',
            'no_kip' => 'nullable|string|max:50',
            'hobi' => 'nullable|string|max:255',
            'cita_cita' => 'nullable|string|max:255',
            'referensi' => 'nullable|string|max:255',
            'ket_referensi' => 'nullable|string|max:255',

            // DATA KELUARGA (Semua wajib kecuali: tinggi_badan, berat_badan, jarak_kesekolah, waktu_tempuh, transportasi)
            'anak_ke' => 'required|integer|min:1',
            'jumlah_saudara' => 'required|integer|min:0',
            'status_dalam_keluarga' => 'required|string|max:255',
            'tinggal_bersama' => 'required|string|max:255',
            'tinggi_badan' => 'nullable|numeric|min:50|max:250',
            'berat_badan' => 'nullable|numeric|min:10|max:200',
            'jarak_kesekolah' => 'nullable|numeric|min:0',
            'waktu_tempuh' => 'nullable|numeric|min:0',
            'transportasi' => 'nullable|string|max:255',

            // DATA ALAMAT (Semua wajib)
            'alamat' => 'required|string',
            'rt' => 'required|string|max:10',
            'rw' => 'required|string|max:10',
            'desa' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kode_pos' => 'required|string|max:10',

            // DATA AYAH
            'status_ayah' => 'nullable|string|in:Masih Hidup,Meninggal Dunia',
            'nik_ayah' => 'nullable|string|max:20',
            'nama_ayah' => 'nullable|string|max:255',
            'tempat_lahir_ayah' => 'nullable|string|max:255',
            'tanggal_lahir_ayah' => 'nullable|date',
            'pendidikan_ayah' => 'nullable|string|max:255',
            'pekerjaan_ayah' => 'nullable|string|max:255',
            'penghasilan_ayah' => 'nullable|string|max:255',
            'no_hp_ayah' => 'nullable|string|max:20',

            // DATA IBU
            'status_ibu' => 'nullable|string|in:Masih Hidup,Meninggal Dunia',
            'nik_ibu' => 'nullable|string|max:20',
            'nama_ibu' => 'nullable|string|max:255',
            'tempat_lahir_ibu' => 'nullable|string|max:255',
            'tanggal_lahir_ibu' => 'nullable|date',
            'pendidikan_ibu' => 'nullable|string|max:255',
            'pekerjaan_ibu' => 'nullable|string|max:255',
            'penghasilan_ibu' => 'nullable|string|max:255',
            'no_hp_ibu' => 'nullable|string|max:20',

            // DATA WALI
            'nik_wali' => 'nullable|string|max:20',
            'nama_wali' => 'nullable|string|max:255',
            'tempat_lahir_wali' => 'nullable|string|max:255',
            'tanggal_lahir_wali' => 'nullable|date',
            'pendidikan_wali' => 'nullable|string|max:255',
            'pekerjaan_wali' => 'nullable|string|max:255',
            'penghasilan_wali' => 'nullable|string|max:255',
            'no_hp_wali' => 'nullable|string|max:20',
        ], [
            // DATA PRIBADI
            'nisn.required' => 'NISN wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'no_kk.required' => 'Nomor Kartu Keluarga (KK) wajib diisi.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'no_hp.required' => 'Nomor HP/WhatsApp wajib diisi.',
            'asal_sekolah.required' => 'Nama asal sekolah wajib diisi.',
            'jenjang_sekolah_asal.required' => 'Jenjang sekolah asal wajib dipilih.',
            'status_sekolah_asal.required' => 'Status sekolah asal wajib dipilih.',
            'kecamatan_desa_sekolah_asal.required' => 'Kecamatan/Desa asal sekolah wajib diisi.',
            'kota_sekolah_asal.required' => 'Kecamatan/Desa asal sekolah wajib diisi.',
            'agama.required' => 'Agama wajib dipilih.',
            'ukuran_baju.required' => 'Ukuran baju seragam wajib dipilih.',

            // DATA KELUARGA
            'anak_ke.required' => 'Urutan anak ke berapa wajib diisi.',
            'jumlah_saudara.required' => 'Jumlah saudara kandung wajib diisi.',
            'status_dalam_keluarga.required' => 'Status dalam keluarga wajib dipilih.',
            'tinggal_bersama.required' => 'Status tinggal bersama wajib dipilih.',

            // DATA ALAMAT
            'alamat.required' => 'Alamat lengkap tempat tinggal wajib diisi.',
            'rt.required' => 'Nomor RT tempat tinggal wajib diisi.',
            'rw.required' => 'Nomor RW tempat tinggal wajib diisi.',
            'desa.required' => 'Desa/Kelurahan tempat tinggal wajib diisi.',
            'kecamatan.required' => 'Kecamatan tempat tinggal wajib diisi.',
            'kota.required' => 'Kota/Kabupaten tempat tinggal wajib diisi.',
            'provinsi.required' => 'Provinsi tempat tinggal wajib diisi.',
            'kode_pos.required' => 'Kode pos tempat tinggal wajib diisi.',
        ]);
    }

    public function uploadFoto(Request $request)
    {
        $request->validate([
            'foto_siswa' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        // ambil data siswa berdasarkan user login
        $siswa = DataSiswa::where('user_id', auth()->id())->first();

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan'
            ], 404);
        }

        /* ===============================
        HAPUS FOTO LAMA (JIKA ADA)
        =============================== */
        if ($siswa->foto_siswa) {
            if (Storage::disk('public')->exists($siswa->foto_siswa)) {
                Storage::disk('public')->delete($siswa->foto_siswa);
            }
        }

        /* ===============================
        UPLOAD FOTO BARU (storeAs)
        =============================== */
        if ($request->hasFile('foto_siswa')) {
            $foto = $request->file('foto_siswa');

            $fileName = 'siswa_' . $siswa->id . '_' . time() . '.' . $foto->getClientOriginalExtension();

            // simpan ke storage/app/public/foto_siswa
            $path = $foto->storeAs('foto_siswa', $fileName, 'public');

            /* ===============================
            UPDATE DATABASE
            =============================== */
            $siswa->update([
                'foto_siswa' => $path
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil diperbarui',
            'foto_url' => asset('storage/' . $siswa->foto_siswa)
        ]);
    }

    public function downloadFormulir()
    {
        try {
            // Ambil data siswa yang login
            $user = auth()->user();
            $siswa = UserSiswa::with(['dataSiswa', 'dataSiswa.jurusan', 'dataSiswa.gelombang', 'pembayaran'])
                ->where('id', $user->id)
                ->firstOrFail();

            // Pastikan data siswa tersedia
            if (!$siswa->dataSiswa) {
                $siswa->dataSiswa()->create([
                    'nama_lengkap' => $siswa->username,
                ]);
                $siswa->load('dataSiswa');
            }

            $pengaturan = \App\Models\PengaturanAplikasi::first();

            // Data untuk PDF
            $data = [
                'pendaftar' => $siswa,
                'siswa' => $siswa,
                'dataSiswa' => $siswa->dataSiswa,
                'pengaturan' => $pengaturan,
                'totalBayar' => $siswa->pembayaran->where('status', 'diverifikasi')->sum('jumlah'),
                'totalBiayaPPDB' => 0,
                'tanggal' => now()->format('d F Y'),
            ];

            // Generate PDF
            $pdf = Pdf::loadView('siswa.formulir.pdf', $data);
            $pdf->setPaper('a4', 'portrait');
            
            // Nama file PDF
            $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $siswa->dataSiswa->nama_lengkap ?? 'siswa');
            $cleanNo = preg_replace('/[^a-zA-Z0-9_-]/', '_', $siswa->dataSiswa->no_pendaftaran ?? '000');
            $filename = 'Formulir_Pendaftaran_' . $cleanName . '_' . $cleanNo . '.pdf';
            
            // Stream PDF agar dapat dilihat dan dicetak langsung di browser
            return $pdf->stream($filename);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses formulir: ' . $e->getMessage());
        }
    }

}