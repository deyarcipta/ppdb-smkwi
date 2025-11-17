<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KontakPendaftaran;
use Illuminate\Http\Request;

class KontakPendaftaranController extends Controller
{
    public function index()
    {
        $data = KontakPendaftaran::latest()->paginate(10);
        return view('admin.kontak-pendaftaran.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kontak' => 'required|string|max:255',
            'no_kontak' => 'required|string|max:20',
        ]);

        KontakPendaftaran::create($request->all());

        return redirect()->route('kontak-pendaftaran.index')
            ->with('success', 'Kontak pendaftaran berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kontak' => 'required|string|max:255',
            'no_kontak' => 'required|string|max:20',
        ]);

        $kontak = KontakPendaftaran::findOrFail($id);
        $kontak->update($request->all());

        return redirect()->route('kontak-pendaftaran.index')
            ->with('success', 'Kontak pendaftaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kontak = KontakPendaftaran::findOrFail($id);
        $kontak->delete();

        return redirect()->route('kontak-pendaftaran.index')
            ->with('success', 'Kontak pendaftaran berhasil dihapus.');
    }

    public function aktifkan($id)
    {
        $kontak = KontakPendaftaran::findOrFail($id);
        $kontak->update(['status' => true]);

        return redirect()->route('kontak-pendaftaran.index')
            ->with('success', 'Kontak berhasil diaktifkan.');
    }

    public function nonaktifkan($id)
    {
        $kontak = KontakPendaftaran::findOrFail($id);
        $kontak->update(['status' => false]);

        return redirect()->route('kontak-pendaftaran.index')
            ->with('success', 'Kontak berhasil dinonaktifkan.');
    }
}