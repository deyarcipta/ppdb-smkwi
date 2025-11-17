<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestimoniAlumni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimoniAlumniController extends Controller
{
    public function index()
    {
        $data = TestimoniAlumni::latest()->paginate(10);
        return view('admin.testimoni-alumni.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_alumni' => 'required|string|max:255',
            'jurusan' => 'required|string|max:255',
            'testimoni' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'urutan' => 'required|integer|min:1',
        ]);

        $data = $request->all();

        // Upload foto jika ada
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = time() . '_' . $foto->getClientOriginalName();
            $foto->storeAs('public/testimoni-alumni', $filename);
            $data['foto'] = $filename;
        }

        TestimoniAlumni::create($data);

        return redirect()->route('testimoni-alumni.index')
            ->with('success', 'Testimoni alumni berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'headline' => 'required|string|max:255',
            'nama_alumni' => 'required|string|max:255',
            'jurusan' => 'required|string|max:255',
            'pekerjaan' => 'nullable|string|max:255',
            'testimoni' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'urutan' => 'required|integer|min:1',
        ]);

        $testimoni = TestimoniAlumni::findOrFail($id);
        $data = $request->all();

        // Upload foto baru jika ada
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($testimoni->foto && Storage::exists('public/testimoni-alumni/' . $testimoni->foto)) {
                Storage::delete('public/testimoni-alumni/' . $testimoni->foto);
            }

            $foto = $request->file('foto');
            $filename = time() . '_' . $foto->getClientOriginalName();
            $foto->storeAs('public/testimoni-alumni', $filename);
            $data['foto'] = $filename;
        }

        $testimoni->update($data);

        return redirect()->route('testimoni-alumni.index')
            ->with('success', 'Testimoni alumni berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $testimoni = TestimoniAlumni::findOrFail($id);

        // Hapus foto jika ada
        if ($testimoni->foto && Storage::exists('public/testimoni-alumni/' . $testimoni->foto)) {
            Storage::delete('public/testimoni-alumni/' . $testimoni->foto);
        }

        $testimoni->delete();

        return redirect()->route('testimoni-alumni.index')
            ->with('success', 'Testimoni alumni berhasil dihapus.');
    }

    public function aktifkan($id)
    {
        $testimoni = TestimoniAlumni::findOrFail($id);
        $testimoni->update(['status' => true]);

        return redirect()->route('testimoni-alumni.index')
            ->with('success', 'Testimoni berhasil diaktifkan.');
    }

    public function nonaktifkan($id)
    {
        $testimoni = TestimoniAlumni::findOrFail($id);
        $testimoni->update(['status' => false]);

        return redirect()->route('testimoni-alumni.index')
            ->with('success', 'Testimoni berhasil dinonaktifkan.');
    }
}