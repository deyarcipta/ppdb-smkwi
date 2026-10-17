@extends('admin.layouts.app')
@section('title', 'Laporan Pembayaran')

@section('content')
<div class="container-fluid p-0">
    <div class="card card-full-width">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
            <div>
                <h5 class="mb-1"><i class="bx bx-receipt me-2 text-primary"></i>Laporan Pembayaran</h5>
                <small class="text-muted">Rekapitulasi dan laporan rincian pembayaran keuangan pendaftar.</small>
            </div>

            <div class="d-flex align-items-center flex-wrap gap-2">
                <form action="{{ route('laporan-pembayaran.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <select name="jenis_pembayaran" class="form-select form-select-sm" style="width: auto; min-width: 130px;" onchange="this.form.submit()">
                        <option value="">Semua Jenis</option>
                        <option value="formulir" {{ request('jenis_pembayaran') == 'formulir' ? 'selected' : '' }}>Formulir</option>
                        <option value="ppdb" {{ request('jenis_pembayaran') == 'ppdb' ? 'selected' : '' }}>PPDB</option>
                    </select>

                    <div class="d-flex align-items-center gap-1">
                        <input type="date" name="tanggal_awal" class="form-control form-control-sm" style="width: 130px;" value="{{ request('tanggal_awal') }}" title="Tanggal Awal">
                        <span class="text-muted small">-</span>
                        <input type="date" name="tanggal_akhir" class="form-control form-control-sm" style="width: 130px;" value="{{ request('tanggal_akhir') }}" title="Tanggal Akhir">
                    </div>

                    <div class="input-group input-group-sm" style="width: 220px; min-width: 170px;">
                        <input type="text" name="nama_siswa" class="form-control" placeholder="Cari nama siswa..." value="{{ request('nama_siswa') }}">
                        <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i></button>
                    </div>

                    @if(request()->hasAny(['nama_siswa', 'jenis_pembayaran', 'tanggal_awal', 'tanggal_akhir']))
                        <a href="{{ route('laporan-pembayaran.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                            <i class="bx bx-refresh"></i>
                        </a>
                    @endif
                </form>

                <a href="{{ route('laporan-pembayaran.export', request()->all()) }}" class="btn btn-success btn-sm d-flex align-items-center gap-1" title="Download Excel">
                    <i class="bx bx-download"></i>
                    <span>Excel</span>
                </a>

                <div class="d-flex align-items-center gap-2 ms-sm-1">
                    <span class="badge bg-label-primary px-2 py-2" title="Total Siswa">
                        <i class="bx bx-user me-1"></i>Siswa: <strong>{{ $totalSiswa }}</strong>
                    </span>
                    <span class="badge bg-label-info px-2 py-2" title="Total Transaksi">
                        <i class="bx bx-receipt me-1"></i>Trx: <strong>{{ $totalTransaksi }}</strong>
                    </span>
                    <span class="badge bg-label-success px-2 py-2" title="Total Nominal">
                        <i class="bx bx-wallet me-1"></i>Rp {{ number_format($totalNominal, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card-body">

                    <!-- Table Section -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th width="40">#</th>
                                    <th width="140" class="text-center">No Daftar</th>
                                    <th width="280">Nama Siswa</th>
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
                                        <td class="text-center">
                                            <span class="fw-medium">{{ $item->username ?? '-' }}</span>
                                        </td>
                                        <td style="min-width: 260px;">
                                            <div class="fw-bold text-dark">{{ $item->datasiswa->nama_lengkap ?? 'N/A' }}</div>
                                            <small class="text-primary fw-semibold d-block">{{ $item->datasiswa->no_pendaftaran ?? '-' }}</small>
                                            <small class="text-muted">{{ $item->datasiswa->no_hp }}</small>
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
    margin-left: 0;
    margin-right: 0;
    border-radius: 0.75rem;
    margin-bottom: 0 !important;
    border-top: 1px solid #e4e6e8;
    border-bottom: 1px solid #e4e6e8;
}

@media (min-width: 1200px) {
    .card-full-width {
        margin-left: 0;
        margin-right: 0;
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

.table th {
    font-weight: 700;
    background-color: #e9ecef;
    border-bottom: 2px solid #dee2e6;
    padding: 0.65rem 0.75rem;
    font-size: 0.85rem;
    color: #495057;
}

.table td {
    white-space: normal;
    word-wrap: break-word;
    padding: 0.65rem 0.75rem;
    font-size: 0.9375rem;
}

.table-sm th,
.table-sm td {
    padding: 0.65rem 0.75rem;
    font-size: 0.9375rem;
}

.btn-sm.p-1 {
    padding: 0.3rem 0.5rem;
}

.table-responsive {
    overflow-x: auto;
}

.badge {
    font-size: 0.78rem;
    padding: 0.4em 0.75em;
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