<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DataSiswa;
use App\Models\Pembayaran;
use App\Models\Jurusan;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik utama dari tabel data_siswa
        $totalPendaftar = DataSiswa::count();
        $pendingVerifikasi = DataSiswa::where('status_pendaftar', 'pending')->count();
        $diterima = DataSiswa::where('status_pendaftar', 'diterima')->count();
        $ditolak = DataSiswa::where('status_pendaftar', 'ditolak')->count();

        // Data untuk chart jurusan dengan relasi
        $jurusanStats = DataSiswa::with('jurusan')
            ->select('jurusan_id', DB::raw('COUNT(*) as total'))
            ->groupBy('jurusan_id')
            ->get()
            ->map(function($item) {
                return [
                    'nama_jurusan' => $item->jurusan ? $item->jurusan->nama_jurusan : 'Belum Memilih',
                    'total' => $item->total
                ];
            });

        $jurusanLabels = $jurusanStats->pluck('nama_jurusan')->toArray();
        $jurusanData = $jurusanStats->pluck('total')->toArray();

        // Pendaftar terbaru dari data_siswa dengan relasi jurusan
        $pendaftarTerbaru = DataSiswa::with('jurusan')
            ->latest()
            ->take(8)
            ->get(['id', 'nama_lengkap', 'jurusan_id', 'status_pendaftar', 'created_at']);

        // Data pembayaran pending dari tabel pembayaran
        $pembayaranPending = Pembayaran::where('status', 'pending')->count();

        return view('admin.dashboard', compact(
            'totalPendaftar',
            'pendingVerifikasi',
            'diterima',
            'ditolak',
            'jurusanLabels',
            'jurusanData',
            'pendaftarTerbaru',
            'pembayaranPending'
        ));
    }
}