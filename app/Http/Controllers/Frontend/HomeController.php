<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PengaturanAplikasi;
use App\Models\GelombangPendaftaran;
use App\Models\PersyaratanPendaftaran;
use App\Models\KontakPendaftaran;
use App\Models\TestimoniAlumni;
use App\Models\Faq;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Pengaturan Aplikasi
        $pengaturan = PengaturanAplikasi::first();

        // Ambil data gelombang aktif
        $gelombangAktif = GelombangPendaftaran::with('tahunAjaran')
            ->aktif()
            ->orderBy('tanggal_mulai', 'asc')
            ->get();

        // Ambil data persyaratan dan jadwal dari database
        $jadwalPendaftaran = PersyaratanPendaftaran::tipe('jadwal')
            ->aktif()
            ->orderBy('urutan')
            ->get();

        $persyaratanUmum = PersyaratanPendaftaran::tipe('umum')
            ->aktif()
            ->orderBy('urutan')
            ->get();

        $dokumenPersyaratan = PersyaratanPendaftaran::tipe('dokumen')
            ->aktif()
            ->orderBy('urutan')
            ->get();
        
        // Ambil data kontak pendaftaran aktif
        $kontakPendaftaran = KontakPendaftaran::aktif()->get();

        // Ambil data testimoni alumni aktif
        $testimoniAlumni = TestimoniAlumni::aktif()->urutan()->get();

        // Ambil data FAQ aktif
        $faqs = Faq::aktif()->urutan()->get();

        // Catat visitor
        $this->recordVisitor($request);

        // Ambil statistik visitor
        $statistikVisitor = $this->getVisitorStats();

        return view('frontend.home', compact(
            'pengaturan',
            'gelombangAktif', 
            'statistikVisitor',
            'jadwalPendaftaran',
            'persyaratanUmum',
            'testimoniAlumni',
            'kontakPendaftaran',
            'faqs',
            'dokumenPersyaratan'
        ));
    }

    private function recordVisitor(Request $request)
    {
        $ip = $request->ip();
        $today = Carbon::today();
        $sessionId = $request->session()->getId();
        $pageVisited = $request->path();

        // Cek apakah session ini sudah tercatat untuk halaman ini hari ini
        $existingVisit = Visitor::where('session_id', $sessionId)
            ->where('page_visited', $pageVisited)
            ->where('visit_date', $today)
            ->first();

        if (!$existingVisit) {
            Visitor::create([
                'ip_address' => $ip,
                'user_agent' => $request->userAgent(),
                'page_visited' => $pageVisited,
                'session_id' => $sessionId,
                'visit_date' => $today,
            ]);
        }
    }

    private function getVisitorStats()
    {
        $today = Carbon::today();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        return [
            'hari_ini' => [
                'pageviews' => Visitor::hariIni()->count(),
                'visitors' => Visitor::hariIni()->distinct('session_id')->count('session_id'),
            ],
            'bulan_ini' => [
                'pageviews' => Visitor::bulanIni()->count(),
                'visitors' => Visitor::bulanIni()->distinct('session_id')->count('session_id'),
            ],
            'total' => [
                'pageviews' => Visitor::count(),
                'visitors' => Visitor::distinct('session_id')->count('session_id'),
            ],
            'halaman_ini_hari_ini' => Visitor::hariIni()
                ->halamanIni('/')
                ->count(),
            'halaman_ini_bulan_ini' => Visitor::bulanIni()
                ->halamanIni('/')
                ->count(),
        ];
    }

    // Method untuk halaman lain jika perlu
    public function showPage(Request $request, $page)
    {
        // Catat visitor untuk halaman spesifik
        $this->recordVisitor($request);

        // Logika halaman lainnya...
    }
}