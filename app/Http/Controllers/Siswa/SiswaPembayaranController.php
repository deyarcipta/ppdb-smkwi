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

            // Ambil data biaya PPDB
            $MasterBiaya = MasterBiaya::where('status', 1)->get();

            // Ambil data info pembayaran
            $infoPembayaran = InfoPembayaran::where('status', 1)->first();

            // Hitung total biaya, total dibayar, dan sisa bayar
            $totalBiaya = $MasterBiaya->sum('total_biaya');
            $totalDibayar = $pembayaran->where('status', 'diverifikasi')->sum('jumlah');
            $sisaBayar = $totalBiaya - $totalDibayar;

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
                'status'
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
            $validated = $this->validatePembayaran($request);
            Log::info('Validated Pembayaran Data:', $validated);

            // Ambil data siswa
            $siswa = DataSiswa::where('user_id', Auth::id())->firstOrFail();

            // Upload bukti pembayaran
            if ($request->hasFile('bukti_pembayaran')) {
                $buktiPembayaran = $request->file('bukti_pembayaran');
                $fileName = 'bukti_' . time() . '_' . $siswa->no_pendaftaran . '.' . $buktiPembayaran->getClientOriginalExtension();
                $path = $buktiPembayaran->storeAs('bukti_pembayaran', $fileName, 'public');
                $validated['bukti_pembayaran'] = $path;
            }

            // Tambahkan data tambahan
            $validated['user_id'] = Auth::id();
            $validated['no_pendaftaran'] = $siswa->no_pendaftaran;
            $validated['nama_siswa'] = $siswa->nama_lengkap;
            $validated['status'] = 'pending';

            // Simpan data pembayaran
            Pembayaran::create($validated);
            Log::info('Pembayaran created successfully for user: ' . Auth::id());

            return redirect()->route('siswa.pembayaran.index')
                ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');

        } catch (\Exception $e) {
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

            $pembayaran->update($validated);
            Log::info('Pembayaran updated successfully: ' . $id);

            return redirect()->route('siswa.pembayaran.index')
                ->with('success', 'Pembayaran berhasil diperbarui.');

        } catch (\Exception $e) {
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
            $pembayaran = Pembayaran::where('id', $id)
                ->where('user_id', Auth::id())
                ->where('status', 'pending')
                ->firstOrFail();

            // Hapus file bukti pembayaran
            if ($pembayaran->bukti_pembayaran && Storage::disk('public')->exists($pembayaran->bukti_pembayaran)) {
                Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
            }

            $pembayaran->delete();
            Log::info('Pembayaran deleted successfully: ' . $id);

            return redirect()->route('siswa.pembayaran.index')
                ->with('success', 'Pembayaran berhasil dihapus.');

        } catch (\Exception $e) {
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
        return $request->validate([
            'jenis_pembayaran' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|string|max:255',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tanggal_bayar' => 'required|date|before_or_equal:today',
            'catatan' => 'nullable|string|max:500',
        ], [
            'jenis_pembayaran.required' => 'Jenis pembayaran wajib diisi',
            'jumlah.required' => 'Jumlah pembayaran wajib diisi',
            'jumlah.min' => 'Jumlah pembayaran harus lebih dari 0',
            'metode_pembayaran.required' => 'Metode pembayaran wajib diisi',
            'bukti_pembayaran.required' => 'Bukti pembayaran wajib diupload',
            'bukti_pembayaran.image' => 'File harus berupa gambar',
            'bukti_pembayaran.mimes' => 'Format file harus jpeg, png, jpg, atau gif',
            'bukti_pembayaran.max' => 'Ukuran file maksimal 2MB',
            'tanggal_bayar.required' => 'Tanggal bayar wajib diisi',
            'tanggal_bayar.date' => 'Format tanggal tidak valid',
            'tanggal_bayar.before_or_equal' => 'Tanggal bayar tidak boleh melebihi hari ini',
        ]);
    }

    /**
     * Validasi form pembayaran untuk update
     */
    private function validatePembayaranUpdate(Request $request)
    {
        return $request->validate([
            'jenis_pembayaran' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|string|max:255',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tanggal_bayar' => 'required|date|before_or_equal:today',
            'catatan' => 'nullable|string|max:500',
        ], [
            'jenis_pembayaran.required' => 'Jenis pembayaran wajib diisi',
            'jumlah.required' => 'Jumlah pembayaran wajib diisi',
            'jumlah.min' => 'Jumlah pembayaran harus lebih dari 0',
            'metode_pembayaran.required' => 'Metode pembayaran wajib diisi',
            'bukti_pembayaran.image' => 'File harus berupa gambar',
            'bukti_pembayaran.mimes' => 'Format file harus jpeg, png, jpg, atau gif',
            'bukti_pembayaran.max' => 'Ukuran file maksimal 2MB',
            'tanggal_bayar.required' => 'Tanggal bayar wajib diisi',
            'tanggal_bayar.date' => 'Format tanggal tidak valid',
            'tanggal_bayar.before_or_equal' => 'Tanggal bayar tidak boleh melebihi hari ini',
        ]);
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
}