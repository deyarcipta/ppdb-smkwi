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
use Barryvdh\DomPDF\Facade\Pdf;
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

    public function cetakMassalKartu(Request $request)
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

        $dataSiswaList = $query->orderBy('nama_lengkap', 'asc')->get();

        if ($dataSiswaList->isEmpty()) {
            return back()->with('error', 'Tidak ada data siswa yang memenuhi kriteria filter untuk dicetak.');
        }

        return view('admin.data-cetak-kartu.cetak-massal-kartu', compact('dataSiswaList', 'pengaturan'));
    }

    public function cetakMassalSuratSehat(Request $request)
    {
        $pengaturan = PengaturanAplikasi::getSettings();

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

        $dataSiswaList = $query->orderBy('nama_lengkap', 'asc')->get();

        if ($dataSiswaList->isEmpty()) {
            return back()->with('error', 'Tidak ada data siswa yang memenuhi kriteria filter untuk dicetak.');
        }

        $pdf = Pdf::loadView('admin.data-cetak-kartu.surat-sehat-massal-pdf', compact('dataSiswaList', 'pengaturan'));
        $pdf->setPaper('a4', 'portrait');

        $filename = 'Surat_Pernyataan_Sehat_Massal_' . date('Ymd_His') . '.pdf';

        if ($request->has('download')) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
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

    /**
     * Cetak Master Format Blank Surat Pernyataan Sehat (PDF)
     */
    public function cetakSuratPernyataanSehatMaster(Request $request)
    {
        $pengaturan = PengaturanAplikasi::getSettings();
        $dataSiswa = null;
        $user = null;

        $pdf = Pdf::loadView('siswa.surat-pernyataan-sehat-pdf', compact('user', 'dataSiswa', 'pengaturan'));
        $pdf->setPaper('a4', 'portrait');

        $filename = 'Format_Surat_Pernyataan_Sehat_SPMB.pdf';

        if ($request->has('download')) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    /**
     * Cetak Surat Pernyataan Sehat Siswa Tertentu (PDF)
     */
    public function cetakSuratPernyataanSehat(Request $request, $id)
    {
        $dataSiswa = DataSiswa::with(['user', 'jurusan', 'gelombang.tahunAjaran'])->findOrFail($id);
        $user = $dataSiswa->user;
        $pengaturan = PengaturanAplikasi::getSettings();

        $pdf = Pdf::loadView('siswa.surat-pernyataan-sehat-pdf', compact('user', 'dataSiswa', 'pengaturan'));
        $pdf->setPaper('a4', 'portrait');

        $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $dataSiswa->nama_lengkap ?? 'siswa');
        $filename = 'Surat_Pernyataan_Sehat_' . $cleanName . '.pdf';

        if ($request->has('download')) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    /**
     * Cetak Formulir Pendaftaran Siswa (PDF persis seperti di siswa/download-formulir)
     */
    public function cetakFormulir($id)
    {
        // Cari siswa baik berdasarkan id UserSiswa atau id DataSiswa
        $siswa = UserSiswa::with(['dataSiswa', 'dataSiswa.jurusan', 'dataSiswa.gelombang', 'pembayaran'])->find($id);

        if (!$siswa) {
            $dataSiswa = DataSiswa::with(['user.pembayaran', 'jurusan', 'gelombang'])->findOrFail($id);
            $siswa = $dataSiswa->user ?? new UserSiswa(['username' => $dataSiswa->nama_lengkap]);
            $siswa->setRelation('dataSiswa', $dataSiswa);
            if ($dataSiswa->user && $dataSiswa->user->pembayaran) {
                $siswa->setRelation('pembayaran', $dataSiswa->user->pembayaran);
            }
        }

        if (!$siswa->dataSiswa) {
            $siswa->dataSiswa()->create([
                'nama_lengkap' => $siswa->username,
            ]);
            $siswa->load('dataSiswa');
        }

        $pengaturan = PengaturanAplikasi::getSettings();

        $data = [
            'pendaftar' => $siswa,
            'siswa' => $siswa,
            'dataSiswa' => $siswa->dataSiswa,
            'pengaturan' => $pengaturan,
            'totalBayar' => $siswa->pembayaran ? $siswa->pembayaran->where('status', 'diverifikasi')->sum('jumlah') : 0,
            'totalBiayaPPDB' => 0,
            'tanggal' => now()->format('d F Y'),
        ];

        $pdf = Pdf::loadView('siswa.formulir.pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $siswa->dataSiswa->nama_lengkap ?? 'siswa');
        $cleanNo = preg_replace('/[^a-zA-Z0-9_-]/', '_', $siswa->dataSiswa->no_pendaftaran ?? '000');
        $filename = 'Formulir_Pendaftaran_' . $cleanName . '_' . $cleanNo . '.pdf';

        return $pdf->stream($filename);
    }
}

