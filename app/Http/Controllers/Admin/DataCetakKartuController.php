<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataSiswa;
use App\Models\UserSiswa;
use App\Models\PengaturanAplikasi;
use App\Models\GelombangPendaftaran;
use App\Models\Jurusan;
use App\Exports\DataCetakKartuExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class DataCetakKartuController extends Controller


{
    public function index(Request $request)
    {
        $pengaturan = PengaturanAplikasi::getSettings();

        if (!$pengaturan->enable_cetak_kartu) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Fitur cetak kartu saat ini sedang dinonaktifkan.');
        }

        $query = DataSiswa::with(['user', 'jurusan', 'gelombang.tahunAjaran']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('username', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        if ($request->filled('gelombang_id')) {
            $query->where('gelombang_id', $request->gelombang_id);
        }

        $dataSiswaList = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $gelombangList = GelombangPendaftaran::all();
        $jurusanList = Jurusan::all();

        return view('admin.data-cetak-kartu.index', compact('dataSiswaList', 'gelombangList', 'jurusanList', 'pengaturan'));
    }

    public function cetakKartu($id)
    {
        $dataSiswa = DataSiswa::with(['user', 'jurusan', 'gelombang'])->findOrFail($id);
        $user = $dataSiswa->user;
        $pengaturan = PengaturanAplikasi::getSettings();

        if (!$user) {
            return back()->with('error', 'Data akun siswa tidak ditemukan.');
        }

        return view('siswa.cetak-kartu', compact('user', 'dataSiswa', 'pengaturan'));
    }

    public function exportExcel(Request $request)
    {
        $pengaturan = PengaturanAplikasi::getSettings();

        if (!$pengaturan->enable_cetak_kartu) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Fitur cetak kartu saat ini sedang dinonaktifkan.');
        }

        $filename = 'Data_Cetak_Kartu_Peserta_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new DataCetakKartuExport($request->search, $request->jurusan_id, $request->gelombang_id), $filename);
    }
}

