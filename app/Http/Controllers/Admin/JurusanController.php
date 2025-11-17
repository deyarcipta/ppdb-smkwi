<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index()
    {
        $jurusans = Jurusan::orderBy('nama_jurusan')->paginate(10);;
        return view('admin.jurusan.index', compact('jurusans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_jurusan' => 'required|unique:jurusans,kode_jurusan',
            'nama_jurusan' => 'required',
        ]);

        Jurusan::create([
            'kode_jurusan' => $request->kode_jurusan,
            'nama_jurusan' => $request->nama_jurusan,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->back()->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_jurusan' => 'required|unique:jurusans,kode_jurusan,' . $id,
            'nama_jurusan' => 'required',
        ]);

        $jurusan = Jurusan::findOrFail($id);
        $jurusan->update([
            'kode_jurusan' => $request->kode_jurusan,
            'nama_jurusan' => $request->nama_jurusan,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->back()->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        $jurusan->delete();
        return redirect()->back()->with('success', 'Jurusan berhasil dihapus.');
    }

    public function aktifkan($id)
    {
        Jurusan::where('id', $id)->update(['status' => true]);
        return redirect()->back()->with('success', 'Jurusan berhasil diaktifkan.');
    }

    public function nonaktifkan($id)
    {
        Jurusan::where('id', $id)->update(['status' => false]);
        return redirect()->back()->with('success', 'Jurusan berhasil dinonaktifkan.');
    }
}
