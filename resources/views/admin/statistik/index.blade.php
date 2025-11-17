@extends('admin.layouts.app')
@section('title', 'Data Statistik Pendaftar')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y" style="padding-top: 0.75rem !important; padding-bottom: 0.25rem !important;">
    <!-- Statistik Visitor -->
    <div class="row" style="margin-bottom: 0.75rem !important;">
        <div class="col-12">
            <div class="card bg-dark text-white">
                <div class="card-body py-2">
                    <h6 class="card-title text-white mb-2">
                        <i class="bx bx-stats"></i> Statistik Website PPDB Wistin
                    </h6>
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-12 mb-2">
                            <div class="text-center">
                                <div class="fs-5 fw-bold">{{ number_format($visitorStats['pageviews_today']) }}</div>
                                <small class="text-white-50">Pageview Hari Ini</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 mb-2">
                            <div class="text-center">
                                <div class="fs-5 fw-bold">{{ number_format($visitorStats['visitors_today']) }}</div>
                                <small class="text-white-50">Visitor Hari Ini</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 mb-2">
                            <div class="text-center">
                                <div class="fs-5 fw-bold">{{ number_format($visitorStats['visitors_this_month']) }}</div>
                                <small class="text-white-50">Visitor Bulan Ini</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 mb-2">
                            <div class="text-center">
                                <div class="fs-5 fw-bold">{{ number_format($visitorStats['total_visitors']) }}</div>
                                <small class="text-white-50">Total Visitor</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="margin-bottom: 0 !important;">
        <div class="col-12">
            <div class="card" style="margin-bottom: 0.5rem !important;">
                <div class="card-body p-3">
                    <h5 class="card-title text-primary mb-2">Dashboard Statistik Pendaftar</h5>
                    
                    <!-- Card Total Pendaftar -->
                    <div class="row" style="margin-bottom: 0.75rem !important;">
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="card bg-primary text-white">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar flex-shrink-0 me-2">
                                            <i class="bx bx-user-plus" style="font-size: 1.25rem;"></i>
                                        </div>
                                        <div>
                                            <span class="fw-semibold d-block mb-0" style="font-size: 0.85rem;">Total Pendaftar</span>
                                            <h5 class="card-title mb-0">{{ number_format($totalPendaftar) }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart dan Tabel Referensi -->
                    <div class="row" style="margin-bottom: 1rem !important;">
                        <!-- Chart Referensi -->
                        <div class="col-lg-6 col-md-6" style="margin-bottom: 0.5rem !important;">
                            <div class="card">
                                <div class="card-header py-1">
                                    <h6 class="card-title mb-0">Grafik Statistik Referensi Pendaftar</h6>
                                </div>
                                <div class="card-body p-2">
                                    <canvas id="referensiChart" height="220"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Statistik Referensi -->
                        <div class="col-lg-6 col-md-6" style="margin-bottom: 0 !important;">
                            <div class="card">
                                <div class="card-header py-1 d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0">Data Statistik Referensi</h6>
                                </div>
                                <div class="card-body p-2">
                                    <div class="table-responsive" style="max-height: 280px;">
                                        <table class="table table-sm table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="py-1">Referensi</th>
                                                    <th class="py-1">Jumlah</th>
                                                    <th class="py-1">Persentase</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($statistikReferensi as $item)
                                                <tr>
                                                    <td class="py-1">
                                                        @switch($item->referensi)
                                                            @case('guru-staff')
                                                                Guru/Staff/Laboran/Pegawai
                                                                @break
                                                            @case('siswa')
                                                                Siswa SMK Wisata Indonesia
                                                                @break
                                                            @case('alumni')
                                                                Alumni SMK Wisata Indonesia
                                                                @break
                                                            @case('guru-smp')
                                                                Guru SMP
                                                                @break
                                                            @case('calon-siswa')
                                                                Calon Siswa
                                                                @break
                                                            @case('sosial-media')
                                                                Sosial Media
                                                                @break
                                                            @case('referensi-langsung')
                                                                Referensi Langsung
                                                                @break
                                                            @default
                                                                {{ $item->referensi }}
                                                        @endswitch
                                                    </td>
                                                    <td class="py-1">{{ number_format($item->total) }}</td>
                                                    <td class="py-1">
                                                        {{ number_format(($item->total / $totalPendaftar) * 100, 2) }}%
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart dan Tabel Sekolah Asal -->
                    <div class="row" style="margin-bottom: 0 !important;">
                        <!-- Chart Sekolah Asal -->
                        <div class="col-lg-6 col-md-6" style="margin-bottom: 0.5rem !important;">
                            <div class="card">
                                <div class="card-header py-1">
                                    <h6 class="card-title mb-0">Grafik Statistik Sekolah Asal</h6>
                                </div>
                                <div class="card-body p-2">
                                    <canvas id="sekolahChart" height="220"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Statistik Sekolah Asal -->
                        <div class="col-lg-6 col-md-6" style="margin-bottom: 0 !important;">
                            <div class="card">
                                <div class="card-header py-1 d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0">Data Statistik Sekolah Asal</h6>
                                    <small class="text-muted">Top {{ $statistikSekolah->count() }} Sekolah</small>
                                </div>
                                <div class="card-body p-2">
                                    <div class="table-responsive" style="max-height: 280px;">
                                        <table class="table table-sm table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="py-1">Sekolah Asal</th>
                                                    <th class="py-1">Jumlah</th>
                                                    <th class="py-1">Persentase</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($statistikSekolah as $item)
                                                <tr>
                                                    <td class="py-1">
                                                        {{ $item->asal_sekolah ?: 'Tidak Diketahui' }}
                                                    </td>
                                                    <td class="py-1">{{ number_format($item->total) }}</td>
                                                    <td class="py-1">
                                                        {{ number_format(($item->total / $totalPendaftar) * 100, 2) }}%
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.container-xxl.flex-grow-1.container-p-y {
    padding-top: 0.75rem !important;
    padding-bottom: 0.25rem !important;
}

.card {
    margin-bottom: 0.5rem !important;
}

.row {
    margin-bottom: 0.5rem !important;
}

.content-wrapper {
    padding-bottom: 0 !important;
}

.card-title {
    font-size: 1rem !important;
}

.table-sm th,
.table-sm td {
    font-size: 0.875rem !important;
    padding: 0.4rem 0.5rem !important;
}

#referensiChart,
#sekolahChart {
    height: 220px !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart Referensi
    const ctxReferensi = document.getElementById('referensiChart').getContext('2d');
    const chartDataReferensi = {
        labels: {!! json_encode($chartDataReferensi['labels']) !!},
        datasets: [{
            data: {!! json_encode($chartDataReferensi['data']) !!},
            backgroundColor: {!! json_encode($chartDataReferensi['colors']) !!},
            borderWidth: 1
        }]
    };

    const referensiChart = new Chart(ctxReferensi, {
        type: 'pie',
        data: chartDataReferensi,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        font: {
                            size: 11
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Chart Sekolah Asal
    const ctxSekolah = document.getElementById('sekolahChart').getContext('2d');
    const chartDataSekolah = {
        labels: {!! json_encode($chartDataSekolah['labels']) !!},
        datasets: [{
            data: {!! json_encode($chartDataSekolah['data']) !!},
            backgroundColor: {!! json_encode($chartDataSekolah['colors']) !!},
            borderWidth: 1
        }]
    };

    const sekolahChart = new Chart(ctxSekolah, {
        type: 'pie',
        data: chartDataSekolah,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        font: {
                            size: 11
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush