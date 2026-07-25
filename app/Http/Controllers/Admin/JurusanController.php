<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class JurusanController extends Controller
{
    public function index()
    {
        $jurusans = Jurusan::orderBy('nama_jurusan')->paginate(10);
        return view('admin.jurusan.index', compact('jurusans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_jurusan' => 'required|unique:jurusans,kode_jurusan',
            'nama_jurusan' => 'required',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $iconName = null;
        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $iconName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $destinationPath = public_path('uploads/jurusan');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $iconName);
        }

        Jurusan::create([
            'kode_jurusan' => $request->kode_jurusan,
            'nama_jurusan' => $request->nama_jurusan,
            'deskripsi' => $request->deskripsi,
            'icon' => $iconName,
        ]);

        return redirect()->back()->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_jurusan' => 'required|unique:jurusans,kode_jurusan,' . $id,
            'nama_jurusan' => 'required',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $jurusan = Jurusan::findOrFail($id);
        $iconName = $jurusan->icon;

        if ($request->hasFile('icon')) {
            // Hapus gambar lama jika ada
            if ($jurusan->icon && File::exists(public_path('uploads/jurusan/' . $jurusan->icon))) {
                File::delete(public_path('uploads/jurusan/' . $jurusan->icon));
            }

            $file = $request->file('icon');
            $iconName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $destinationPath = public_path('uploads/jurusan');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $iconName);
        }

        $jurusan->update([
            'kode_jurusan' => $request->kode_jurusan,
            'nama_jurusan' => $request->nama_jurusan,
            'deskripsi' => $request->deskripsi,
            'icon' => $iconName,
        ]);

        return redirect()->back()->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        if ($jurusan->icon && File::exists(public_path('uploads/jurusan/' . $jurusan->icon))) {
            File::delete(public_path('uploads/jurusan/' . $jurusan->icon));
        }
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
