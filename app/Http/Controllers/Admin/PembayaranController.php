<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembayaran::with(['user', 'verifier', 'dataSiswa'])
            ->whereIn('status', ['diverifikasi']); // Hanya data yang sudah diverifikasi

        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan jenis pembayaran
        if ($request->has('jenis_pembayaran') && $request->jenis_pembayaran != '') {
            $query->where('jenis_pembayaran', $request->jenis_pembayaran);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('nama_siswa', 'like', "%{$search}%")
                  ->orWhere('metode_pembayaran', 'like', "%{$search}%");
            });
        }

        $data = $query->latest()->paginate(10);
        
        $jenisPembayaran = [
            'pendaftaran' => 'Biaya Pendaftaran',
            'formulir' => 'Biaya Formulir',
            'uang_pangkal' => 'Uang Pangkal',
            'spp' => 'SPP'
        ];

        $statuses = [
            'diverifikasi' => 'Terverifikasi',
            'ditolak' => 'Ditolak',
            'pending' => 'Menunggu Verifikasi'
        ];

        // Hitung statistik
        $totalTerverifikasi = Pembayaran::where('status', 'diverifikasi')->count();
        $totalDitolak = Pembayaran::where('status', 'ditolak')->count();
        $totalAmount = Pembayaran::where('status', 'diverifikasi')->sum('jumlah');

        return view('admin.pembayaran.index', compact(
            'data', 
            'jenisPembayaran', 
            'statuses',
            'totalTerverifikasi',
            'totalDitolak',
            'totalAmount'
        ));
    }

    public function downloadBukti($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        
        if (!$pembayaran->bukti_pembayaran) {
            return redirect()->back()->with('error', 'Bukti pembayaran tidak ditemukan');
        }

        $path = storage_path('app/public/bukti_pembayaran/' . $pembayaran->bukti_pembayaran);
        
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File bukti pembayaran tidak ditemukan');
        }

        return response()->download($path);
    }

    public function show($id)
    {
        $pembayaran = Pembayaran::with(['user', 'verifier', 'dataSiswa'])->findOrFail($id);
        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    public function destroy($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        
        // Hapus file bukti pembayaran jika ada
        if ($pembayaran->bukti_pembayaran) {
            Storage::delete('public/bukti_pembayaran/' . $pembayaran->bukti_pembayaran);
        }
        
        $pembayaran->delete();

        return redirect()->route('pembayaran.index')
            ->with('success', 'Data pembayaran berhasil dihapus');
    }
}