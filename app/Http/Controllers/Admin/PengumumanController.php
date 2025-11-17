<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengumumanController extends Controller
{
    public function index()
    {
        $data = Pengumuman::latest()->paginate(10);
        return view('admin.pengumuman.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tanggal' => 'required|date',
        ]);

        $data = $request->all();

        // Upload gambar jika ada
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $filename = time() . '_' . $gambar->getClientOriginalName();
            $gambar->storeAs('public/pengumuman', $filename);
            $data['gambar'] = $filename;
        }

        Pengumuman::create($data);

        return redirect()->route('pengumuman.index')
            ->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tanggal' => 'required|date',
        ]);

        $pengumuman = Pengumuman::findOrFail($id);
        $data = $request->all();

        // Upload gambar baru jika ada
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($pengumuman->gambar && Storage::exists('public/pengumuman/' . $pengumuman->gambar)) {
                Storage::delete('public/pengumuman/' . $pengumuman->gambar);
            }

            $gambar = $request->file('gambar');
            $filename = time() . '_' . $gambar->getClientOriginalName();
            $gambar->storeAs('public/pengumuman', $filename);
            $data['gambar'] = $filename;
        }

        $pengumuman->update($data);

        return redirect()->route('pengumuman.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        // Hapus gambar jika ada
        if ($pengumuman->gambar && Storage::exists('public/pengumuman/' . $pengumuman->gambar)) {
            Storage::delete('public/pengumuman/' . $pengumuman->gambar);
        }

        $pengumuman->delete();

        return redirect()->route('pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    public function aktifkan($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $pengumuman->update(['status' => true]);

        return redirect()->route('pengumuman.index')
            ->with('success', 'Pengumuman berhasil diaktifkan.');
    }

    public function nonaktifkan($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $pengumuman->update(['status' => false]);

        return redirect()->route('pengumuman.index')
            ->with('success', 'Pengumuman berhasil dinonaktifkan.');
    }
}