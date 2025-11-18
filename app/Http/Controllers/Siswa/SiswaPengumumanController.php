<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;

class SiswaPengumumanController extends Controller
{
    /**
     * Menampilkan daftar pengumuman
     */
    public function index()
    {
        $pengumuman = Pengumuman::aktif()
            ->terbaru()
            ->paginate(10);

        return view('siswa.pengumuman.index', compact('pengumuman'));
    }

    /**
     * Menampilkan detail pengumuman
     */
    public function show($id)
    {
        $pengumuman = Pengumuman::aktif()
            ->findOrFail($id);

        // Pengumuman terkait (kecuali yang sedang dilihat)
        $pengumumanTerkait = Pengumuman::aktif()
            ->where('id', '!=', $id)
            ->terbaru()
            ->limit(5)
            ->get();

        return view('siswa.pengumuman.show', compact('pengumuman', 'pengumumanTerkait'));
    }

    /**
     * Mencari pengumuman
     */
    public function search(Request $request)
    {
        $keyword = $request->get('search');
        
        $pengumuman = Pengumuman::aktif()
            ->where(function($query) use ($keyword) {
                $query->where('judul', 'like', "%{$keyword}%")
                      ->orWhere('isi', 'like', "%{$keyword}%");
            })
            ->terbaru()
            ->paginate(10);

        return view('siswa.pengumuman.index', compact('pengumuman', 'keyword'));
    }
}