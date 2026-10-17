<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KuotaJurusan;
use App\Models\Jurusan;
use App\Models\GelombangPendaftaran;
use Illuminate\Http\Request;

class KuotaJurusanController extends Controller
{
    public function index(Request $request)
    {
        // Relasi ke jurusan dan gelombang pendaftaran
        $query = KuotaJurusan::with(['jurusan', 'gelombang']);

        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        if ($request->filled('gelombang_id')) {
            $query->where('gelombang_id', $request->gelombang_id);
        }

        $data = $query->paginate(10)->withQueryString();
        $jurusans = Jurusan::where('status', 1)->get();
        $gelombangs = GelombangPendaftaran::where('status', 'aktif')->get();

        return view('admin.kuota-jurusan.index', compact('data', 'jurusans', 'gelombangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jurusan_id' => 'required',
            'gelombang_id' => 'required',
            'kuota' => 'required|integer|min:1',
        ]);

        KuotaJurusan::create($request->all());

        return redirect()->back()->with('success', 'Kuota berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jurusan_id' => 'required',
            'gelombang_id' => 'required',
            'kuota' => 'required|integer|min:1',
        ]);

        $kuota = KuotaJurusan::findOrFail($id);
        $kuota->update($request->all());

        return redirect()->back()->with('success', 'Kuota berhasil diperbarui.');
    }

    public function destroy($id)
    {
        KuotaJurusan::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Kuota berhasil dihapus.');
    }

    public function aktifkan($id)
    {
        KuotaJurusan::where('id', $id)->update(['status' => true]);
        return redirect()->back()->with('success', 'Kuota diaktifkan.');
    }

    public function nonaktifkan($id)
    {
        KuotaJurusan::where('id', $id)->update(['status' => false]);
        return redirect()->back()->with('success', 'Kuota dinonaktifkan.');
    }
}
