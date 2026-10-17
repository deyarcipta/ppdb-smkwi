<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterBiaya;
use App\Models\GelombangPendaftaran;
use Illuminate\Http\Request;

class MasterBiayaController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterBiaya::with(['gelombang', 'gelombang.tahunAjaran']);

        if ($request->filled('gelombang_id')) {
            $query->where('gelombang_id', $request->gelombang_id);
        }

        if ($request->filled('jenis_biaya')) {
            $query->where('jenis_biaya', $request->jenis_biaya);
        }

        $data = $query->paginate(10)->withQueryString();
        $tahunAjaranAktif = \App\Models\TahunAjaran::where('status', 'aktif')->first();
        $gelombangs = GelombangPendaftaran::with('tahunAjaran')
            ->where('status', 'aktif')
            ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            })
            ->get();
        if ($gelombangs->isEmpty()) {
            $gelombangs = GelombangPendaftaran::with('tahunAjaran')->where('status', 'aktif')->get();
        }

        return view('admin.master-biaya.index', compact('data', 'gelombangs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gelombang_id' => 'required',
            'jenis_biaya' => 'required',
            'nama_biaya' => 'required|string|max:255',
            'total_biaya' => 'required|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        // Jika kosong, set diskon ke 0
        $validated['diskon'] = $request->diskon ?? 0;

        MasterBiaya::create($validated);

        return back()->with('success', 'Biaya berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'gelombang_id' => 'required',
            'jenis_biaya' => 'required',
            'nama_biaya' => 'required|string|max:255',
            'total_biaya' => 'required|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $validated['diskon'] = $request->diskon ?? 0;

        MasterBiaya::findOrFail($id)->update($validated);

        return back()->with('success', 'Biaya berhasil diperbarui.');
    }

    public function destroy($id)
    {
        MasterBiaya::findOrFail($id)->delete();
        return back()->with('success', 'Biaya berhasil dihapus.');
    }

    public function aktifkan($id)
    {
        MasterBiaya::where('id', $id)->update(['status' => true]);
        return back()->with('success', 'Biaya diaktifkan.');
    }

    public function nonaktifkan($id)
    {
        MasterBiaya::where('id', $id)->update(['status' => false]);
        return back()->with('success', 'Biaya dinonaktifkan.');
    }
}
