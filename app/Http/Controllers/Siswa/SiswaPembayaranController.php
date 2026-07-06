<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\InfoPembayaran;
use App\Models\DataSiswa;
use App\Models\MasterBiaya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SiswaPembayaranController extends Controller
{
    /**
     * Menampilkan halaman index pembayaran
     */
    public function index()
    {
        try {
            // Ambil data siswa berdasarkan user_id
            $siswa = DataSiswa::where('user_id', Auth::id())->firstOrFail();
            
            // Ambil data pembayaran siswa
            $pembayaran = Pembayaran::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();

            // Ambil data biaya PPDB (termasuk formulir)
            $MasterBiaya = MasterBiaya::where('status', 1)
                ->get();

            // Ambil data info pembayaran
            $infoPembayaran = InfoPembayaran::where('status', 1)->first();

            // Hitung total biaya, total dibayar, dan sisa bayar
            $totalBiaya = $MasterBiaya->sum('total_biaya');
            $totalDibayar = $pembayaran->where('status', 'diverifikasi')->sum('jumlah');
            $sisaBayar = $totalBiaya - $totalDibayar;

            // Hitung sisa_biaya untuk masing-masing master biaya
            // 1. Dapatkan semua pembayaran PPDB terverifikasi
            $pembayaranPPDB = $pembayaran->where('jenis_pembayaran', 'ppdb')->where('status', 'diverifikasi');
            
            // 2. Pembayaran PPDB yang spesifik ke ID
            $pembayaranSpesifik = $pembayaranPPDB->whereNotNull('master_biaya_id');
            
            // 3. Pembayaran PPDB yang global (master_biaya_id = null)
            $totalDibayarPPDBGlobal = $pembayaranPPDB->whereNull('master_biaya_id')->sum('jumlah');

            // 4. Proses masing-masing master biaya
            $masterBiayasPPDB = [];
            foreach ($MasterBiaya as $biaya) {
                if ($biaya->jenis_biaya === 'formulir') {
                    // Formulir: hitung sisa langsung
                    $paidFormulir = $pembayaran->where('jenis_pembayaran', 'formulir')
                        ->where('status', 'diverifikasi')
                        ->sum('jumlah');
                    $biaya->sisa_biaya = max(0, $biaya->total_biaya - $paidFormulir);
                } else {
                    // PPDB: kurangi pembayaran yang spesifik ke ID ini dahulu
                    $paidSpecific = $pembayaranSpesifik->where('master_biaya_id', $biaya->id)->sum('jumlah');
                    $biaya->sisa_biaya = max(0, $biaya->total_biaya - $paidSpecific);
                    $masterBiayasPPDB[] = $biaya;
                }
            }

            // 5. Alokasikan pembayaran PPDB global secara berurutan untuk memotong sisa biaya PPDB yang tersisa
            $tempPaidGlobal = $totalDibayarPPDBGlobal;
            foreach ($masterBiayasPPDB as $biaya) {
                if ($tempPaidGlobal > 0 && $biaya->sisa_biaya > 0) {
                    if ($tempPaidGlobal >= $biaya->sisa_biaya) {
                        $tempPaidGlobal -= $biaya->sisa_biaya;
                        $biaya->sisa_biaya = 0;
                    } else {
                        $biaya->sisa_biaya -= $tempPaidGlobal;
                        $tempPaidGlobal = 0;
                    }
                }
            }

            // Hitung total semua biaya yang tersisa (untuk opsi "semua")
            $totalAll = $MasterBiaya->where('jenis_biaya', '!=', 'formulir')->sum('sisa_biaya');

            // Tentukan status pembayaran
            if ($sisaBayar <= 0) {
                $status = 'LUNAS';
            } elseif ($totalDibayar > 0) {
                $status = 'BELUM LUNAS';
            } else {
                $status = 'BELUM BAYAR';
            }

            $hasPendingPayment = $pembayaran->where('status', 'pending')->isNotEmpty();

            return view('siswa.pembayaran.index', compact(
                'pembayaran',
                'infoPembayaran',
                'MasterBiaya',
                'siswa',
                'totalBiaya',
                'totalDibayar',
                'sisaBayar',
                'status',
                'totalAll',
                'hasPendingPayment'
            ));

        } catch (\Exception $e) {
            Log::error('Error in PembayaranController@index: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menyimpan data pembayaran baru (dari modal)
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();


            // Ambil data siswa
            $siswa = DataSiswa::where('user_id', Auth::id())->firstOrFail();
            // 🔒 CEK NO PENDAFTARAN
            if (empty($siswa->no_pendaftaran)) {
                return redirect()
                    ->route('siswa.pembayaran.index')
                    ->with('warning', 'Silakan lengkapi formulir pendaftaran terlebih dahulu sebelum melakukan pembayaran.');
            }

            // Cek apakah ada pembayaran pending
            $hasPending = Pembayaran::where('user_id', Auth::id())
                ->where('status', 'pending')
                ->exists();

            if ($hasPending) {
                throw new \Exception('Anda tidak dapat melakukan pembayaran baru karena masih memiliki pembayaran yang menunggu verifikasi.');
            }

            $validated = $this->validatePembayaran($request);
            Log::info('Validated Pembayaran Data:', $validated);

            $jenisPembayaranInput = $validated['jenis_pembayaran'];
            
            // Hitung sisa_biaya untuk setiap master biaya
            $masterBiayas = MasterBiaya::where('status', 1)->get();
            $pembayaranSiswa = Pembayaran::where('user_id', Auth::id())
                ->where('status', 'diverifikasi')
                ->get();
                
            $totalDibayarPPDBGlobal = $pembayaranSiswa->where('jenis_pembayaran', 'ppdb')->whereNull('master_biaya_id')->sum('jumlah');
            $pembayaranSpesifik = $pembayaranSiswa->where('jenis_pembayaran', 'ppdb')->whereNotNull('master_biaya_id');

            $masterBiayasPPDB = [];
            foreach ($masterBiayas as $biaya) {
                if ($biaya->jenis_biaya === 'formulir') {
                    $paidFormulir = $pembayaranSiswa->where('jenis_pembayaran', 'formulir')->sum('jumlah');
                    $biaya->sisa_biaya = max(0, $biaya->total_biaya - $paidFormulir);
                } else {
                    $paidSpecific = $pembayaranSpesifik->where('master_biaya_id', $biaya->id)->sum('jumlah');
                    $biaya->sisa_biaya = max(0, $biaya->total_biaya - $paidSpecific);
                    $masterBiayasPPDB[] = $biaya;
                }
            }

            // Alokasikan pembayaran global PPDB secara berurutan
            $tempPaidGlobal = $totalDibayarPPDBGlobal;
            foreach ($masterBiayasPPDB as $biaya) {
                if ($tempPaidGlobal > 0 && $biaya->sisa_biaya > 0) {
                    if ($tempPaidGlobal >= $biaya->sisa_biaya) {
                        $tempPaidGlobal -= $biaya->sisa_biaya;
                        $biaya->sisa_biaya = 0;
                    } else {
                        $biaya->sisa_biaya -= $tempPaidGlobal;
                        $tempPaidGlobal = 0;
                    }
                }
            }

            $jenisDb = 'ppdb';
            $masterBiayaId = null;

            if ($jenisPembayaranInput === 'semua') {
                $limitBayar = $masterBiayas->where('jenis_biaya', '!=', 'formulir')->sum('sisa_biaya');
            } else {
                $targetBiaya = $masterBiayas->where('id', $jenisPembayaranInput)->first();
                if (!$targetBiaya) {
                    throw new \Exception('Jenis biaya tidak ditemukan.');
                }
                $limitBayar = $targetBiaya->sisa_biaya;
                $jenisDb = $targetBiaya->jenis_biaya;
                $masterBiayaId = $targetBiaya->id;
            }

            // Validasi jumlah yang dibayar tidak boleh melebihi sisa tagihan
            if ($validated['jumlah'] > $limitBayar) {
                throw new \Exception('Jumlah pembayaran melebihi sisa tagihan Anda untuk opsi ini (Sisa tagihan: Rp ' . number_format($limitBayar, 0, ',', '.') . ')');
            }

            // Simpan data pembayaran
            $paymentData = [
                'user_id' => Auth::id(),
                'master_biaya_id' => $masterBiayaId,
                'no_pendaftaran' => $siswa->no_pendaftaran,
                'nama_siswa' => $siswa->nama_lengkap,
                'jenis_pembayaran' => $jenisDb,
                'jumlah' => $validated['jumlah'],
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'bukti_pembayaran' => null,
                'tanggal_bayar' => $validated['tanggal_bayar'],
                'catatan' => $validated['catatan'] ?? ($jenisPembayaranInput === 'semua' ? 'Pembayaran semua biaya sekaligus' : null),
                'status' => 'pending',
            ];

            // Upload bukti pembayaran
            if ($request->hasFile('bukti_pembayaran')) {
                $buktiPembayaran = $request->file('bukti_pembayaran');
                $fileName = 'bukti_' . time() . '_' . $siswa->no_pendaftaran . '.' . $buktiPembayaran->getClientOriginalExtension();
                $path = $buktiPembayaran->storeAs('bukti_pembayaran', $fileName, 'public');
                $paymentData['bukti_pembayaran'] = $path;
            }

            Pembayaran::create($paymentData);

            // Kirim notifikasi WhatsApp ke Admin jika nomor HP admin diset
            try {
                $pengaturan = \App\Models\PengaturanAplikasi::getSettings();
                if ($pengaturan && !empty($pengaturan->no_hp_admin)) {
                    $namaBiaya = 'Semua Biaya Sekaligus';
                    if ($jenisPembayaranInput !== 'semua') {
                        $targetBiaya = \App\Models\MasterBiaya::find($jenisPembayaranInput);
                        if ($targetBiaya) {
                            $namaBiaya = $targetBiaya->nama_biaya;
                        }
                    }
                    
                    $message = "Ada siswa atas nama " . $siswa->nama_lengkap . " melakukan pembayaran " . $namaBiaya . " dengan nominal Rp " . number_format($validated['jumlah'], 0, ',', '.') . ". Harap segera diverifikasi.";
                               
                    $whatsappService = new \App\Services\WhatsappService();
                    $whatsappService->sendMessage($pengaturan->no_hp_admin, $message);
                }
            } catch (\Exception $waEx) {
                Log::error('Gagal mengirim notifikasi WA ke admin: ' . $waEx->getMessage());
            }

            DB::commit();
            Log::info('Pembayaran created successfully for user: ' . Auth::id() . ', amount: ' . $validated['jumlah']);

            return redirect()->route('siswa.pembayaran.index')
                ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving pembayaran: ' . $e->getMessage());
            return redirect()->route('siswa.pembayaran.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update data pembayaran (dari modal edit)
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $pembayaran = Pembayaran::where('id', $id)
                ->where('user_id', Auth::id())
                ->where('status', 'pending')
                ->firstOrFail();

            $validated = $this->validatePembayaranUpdate($request);
            Log::info('Validated Update Pembayaran Data:', $validated);

            // Upload bukti pembayaran baru jika ada
            if ($request->hasFile('bukti_pembayaran')) {
                // Hapus file lama
                if ($pembayaran->bukti_pembayaran && Storage::disk('public')->exists($pembayaran->bukti_pembayaran)) {
                    Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
                }

                // Upload file baru
                $buktiPembayaran = $request->file('bukti_pembayaran');
                $fileName = 'bukti_' . time() . '_' . $pembayaran->no_pendaftaran . '.' . $buktiPembayaran->getClientOriginalExtension();
                $path = $buktiPembayaran->storeAs('bukti_pembayaran', $fileName, 'public');
                $validated['bukti_pembayaran'] = $path;
            } else {
                // Pertahankan bukti pembayaran lama
                unset($validated['bukti_pembayaran']);
            }

            // Hitung sisa_biaya untuk setiap master biaya (kecuali transaksi ini)
            $masterBiayas = MasterBiaya::where('status', 1)->get();
            $pembayaranSiswa = Pembayaran::where('user_id', Auth::id())
                ->where('id', '!=', $id) // Kecualikan transaksi yang sedang diedit
                ->where('status', 'diverifikasi')
                ->get();
                
            $totalDibayarPPDBGlobal = $pembayaranSiswa->where('jenis_pembayaran', 'ppdb')->whereNull('master_biaya_id')->sum('jumlah');
            $pembayaranSpesifik = $pembayaranSiswa->where('jenis_pembayaran', 'ppdb')->whereNotNull('master_biaya_id');

            $masterBiayasPPDB = [];
            foreach ($masterBiayas as $biaya) {
                if ($biaya->jenis_biaya === 'formulir') {
                    $paidFormulir = $pembayaranSiswa->where('jenis_pembayaran', 'formulir')->sum('jumlah');
                    $biaya->sisa_biaya = max(0, $biaya->total_biaya - $paidFormulir);
                } else {
                    $paidSpecific = $pembayaranSpesifik->where('master_biaya_id', $biaya->id)->sum('jumlah');
                    $biaya->sisa_biaya = max(0, $biaya->total_biaya - $paidSpecific);
                    $masterBiayasPPDB[] = $biaya;
                }
            }

            // Alokasikan pembayaran global PPDB secara berurutan
            $tempPaidGlobal = $totalDibayarPPDBGlobal;
            foreach ($masterBiayasPPDB as $biaya) {
                if ($tempPaidGlobal > 0 && $biaya->sisa_biaya > 0) {
                    if ($tempPaidGlobal >= $biaya->sisa_biaya) {
                        $tempPaidGlobal -= $biaya->sisa_biaya;
                        $biaya->sisa_biaya = 0;
                    } else {
                        $biaya->sisa_biaya -= $tempPaidGlobal;
                        $tempPaidGlobal = 0;
                    }
                }
            }

            $jenisPembayaranInput = $validated['jenis_pembayaran'];
            $jenisDb = 'ppdb';
            $masterBiayaId = null;

            if ($jenisPembayaranInput === 'semua') {
                $limitBayar = $masterBiayas->where('jenis_biaya', '!=', 'formulir')->sum('sisa_biaya');
            } else {
                $targetBiaya = $masterBiayas->where('id', $jenisPembayaranInput)->first();
                if (!$targetBiaya) {
                    throw new \Exception('Jenis biaya tidak ditemukan.');
                }
                $limitBayar = $targetBiaya->sisa_biaya;
                $jenisDb = $targetBiaya->jenis_biaya;
                $masterBiayaId = $targetBiaya->id;
            }

            // Validasi jumlah yang dibayar tidak boleh melebihi sisa tagihan
            if ($validated['jumlah'] > $limitBayar) {
                throw new \Exception('Jumlah pembayaran melebihi sisa tagihan Anda untuk opsi ini (Sisa tagihan: Rp ' . number_format($limitBayar, 0, ',', '.') . ')');
            }

            $validated['jenis_pembayaran'] = $jenisDb;
            $validated['master_biaya_id'] = $masterBiayaId;

            $pembayaran->update($validated);
            Log::info('Pembayaran updated successfully: ' . $id);
            
            DB::commit();

            return redirect()->route('siswa.pembayaran.index')
                ->with('success', 'Pembayaran berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating pembayaran: ' . $e->getMessage());
            return redirect()->route('siswa.pembayaran.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus pembayaran (hanya untuk status pending)
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $pembayaran = Pembayaran::where('id', $id)
                ->where('user_id', Auth::id())
                ->where('status', 'pending')
                ->firstOrFail();

            // Hapus file bukti pembayaran
            if ($pembayaran->bukti_pembayaran && Storage::disk('public')->exists($pembayaran->bukti_pembayaran)) {
                Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
            }

            $pembayaran->delete();
            $message = 'Pembayaran berhasil dihapus.';

            Log::info('Pembayaran deleted successfully: ' . $id);
            
            DB::commit();

            return redirect()->route('siswa.pembayaran.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting pembayaran: ' . $e->getMessage());
            return redirect()->route('siswa.pembayaran.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Validasi form pembayaran untuk create
     */
    private function validatePembayaran(Request $request)
    {
        $rules = [
            'jenis_pembayaran' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|string|max:255',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'tanggal_bayar' => 'required|date|before_or_equal:today',
            'catatan' => 'nullable|string|max:500',
        ];

        $messages = [
            'jenis_pembayaran.required' => 'Jenis pembayaran wajib diisi',
            'jumlah.required' => 'Jumlah pembayaran wajib diisi',
            'jumlah.min' => 'Jumlah pembayaran harus lebih dari 0',
            'metode_pembayaran.required' => 'Metode pembayaran wajib diisi',
            'bukti_pembayaran.required' => 'Bukti pembayaran wajib diupload',
            'bukti_pembayaran.image' => 'File harus berupa gambar',
            'bukti_pembayaran.mimes' => 'Format file harus jpeg, png, jpg, atau gif',
            'bukti_pembayaran.max' => 'Ukuran file maksimal 5MB',
            'tanggal_bayar.required' => 'Tanggal bayar wajib diisi',
            'tanggal_bayar.date' => 'Format tanggal tidak valid',
            'tanggal_bayar.before_or_equal' => 'Tanggal bayar tidak boleh melebihi hari ini',
        ];

        return $request->validate($rules, $messages);
    }

    /**
     * Validasi form pembayaran untuk update
     */
    private function validatePembayaranUpdate(Request $request)
    {
        $rules = [
            'jenis_pembayaran' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|string|max:255',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'tanggal_bayar' => 'required|date|before_or_equal:today',
            'catatan' => 'nullable|string|max:500',
        ];

        $messages = [
            'jenis_pembayaran.required' => 'Jenis pembayaran wajib diisi',
            'jumlah.required' => 'Jumlah pembayaran wajib diisi',
            'jumlah.min' => 'Jumlah pembayaran harus lebih dari 0',
            'metode_pembayaran.required' => 'Metode pembayaran wajib diisi',
            'bukti_pembayaran.image' => 'File harus berupa gambar',
            'bukti_pembayaran.mimes' => 'Format file harus jpeg, png, jpg, atau gif',
            'bukti_pembayaran.max' => 'Ukuran file maksimal 5MB',
            'tanggal_bayar.required' => 'Tanggal bayar wajib diisi',
            'tanggal_bayar.date' => 'Format tanggal tidak valid',
            'tanggal_bayar.before_or_equal' => 'Tanggal bayar tidak boleh melebihi hari ini',
        ];

        return $request->validate($rules, $messages);
    }

    /**
     * Get data pembayaran untuk AJAX (jika diperlukan)
     */
    public function getPembayaran($id)
    {
        try {
            $pembayaran = Pembayaran::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $pembayaran
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting pembayaran: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Data pembayaran tidak ditemukan.'
            ], 404);
        }
    }

    /**
     * Get detail biaya untuk AJAX berdasarkan jenis pembayaran
     */
    public function getBiayaDetail(Request $request)
    {
        try {
            $jenisPembayaran = $request->input('jenis_pembayaran');
            
            if ($jenisPembayaran === 'semua') {
                // Hitung total semua biaya
                $totalAll = MasterBiaya::where('status', 1)
                    ->where('jenis_biaya', '!=', 'formulir')
                    ->sum('total_biaya');
                    
                return response()->json([
                    'success' => true,
                    'data' => [
                        'jenis_biaya' => 'semua',
                        'total_biaya' => $totalAll,
                        'nama_biaya' => 'SEMUA BIAYA SEKALIGUS'
                    ]
                ]);
            } else {
                // Ambil detail biaya tertentu berdasarkan ID
                $masterBiaya = MasterBiaya::where('id', $jenisPembayaran)
                    ->where('status', 1)
                    ->firstOrFail();
                    
                return response()->json([
                    'success' => true,
                    'data' => [
                        'jenis_biaya' => $masterBiaya->id,
                        'total_biaya' => $masterBiaya->total_biaya,
                        'nama_biaya' => strtoupper(str_replace('_', ' ', $masterBiaya->nama_biaya))
                    ]
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error getting biaya detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Data biaya tidak ditemukan.'
            ], 404);
        }
    }
}