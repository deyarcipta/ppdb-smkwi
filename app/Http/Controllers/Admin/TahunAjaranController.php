<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $data = TahunAjaran::orderBy('id', 'desc')->paginate(10);
        return view('admin.tahun-ajaran.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama' => 'required']);
        TahunAjaran::create(['nama' => $request->nama]);
        return back()->with('success', 'Tahun ajaran berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nama' => 'required']);
        $ta = TahunAjaran::findOrFail($id);
        $ta->update(['nama' => $request->nama]);
        return back()->with('success', 'Tahun ajaran berhasil diperbarui');
    }

    public function destroy($id)
    {
        $tahun = TahunAjaran::findOrFail($id);
        $tahun->delete();

        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Data tahun ajaran berhasil dihapus!');
    }

    public function aktifkan($id)
    {
        TahunAjaran::query()->update(['status' => 'nonaktif']);
        TahunAjaran::findOrFail($id)->update(['status' => 'aktif']);

        // Sinkronkan ke pengaturan aplikasi
        $pengaturan = \App\Models\PengaturanAplikasi::first();
        if ($pengaturan) {
            $pengaturan->update(['tahun_ajaran_id' => $id]);
        }

        return back()->with('success', 'Tahun ajaran berhasil diaktifkan dan disinkronkan ke seluruh sistem');
    }

    public function nonaktifkan($id)
    {
        $tahun = TahunAjaran::findOrFail($id);
        $tahun->update(['status' => 'nonaktif']);

        // Sinkronkan ke pengaturan aplikasi jika tahun ajaran ini yang aktif
        $pengaturan = \App\Models\PengaturanAplikasi::first();
        if ($pengaturan && $pengaturan->tahun_ajaran_id == $id) {
            $pengaturan->update(['tahun_ajaran_id' => null]);
        }

        return redirect()->route('tahun-ajaran.index')->with('success', 'Tahun ajaran dinonaktifkan.');
    }
}
