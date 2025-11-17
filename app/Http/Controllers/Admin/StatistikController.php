<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataSiswa;
use App\Models\Visitor;
use Illuminate\Support\Facades\DB;

class StatistikController extends Controller
{
    public function index()
    {
        // Statistik Referensi Pendaftar
        $statistikReferensi = DataSiswa::select(
                'referensi',
                DB::raw('COUNT(*) as total')
            )
            ->whereNotNull('referensi')
            ->where('referensi', '!=', '')
            ->groupBy('referensi')
            ->get();

        $chartDataReferensi = [
            'labels' => $statistikReferensi->pluck('referensi'),
            'data' => $statistikReferensi->pluck('total'),
            'colors' => $this->generateChartColors($statistikReferensi->count())
        ];

        // Statistik Sekolah Asal
        $statistikSekolah = DataSiswa::select(
                'asal_sekolah',
                DB::raw('COUNT(*) as total')
            )
            ->whereNotNull('asal_sekolah')
            ->where('asal_sekolah', '!=', '')
            ->groupBy('asal_sekolah')
            ->orderBy('total', 'desc')
            ->get();

        $chartDataSekolah = [
            'labels' => $statistikSekolah->pluck('asal_sekolah'),
            'data' => $statistikSekolah->pluck('total'),
            'colors' => $this->generateChartColors($statistikSekolah->count())
        ];

        $totalPendaftar = DataSiswa::count();

        // Statistik Visitor
        $visitorStats = $this->getVisitorStats();

        return view('admin.statistik.index', compact(
            'statistikReferensi', 
            'chartDataReferensi',
            'statistikSekolah',
            'chartDataSekolah',
            'totalPendaftar',
            'visitorStats'
        ));
    }

    /**
     * Get visitor statistics
     */
    private function getVisitorStats()
    {
        // Pageview Hari Ini (semua kunjungan)
        $pageviewsToday = Visitor::hariIni()->count();

        // Visitor Hari Ini (unik berdasarkan session)
        $visitorsToday = Visitor::hariIni()->distinct('session_id')->count('session_id');

        // Visitor Bulan Ini (unik berdasarkan session)
        $visitorsThisMonth = Visitor::bulanIni()->distinct('session_id')->count('session_id');

        // Total Visitor (unik berdasarkan session)
        $totalVisitors = Visitor::count();

        return [
            'pageviews_today' => $pageviewsToday,
            'visitors_today' => $visitorsToday,
            'visitors_this_month' => $visitorsThisMonth,
            'total_visitors' => $totalVisitors
        ];
    }

    private function generateChartColors($count)
    {
        $colors = [];
        $baseColors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
            '#9966FF', '#FF9F40', '#8AC926', '#FF595E',
            '#1982C4', '#6A4C93', '#F15BB5', '#00BBF9',
            '#FB5607', '#8338EC', '#3A86FF', '#FF006E'
        ];

        for ($i = 0; $i < $count; $i++) {
            $colors[] = $baseColors[$i % count($baseColors)];
        }

        return $colors;
    }
}