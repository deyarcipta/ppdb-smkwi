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

            // Ambil data biaya PPDB (kecuali formulir)
            $MasterBiaya = MasterBiaya::where('status', 1)
                ->where('jenis_biaya', '!=', 'formulir')
                ->get();

            // Ambil data info pembayaran
            $infoPembayaran = InfoPembayaran::where('status', 1)->first();

            // Hitung total biaya, total dibayar, dan sisa bayar
            $totalBiaya = $MasterBiaya->sum('total_biaya');
            $totalDibayar = $pembayaran->where('status', 'diverifikasi')->sum('jumlah');
            $sisaBayar = $totalBiaya - $totalDibayar;

            // Hitung total semua biaya (untuk opsi "semua")
            $totalAll = $MasterBiaya->sum('total_biaya');

            // Tentukan status pembayaran
            if ($sisaBayar <= 0) {
                $status = 'LUNAS';
            } elseif ($totalDibayar > 0) {
                $status = 'BELUM LUNAS';
            } else {
                $status = 'BELUM BAYAR';
            }

            return view('siswa.pembayaran.index', compact(
                'pembayaran',
                'infoPembayaran',
                'MasterBiaya',
                'siswa',
                'totalBiaya',
                'totalDibayar',
                'sisaBayar',
                'status',
                'totalAll'
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

            $validated = $this->validatePembayaran($request);
            Log::info('Validated Pembayaran Data:', $validated);
                
            // Upload bukti pembayaran
            if ($request->hasFile('bukti_pembayaran')) {
                $buktiPembayaran = $request->file('bukti_pembayaran');
                $fileName = 'bukti_' . time() . '_' . $siswa->no_pendaftaran . '.' . $buktiPembayaran->getClientOriginalExtension();
                $path = $buktiPembayaran->storeAs('bukti_pembayaran', $fileName, 'public');
                $validated['bukti_pembayaran'] = $path;
            }

            // Cek apakah memilih "semua" atau jenis pembayaran tertentu
            if ($validated['jenis_pembayaran'] === 'semua') {
                // Simpan semua jenis pembayaran sekaligus
                $this->storeAllPayments($validated, $siswa);
            } else {
                // Simpan pembayaran tunggal
                $this->storeSinglePayment($validated, $siswa);
            }

            DB::commit();
            Log::info('Pembayaran created successfully for user: ' . Auth::id());

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
     * Simpan semua jenis pembayaran sekaligus
     */
    private function storeAllPayments(array $data, DataSiswa $siswa)
    {
        // Ambil semua biaya (kecuali formulir)
        $masterBiayas = MasterBiaya::where('status', 1)
            ->where('jenis_biaya', '!=', 'formulir')
            ->get();

        // Hitung total semua biaya
        $totalAll = $masterBiayas->sum('total_biaya');
        
        // Validasi jumlah yang dibayar harus sama dengan total semua biaya
        if ($data['jumlah'] != $totalAll) {
            throw new \Exception('Jumlah pembayaran untuk "BAYAR SEMUA" harus sebesar Rp ' . number_format($totalAll, 0, ',', '.'));
        }

        // Simpan setiap jenis pembayaran
        foreach ($masterBiayas as $masterBiaya) {
            $paymentData = [
                'user_id' => Auth::id(),
                'master_biaya_id' => $masterBiaya->id,
                'no_pendaftaran' => $siswa->no_pendaftaran,
                'nama_siswa' => $siswa->nama_lengkap,
                'jenis_biaya' => $masterBiaya->jenis_biaya,
                'jenis_pembayaran' => $masterBiaya->jenis_biaya,
                'jumlah' => $masterBiaya->total_biaya,
                'metode_pembayaran' => $data['metode_pembayaran'],
                'bukti_pembayaran' => $data['bukti_pembayaran'],
                'tanggal_bayar' => $data['tanggal_bayar'],
                'catatan' => $data['catatan'] ?? 'Pembayaran semua biaya sekaligus',
                'status' => 'pending',
                'is_part_of_all' => true, // Field tambahan untuk menandai
            ];

            Pembayaran::create($paymentData);
        }
        
        Log::info('All payments created for user: ' . Auth::id() . ', total items: ' . count($masterBiayas));
    }

    /**
     * Simpan pembayaran tunggal
     */
    private function storeSinglePayment(array $data, DataSiswa $siswa)
    {
        // Cari master biaya berdasarkan jenis_biaya
        $masterBiaya = MasterBiaya::where('jenis_biaya', $data['jenis_pembayaran'])
            ->where('status', 1)
            ->first();

        if (!$masterBiaya) {
            throw new \Exception('Jenis biaya tidak ditemukan.');
        }

        // Validasi jumlah yang dibayar harus sama dengan biaya yang dipilih
        if ($data['jumlah'] != $masterBiaya->total_biaya) {
            throw new \Exception('Jumlah pembayaran harus sebesar Rp ' . number_format($masterBiaya->total_biaya, 0, ',', '.'));
        }

        // Simpan data pembayaran
        $paymentData = [
            'user_id' => Auth::id(),
            'master_biaya_id' => $masterBiaya->id,
            'no_pendaftaran' => $siswa->no_pendaftaran,
            'nama_siswa' => $siswa->nama_lengkap,
            'jenis_biaya' => $masterBiaya->jenis_biaya,
            'jenis_pembayaran' => $masterBiaya->jenis_biaya,
            'jumlah' => $masterBiaya->total_biaya,
            'metode_pembayaran' => $data['metode_pembayaran'],
            'bukti_pembayaran' => $data['bukti_pembayaran'],
            'tanggal_bayar' => $data['tanggal_bayar'],
            'catatan' => $data['catatan'] ?? null,
            'status' => 'pending',
            'is_part_of_all' => false,
        ];

        Pembayaran::create($paymentData);
        Log::info('Single payment created for user: ' . Auth::id() . ', type: ' . $masterBiaya->jenis_biaya);
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

            // Jika pembayaran adalah bagian dari "semua", tidak boleh diupdate sendiri
            if ($pembayaran->is_part_of_all) {
                throw new \Exception('Pembayaran ini merupakan bagian dari "BAYAR SEMUA". Untuk mengubah, hapus semua pembayaran terkait.');
            }

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

            // Update master biaya jika jenis pembayaran berubah
            if ($validated['jenis_pembayaran'] !== $pembayaran->jenis_pembayaran) {
                $masterBiaya = MasterBiaya::where('jenis_biaya', $validated['jenis_pembayaran'])
                    ->where('status', 1)
                    ->firstOrFail();
                    
                $validated['master_biaya_id'] = $masterBiaya->id;
                $validated['jenis_biaya'] = $masterBiaya->jenis_biaya;
                
                // Validasi jumlah harus sama dengan biaya baru
                if ($validated['jumlah'] != $masterBiaya->total_biaya) {
                    throw new \Exception('Jumlah pembayaran harus sebesar Rp ' . number_format($masterBiaya->total_biaya, 0, ',', '.'));
                }
            }

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

            // Jika pembayaran adalah bagian dari "semua", hapus semua yang terkait
            if ($pembayaran->is_part_of_all) {
                $allPayments = Pembayaran::where('user_id', Auth::id())
                    ->where('jenis_pembayaran', 'semua')
                    ->where('status', 'pending')
                    ->get();
                    
                foreach ($allPayments as $payment) {
                    // Hapus file bukti pembayaran (sama untuk semua)
                    if ($payment->id === $pembayaran->id && $payment->bukti_pembayaran && Storage::disk('public')->exists($payment->bukti_pembayaran)) {
                        Storage::disk('public')->delete($payment->bukti_pembayaran);
                    }
                    $payment->delete();
                }
                
                $message = 'Semua pembayaran terkait "BAYAR SEMUA" berhasil dihapus.';
            } else {
                // Hapus file bukti pembayaran
                if ($pembayaran->bukti_pembayaran && Storage::disk('public')->exists($pembayaran->bukti_pembayaran)) {
                    Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
                }

                $pembayaran->delete();
                $message = 'Pembayaran berhasil dihapus.';
            }

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
                // Ambil detail biaya tertentu
                $masterBiaya = MasterBiaya::where('jenis_biaya', $jenisPembayaran)
                    ->where('status', 1)
                    ->firstOrFail();
                    
                return response()->json([
                    'success' => true,
                    'data' => [
                        'jenis_biaya' => $masterBiaya->jenis_biaya,
                        'total_biaya' => $masterBiaya->total_biaya,
                        'nama_biaya' => strtoupper(str_replace('_', ' ', $masterBiaya->jenis_biaya))
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