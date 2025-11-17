<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterBiaya;
use App\Models\GelombangPendaftaran;
use Illuminate\Http\Request;

class MasterBiayaController extends Controller
{
    public function index()
    {
        $data = MasterBiaya::with('gelombang')->paginate(10);
        $gelombangs = GelombangPendaftaran::where('status', 'aktif')->get();

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
