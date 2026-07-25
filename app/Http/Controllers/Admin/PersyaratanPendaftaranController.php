<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PersyaratanPendaftaran;
use Illuminate\Http\Request;

class PersyaratanPendaftaranController extends Controller
{
    public function index()
    {
        $data = PersyaratanPendaftaran::orderBy('tipe')->orderBy('urutan')->paginate(15);
        return view('admin.persyaratan-pendaftaran.index', compact('data'));
    }

    public function create()
    {
        return view('admin.persyaratan-pendaftaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'sub_konten' => 'nullable|string',
            'tipe' => 'required|in:umum,dokumen,jadwal,alur',
            'urutan' => 'required|integer|min:1',
        ]);

        PersyaratanPendaftaran::create($request->all());

        return redirect()->route('persyaratan-pendaftaran.index')
            ->with('success', 'Persyaratan / Alur berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $persyaratan = PersyaratanPendaftaran::findOrFail($id);
        return view('admin.persyaratan-pendaftaran.edit', compact('persyaratan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'sub_konten' => 'nullable|string',
            'tipe' => 'required|in:umum,dokumen,jadwal,alur',
            'urutan' => 'required|integer|min:1',
        ]);

        $persyaratan = PersyaratanPendaftaran::findOrFail($id);
        $persyaratan->update($request->all());

        return redirect()->route('persyaratan-pendaftaran.index')
            ->with('success', 'Persyaratan / Alur berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $persyaratan = PersyaratanPendaftaran::findOrFail($id);
        $persyaratan->delete();

        return redirect()->route('persyaratan-pendaftaran.index')
            ->with('success', 'Persyaratan / Alur berhasil dihapus.');
    }

    public function aktifkan($id)
    {
        $persyaratan = PersyaratanPendaftaran::findOrFail($id);
        $persyaratan->update(['status' => true]);

        return redirect()->route('persyaratan-pendaftaran.index')
            ->with('success', 'Persyaratan / Alur berhasil diaktifkan.');
    }

    public function nonaktifkan($id)
    {
        $persyaratan = PersyaratanPendaftaran::findOrFail($id);
        $persyaratan->update(['status' => false]);

        return redirect()->route('persyaratan-pendaftaran.index')
            ->with('success', 'Persyaratan / Alur berhasil dinonaktifkan.');
    }
}