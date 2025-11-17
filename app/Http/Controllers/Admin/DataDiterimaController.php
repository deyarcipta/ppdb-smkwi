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
    public function index()
    {
        // Query untuk mengambil data user yang memiliki dataSiswa dengan status_pendaftar = 'diterima'
        $data = UserSiswa::with(['dataSiswa', 'pembayaran', 'dataSiswa.gelombang', 'dataSiswa.jurusan'])
            ->whereHas('dataSiswa', function($query) {
                $query->where('status_pendaftar', 'diterima');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Ambil total biaya dari tabel master_biaya
        $totalBiaya = MasterBiaya::sum('total_biaya') ? : 3000000;

        return view('admin.data-diterima.index', compact('data', 'totalBiaya'));
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
    public function export()
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
            
            // logger("Data count: " . $count);
            
            if ($count === 0) {
                return back()->with('warning', 'Tidak ada data siswa yang diterima untuk di-export.');
            }

            $filename = 'data-siswa-diterima-' . date('Y-m-d') . '.xlsx';
            // logger("Filename: " . $filename);
            
            return Excel::download(new DataDiterimaExport, $filename);
            
        } catch (\Exception $e) {
            logger('Export Error: ' . $e->getMessage());
            logger('File: ' . $e->getFile());
            logger('Line: ' . $e->getLine());
            
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

}