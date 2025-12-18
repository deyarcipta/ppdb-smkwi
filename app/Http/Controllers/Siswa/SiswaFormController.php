<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\DataSiswa;
use App\Models\UserSiswa;
use App\Models\Jurusan;
use App\Models\GelombangPendaftaran;
use App\Services\ProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

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

            // Tambahkan user_id dari user yang login
            $validated['user_id'] = Auth::id();
            $validated['status_pendaftar'] = 'pending';
            $validated['is_form_completed'] = true;

            // Cek apakah data sudah ada, jika ya update, jika tidak create
            $existingData = DataSiswa::where('user_id', Auth::id())->first();
            
            if ($existingData) {
                // Untuk data existing, pastikan no_pendaftaran sudah ada
                if (empty($existingData->no_pendaftaran)) {
                    // Jika no_pendaftaran belum ada, generate baru
                    $validated['no_pendaftaran'] = $this->generateNoPendaftaran(
                        $existingData->jurusan_id,
                        $existingData->gelombang_id
                    );
                    Log::info('Generated new no_pendaftaran for existing data: ' . $validated['no_pendaftaran']);
                }
                // Jika sudah ada no_pendaftaran, biarkan yang lama
                
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

            // Format: PPDB/TKJ/2025/I/001
            $noPendaftaran = "PPDB/{$kodeJurusan}/{$tahunAjaran}/{$angkaGelombang}/{$nomorUrut}";
            Log::info('Generated No Pendaftaran: ' . $noPendaftaran);

            return $noPendaftaran;

        } catch (\Exception $e) {
            Log::error('Error generating no_pendaftaran: ' . $e->getMessage());
            // Fallback format jika ada error
            return "PPDB/UMUM/" . date('Y') . "/I/001";
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
            // HAPUS validasi untuk jurusan_id dan gelombang_id karena tidak diinput user

            // DATA PRIBADI SISWA
            'nisn' => 'nullable|string|max:20',
            'nik' => 'nullable|string|max:20',
            'no_kk' => 'nullable|string|max:20',
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Laki-Laki,Perempuan',
            'no_hp' => 'nullable|string|max:15',
            'asal_sekolah' => 'nullable|string|max:255',
            'agama' => 'nullable|string|max:50',
            'ukuran_baju' => 'nullable|string|max:10',
            'hobi' => 'nullable|string|max:255',
            'cita_cita' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'desa' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kota' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
            'anak_ke' => 'nullable|integer|min:1',
            'jumlah_saudara' => 'nullable|integer|min:0',
            'tinggi_badan' => 'nullable|integer|min:100|max:250',
            'berat_badan' => 'nullable|integer|min:20|max:200',
            'status_dalam_keluarga' => 'nullable|string|max:255',
            'tinggal_bersama' => 'nullable|string|max:255',
            'jarak_kesekolah' => 'nullable|integer|min:0',
            'waktu_tempuh' => 'nullable|integer|min:0',
            'transportasi' => 'nullable|string|max:255',
            'no_kip' => 'nullable|string|max:50',
            'referensi' => 'nullable|string|max:255',
            'ket_referensi' => 'nullable|string|max:255', // GUNAKAN NAMA YANG ADA DI DATABASE

            // DATA AYAH
            'nik_ayah' => 'nullable|string|max:20',
            'nama_ayah' => 'nullable|string|max:255',
            'tempat_lahir_ayah' => 'nullable|string|max:255',
            'tanggal_lahir_ayah' => 'nullable|date',
            'pendidikan_ayah' => 'nullable|string|max:255',
            'pekerjaan_ayah' => 'nullable|string|max:255',
            'penghasilan_ayah' => 'nullable|string|max:255',
            'no_hp_ayah' => 'nullable|string|max:15',

            // DATA IBU
            'nik_ibu' => 'nullable|string|max:20',
            'nama_ibu' => 'nullable|string|max:255',
            'tempat_lahir_ibu' => 'nullable|string|max:255',
            'tanggal_lahir_ibu' => 'nullable|date',
            'pendidikan_ibu' => 'nullable|string|max:255',
            'pekerjaan_ibu' => 'nullable|string|max:255',
            'penghasilan_ibu' => 'nullable|string|max:255',
            'no_hp_ibu' => 'nullable|string|max:15',

            // DATA WALI
            'nik_wali' => 'nullable|string|max:20',
            'nama_wali' => 'nullable|string|max:255',
            'tempat_lahir_wali' => 'nullable|string|max:255',
            'tanggal_lahir_wali' => 'nullable|date',
            'pendidikan_wali' => 'nullable|string|max:255',
            'pekerjaan_wali' => 'nullable|string|max:255',
            'penghasilan_wali' => 'nullable|string|max:255',
            'no_hp_wali' => 'nullable|string|max:15',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
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

            // Pastikan siswa sudah mengisi formulir
            if (!$siswa->dataSiswa) {
                return back()->with('error', 'Anda belum mengisi formulir pendaftaran');
            }

            // Data untuk PDF
            $data = [
                'siswa' => $siswa,
                'dataSiswa' => $siswa->dataSiswa,
                'totalBayar' => $siswa->pembayaran->where('status', 'diverifikasi')->sum('jumlah'),
                'totalBiayaPPDB' => $totalBiayaPPDB ?? 0, // Sesuaikan dengan data biaya PPDB
                'tanggal' => now()->format('d F Y'),
            ];

            // Generate PDF
            $pdf = Pdf::loadView('formulir.pdf', $data);
            
            // Nama file PDF
            $filename = 'Formulir_Pendaftaran_' . $siswa->dataSiswa->nama_lengkap . '_' . $siswa->dataSiswa->no_pendaftaran . '.pdf';
            
            // Download PDF
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunduh formulir: ' . $e->getMessage());
        }
    }

}