<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\UserSiswa;
use Illuminate\Http\Request;
use App\Exports\LaporanPembayaranExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = UserSiswa::with(['datasiswa', 'pembayaran' => function($query) {
            $query->where('status', 'diverifikasi');
        }]);

        // Filter berdasarkan nama siswa
        if ($request->has('nama_siswa') && $request->nama_siswa != '') {
            $query->whereHas('datasiswa', function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->nama_siswa . '%');
            });
        }

        // Filter berdasarkan jenis pembayaran
        if ($request->has('jenis_pembayaran') && $request->jenis_pembayaran != '') {
            $query->whereHas('pembayaran', function($q) use ($request) {
                $q->where('jenis_pembayaran', $request->jenis_pembayaran)
                  ->where('status', 'diverifikasi');
            });
        }

        // Filter berdasarkan tanggal
        if ($request->has('tanggal_awal') && $request->tanggal_awal != '') {
            $query->whereHas('pembayaran', function($q) use ($request) {
                $q->whereDate('tanggal_bayar', '>=', $request->tanggal_awal)
                  ->where('status', 'diverifikasi');
            });
        }

        if ($request->has('tanggal_akhir') && $request->tanggal_akhir != '') {
            $query->whereHas('pembayaran', function($q) use ($request) {
                $q->whereDate('tanggal_bayar', '<=', $request->tanggal_akhir)
                  ->where('status', 'diverifikasi');
            });
        }

        $siswa = $query->paginate(10);

        // Hitung statistik
        $totalTransaksi = 0;
        $totalNominal = 0;
        $totalSiswa = $siswa->total();

        foreach ($siswa as $item) {
            $totalTransaksi += $item->pembayaran->count();
            $totalNominal += $item->pembayaran->sum('jumlah');
        }

        return view('admin.laporan-pembayaran.index', compact(
            'siswa', 
            'totalTransaksi', 
            'totalNominal',
            'totalSiswa'
        ));
    }

    public function detail($id)
    {
        $siswa = UserSiswa::with(['datasiswa'])->findOrFail($id);
        $pembayaran = Pembayaran::where('user_id', $id)
            ->where('status', 'diverifikasi')
            ->orderBy('tanggal_bayar', 'desc')
            ->get();

        // Hanya hitung formulir dan ppdb
        $totalFormulir = $pembayaran->where('jenis_pembayaran', 'formulir')->sum('jumlah');
        $totalPPDB = $pembayaran->where('jenis_pembayaran', 'ppdb')->sum('jumlah');
        $totalSemua = $pembayaran->sum('jumlah');

        return view('admin.laporan-pembayaran.detail', compact(
            'siswa', 
            'pembayaran', 
            'totalFormulir', 
            'totalPPDB',
            'totalSemua'
        ));
    }

    public function filter(Request $request)
    {
        return $this->index($request);
    }

    public function exportExcel(Request $request)
    {
        $filename = 'laporan-pembayaran-' . date('Y-m-d-H-i-s') . '.xlsx';
        
        return Excel::download(new LaporanPembayaranExport(
            $request->nama_siswa,
            $request->jenis_pembayaran,
            $request->tanggal_awal,
            $request->tanggal_akhir
        ), $filename);
    }
}