<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AktivitasController extends Controller
{
    /**
     * Menampilkan daftar aktivitas
     */
    public function index()
    {
        $aktivitas = ActivityLog::with('admin')
            ->latest()
            ->paginate(20);

        // Kirim statistik ke view
        $totalCreate = ActivityLog::where('action', 'create')->count();
        $totalVerify = ActivityLog::where('action', 'verify')->count();
        $totalDelete = ActivityLog::where('action', 'delete')->count();

        return view('admin.aktivitas.index', compact(
            'aktivitas', 
            'totalCreate', 
            'totalVerify', 
            'totalDelete'
        ));
    }

    /**
     * Mencari aktivitas
     */
    public function search(Request $request)
    {
        $keyword = $request->get('search');
        
        $aktivitas = ActivityLog::with('admin')
            ->where(function($query) use ($keyword) {
                $query->where('description', 'like', "%{$keyword}%")
                      ->orWhere('action', 'like', "%{$keyword}%")
                      ->orWhere('ip_address', 'like', "%{$keyword}%")
                      ->orWhereHas('admin', function($q) use ($keyword) {
                          $q->where('name', 'like', "%{$keyword}%");
                      });
            })
            ->latest()
            ->paginate(20);

        // Hitung statistik untuk halaman search juga
        $totalCreate = ActivityLog::where('action', 'create')->count();
        $totalVerify = ActivityLog::where('action', 'verify')->count();
        $totalDelete = ActivityLog::where('action', 'delete')->count();

        return view('admin.aktivitas.index', compact(
            'aktivitas', 
            'keyword',
            'totalCreate', 
            'totalVerify', 
            'totalDelete'
        ));
    }

    /**
     * Menghapus aktivitas tertentu
     */
    public function destroy($id)
    {
        $aktivitas = ActivityLog::findOrFail($id);
        $aktivitas->delete();

        return redirect()->route('admin.aktivitas.index')
            ->with('success', 'Aktivitas berhasil dihapus');
    }

    /**
     * Menghapus semua aktivitas
     */
    public function clear()
    {
        ActivityLog::truncate();

        return redirect()->route('admin.aktivitas.index')
            ->with('success', 'Semua aktivitas berhasil dihapus');
    }
}