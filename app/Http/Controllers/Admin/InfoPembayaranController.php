<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InfoPembayaran;
use Illuminate\Http\Request;

class InfoPembayaranController extends Controller
{
    public function index()
    {
        $data = InfoPembayaran::latest()->paginate(10);
        return view('admin.info-pembayaran.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bank' => 'required|string|max:255',
            'nomor_rekening' => 'required|string|max:50',
            'atas_nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        InfoPembayaran::create($request->all());

        return redirect()->route('info-pembayaran.index')
            ->with('success', 'Info pembayaran berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_bank' => 'required|string|max:255',
            'nomor_rekening' => 'required|string|max:50',
            'atas_nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $info = InfoPembayaran::findOrFail($id);
        $info->update($request->all());

        return redirect()->route('info-pembayaran.index')
            ->with('success', 'Info pembayaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $info = InfoPembayaran::findOrFail($id);
        $info->delete();

        return redirect()->route('info-pembayaran.index')
            ->with('success', 'Info pembayaran berhasil dihapus.');
    }

    public function aktifkan($id)
    {
        $info = InfoPembayaran::findOrFail($id);
        $info->update(['status' => true]);

        return redirect()->route('info-pembayaran.index')
            ->with('success', 'Info pembayaran berhasil diaktifkan.');
    }

    public function nonaktifkan($id)
    {
        $info = InfoPembayaran::findOrFail($id);
        $info->update(['status' => false]);

        return redirect()->route('info-pembayaran.index')
            ->with('success', 'Info pembayaran berhasil dinonaktifkan.');
    }
}