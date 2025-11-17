@extends('admin.layouts.app')
@section('title', 'Laporan Pembayaran')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Section -->
    <div class="row mb-4 mx-0">
        <div class="col-12 px-3">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between py-3">
                <h4 class="mb-sm-0">Laporan Pembayaran Siswa</h4>
                <div class="d-flex gap-4">
                    <div class="text-muted">
                        <small>Total Siswa: <span class="badge bg-primary">{{ $totalSiswa }}</span></small>
                    </div>
                    <div class="text-muted">
                        <small>Total Transaksi: <span class="badge bg-info">{{ $totalTransaksi }}</span></small>
                    </div>
                    <div class="text-muted">
                        <small>Total Nominal: <span class="badge bg-success text-white">Rp {{ number_format($totalNominal, 0, ',', '.') }}</span></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="row mx-0">
        <div class="col-12 px-3">
            <div class="card card-full-width">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bx bx-receipt me-2"></i>
                        Laporan Pembayaran Siswa
                    </h5>
                </div>

                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <!-- Filter Section -->
                            <div class="filter-section p-3 bg-light rounded">
                                <div class="row g-3 align-items-end">
                                    <!-- Form Filter -->
                                    <form action="{{ route('laporan-pembayaran.filter') }}" method="GET" class="row g-3 align-items-end col-md-11">
                                        <div class="col-md-3">
                                            <label class="form-label small fw-semibold text-muted">Nama Siswa</label>
                                            <input type="text" name="nama_siswa" class="form-control form-control-sm" 
                                                value="{{ request('nama_siswa') }}" placeholder="Cari nama siswa...">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-semibold text-muted">Jenis Pembayaran</label>
                                            <select name="jenis_pembayaran" class="form-select form-select-sm">
                                                <option value="">Semua Jenis</option>
                                                <option value="formulir" {{ request('jenis_pembayaran') == 'formulir' ? 'selected' : '' }}>Formulir</option>
                                                <option value="ppdb" {{ request('jenis_pembayaran') == 'ppdb' ? 'selected' : '' }}>PPDB</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-semibold text-muted">Tanggal Awal</label>
                                            <input type="date" name="tanggal_awal" class="form-control form-control-sm" 
                                                value="{{ request('tanggal_awal') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-semibold text-muted">Tanggal Akhir</label>
                                            <input type="date" name="tanggal_akhir" class="form-control form-control-sm" 
                                                value="{{ request('tanggal_akhir') }}">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                                <i class="bx bx-filter me-1"></i> Filter
                                            </button>
                                        </div>
                                        <div class="col-md-1">
                                            <a href="{{ route('laporan-pembayaran.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                                                <i class="bx bx-refresh me-1"></i> Reset
                                            </a>
                                        </div>
                                    </form>

                                    <!-- Tombol Export Excel (di luar form utama tapi sejajar) -->
                                    <div class="col-md-1">
                                        <form action="{{ route('laporan-pembayaran.export') }}" method="GET">
                                            <input type="hidden" name="nama_siswa" value="{{ request('nama_siswa') }}">
                                            <input type="hidden" name="jenis_pembayaran" value="{{ request('jenis_pembayaran') }}">
                                            <input type="hidden" name="tanggal_awal" value="{{ request('tanggal_awal') }}">
                                            <input type="hidden" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">
                                            <button type="submit" class="btn btn-success btn-sm w-100" title="Download Excel">
                                                <i class="bx bx-download me-1"></i> Excel
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Table Section -->
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th width="40">#</th>
                                    <th width="120">No Pendaftaran</th>
                                    <th width="200">Nama Siswa</th>
                                    <th width="100">Jumlah Cicilan</th>
                                    <th width="150">Total Pembayaran</th>
                                    <th width="120">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($siswa as $index => $item)
                                    @php
                                        $totalPembayaran = $item->pembayaran->sum('jumlah');
                                        $jumlahCicilan = $item->pembayaran->count();
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $siswa->firstItem() + $index }}</td>
                                        <td class="">
                                            <strong class="text-primary">{{ $item->datasiswa->no_pendaftaran ?? '-' }}</strong>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">{{ $item->datasiswa->nama_lengkap ?? 'N/A' }}</h6>
                                                    <small class="text-muted">{{ $item->datasiswa->no_hp }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info text-white">
                                                {{ $jumlahCicilan }} kali
                                            </span>
                                        </td>
                                        <td class="text-center fw-bold text-success">
                                            Rp {{ number_format($totalPembayaran, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex gap-1 justify-content-center">
                                                <!-- Tombol Detail -->
                                                <button class="btn btn-primary btn-sm p-1 btn-detail" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#detailModal"
                                                        data-id="{{ $item->id }}"
                                                        title="Detail Pembayaran">
                                                    <i class="bx bx-show fs-12"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bx bx-receipt display-4"></i>
                                                <p class="mt-2">Belum ada data pembayaran</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Component -->
                    <x-pagination :paginator="$siswa" />
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bx bx-detail me-2"></i>
                    Detail Pembayaran Siswa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Content will be loaded here via AJAX -->
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat data pembayaran...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card-full-width {
    margin-left: -1.5rem;
    margin-right: -1.5rem;
    border-radius: 0;
    border-left: none;
    border-right: none;
    margin-bottom: 0 !important;
    box-shadow: none;
    border-top: 1px solid #e4e6e8;
    border-bottom: 1px solid #e4e6e8;
}

@media (min-width: 1200px) {
    .card-full-width {
        margin-left: -2rem;
        margin-right: -2rem;
    }
}

.container-fluid.p-0 {
    padding: 0 !important;
}

.badge.text-white {
    color: white !important;
    font-weight: 500;
}

.fs-12 {
    font-size: 12px;
}

.table-sm th,
.table-sm td {
    padding: 0.4rem 0.5rem;
    font-size: 0.875rem;
}

.btn-sm.p-1 {
    padding: 0.25rem 0.4rem;
}

.table-responsive {
    overflow-x: auto;
}

.table td {
    white-space: normal;
    word-wrap: break-word;
}

.table th {
    font-weight: 600;
    background-color: #f8f9fa;
    border-bottom: 2px solid #e4e6e8;
}

.badge {
    font-size: 0.7rem;
    padding: 0.35em 0.65em;
}

.filter-section {
    background-color: #f8f9fc;
    border: 1px solid #e3e6f0;
}

.page-title-box {
    border-bottom: 1px solid #e3e6f0;
    padding-bottom: 1.5rem;
    margin-bottom: 1.5rem;
}

/* Pagination Styles */
.pagination {
    margin-bottom: 0;
}

.page-link {
    border: 1px solid #dee2e6;
    color: #6c757d;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
}

.page-link:hover {
    color: #0056b3;
    background-color: #e9ecef;
    border-color: #dee2e6;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle modal show event
    const detailButtons = document.querySelectorAll('.btn-detail');
    
    detailButtons.forEach(button => {
        button.addEventListener('click', function() {
            const siswaId = this.getAttribute('data-id');
            const modalBody = document.getElementById('modalBody');
            
            // Show loading
            modalBody.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat data pembayaran...</p>
                </div>
            `;
            
            // Load data via AJAX
            const url = `{{ route('laporan-pembayaran.detail', '') }}/${siswaId}`;
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.text();
            })
            .then(html => {
                modalBody.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bx bx-error-alt me-2"></i>
                        Gagal memuat data. Silakan coba lagi.
                    </div>
                `;
            });
        });
    });
    
    // Clear modal content when hidden
    const detailModal = document.getElementById('detailModal');
    if (detailModal) {
        detailModal.addEventListener('hidden.bs.modal', function () {
            const modalBody = document.getElementById('modalBody');
            modalBody.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat data pembayaran...</p>
                </div>
            `;
        });
    }
});
</script>
@endpush