<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GelombangPendaftaran;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class GelombangPendaftaranController extends Controller
{
    public function index()
    {
        $data = GelombangPendaftaran::with('tahunAjaran')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $tahunAjaran = TahunAjaran::orderBy('nama', 'desc')->get();
        
        return view('admin.gelombang.index', compact('data', 'tahunAjaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_gelombang' => 'required|string|max:255',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        GelombangPendaftaran::create([
            'nama_gelombang' => $request->nama_gelombang,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => 'nonaktif' // Default status nonaktif
        ]);

        return redirect()->back()->with('success', 'Gelombang pendaftaran berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_gelombang' => 'required|string|max:255',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $gelombang = GelombangPendaftaran::findOrFail($id);
        $gelombang->update([
            'nama_gelombang' => $request->nama_gelombang,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        return redirect()->back()->with('success', 'Data gelombang berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $gelombang = GelombangPendaftaran::findOrFail($id);
        $gelombang->delete();

        return redirect()->back()->with('success', 'Gelombang pendaftaran berhasil dihapus!');
    }

    public function aktifkan($id)
    {
        // Nonaktifkan semua gelombang terlebih dahulu
        GelombangPendaftaran::where('status', 'aktif')->update(['status' => 'nonaktif']);
        
        // Aktifkan gelombang yang dipilih
        GelombangPendaftaran::where('id', $id)->update(['status' => 'aktif']);
        
        return redirect()->back()->with('success', 'Gelombang pendaftaran telah diaktifkan.');
    }

    public function nonaktifkan($id)
    {
        GelombangPendaftaran::where('id', $id)->update(['status' => 'nonaktif']);
        return redirect()->back()->with('success', 'Gelombang pendaftaran telah dinonaktifkan.');
    }
}