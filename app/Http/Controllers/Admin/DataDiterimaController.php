<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserSiswa;
use App\Models\DataSiswa;
use App\Models\MasterBiaya;
use App\Exports\DataDiterimaExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf; // Tambahkan ini untuk PDF

class DataDiterimaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Query untuk mengambil data user yang memiliki dataSiswa dengan status_pendaftar = 'diterima'
        $query = UserSiswa::with(['dataSiswa', 'pembayaran', 'dataSiswa.gelombang', 'dataSiswa.jurusan'])
            ->whereHas('dataSiswa', function($query) {
                $query->where('status_pendaftar', 'diterima');
            });

        if ($request->filled('jurusan_id')) {
            $query->whereHas('dataSiswa', function($q) use ($request) {
                $q->where('jurusan_id', $request->jurusan_id);
            });
        }

        if ($request->filled('gelombang_id')) {
            $query->whereHas('dataSiswa', function($q) use ($request) {
                $q->where('gelombang_id', $request->gelombang_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhereHas('dataSiswa', function($sq) use ($search) {
                      $sq->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('no_pendaftaran', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%")
                        ->orWhere('asal_sekolah', 'like', "%{$search}%");
                  });
            });
        }

        $data = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $gelombangList = \App\Models\GelombangPendaftaran::all();
        $jurusanList = \App\Models\Jurusan::all();

        // Ambil total biaya dari tabel master_biaya
        $totalBiaya = MasterBiaya::where('status', 1)->sum('total_biaya');

        return view('admin.data-diterima.index', compact('data', 'totalBiaya', 'gelombangList', 'jurusanList'));
    }

    /**
     * Update status pendaftar
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users_siswa,id',
            'status_pendaftar' => 'required|in:pending,diterima,ditolak'
        ]);

        try {
            $user = UserSiswa::findOrFail($request->id);
            
            if ($user->dataSiswa) {
                $user->dataSiswa->update([
                    'status_pendaftar' => $request->status_pendaftar
                ]);
                
                return redirect()->back()->with('success', 'Status pendaftar berhasil diupdate.');
            }
            
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = UserSiswa::findOrFail($id);
            
            // Hapus data terkait jika diperlukan
            if ($user->dataSiswa) {
                $user->dataSiswa->delete();
            }
            
            // Hapus pembayaran jika ada
            if ($user->pembayaran) {
                $user->pembayaran()->delete();
            }
            
            $user->delete();
            
            return redirect()->back()->with('success', 'Data berhasil dihapus.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Export data to Excel
     */
    public function export(Request $request)
    {
        try {
            logger('=== EXPORT FUNCTION CALLED ===');
            
            // Cek jika package Excel tersedia
            if (!class_exists('Maatwebsite\Excel\Excel')) {
                logger('Excel package not found');
                return back()->with('error', 'Package Excel tidak terinstall.');
            }

            // Cek jika ada data
            $count = UserSiswa::whereHas('dataSiswa', function($query) {
                $query->where('status_pendaftar', 'diterima');
            })->count();
            
            if ($count === 0) {
                return back()->with('warning', 'Tidak ada data siswa yang diterima untuk di-export.');
            }

            $filename = 'data-siswa-diterima-' . date('Y-m-d') . '.xlsx';
            
            return Excel::download(new DataDiterimaExport($request), $filename);
            
        } catch (\Exception $e) {
            logger('Export Error: ' . $e->getMessage());
            logger('File: ' . $e->getFile());
            logger('Line: ' . $e->getLine());
            
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

}