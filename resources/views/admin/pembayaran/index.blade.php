@extends('admin.layouts.app')
@section('title', 'Data Pembayaran')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Section -->
    <div class="row mb-4 mx-0">
        <div class="col-12 px-3">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between py-3">
                <h4 class="mb-sm-0">Data Pembayaran</h4>
                <div class="d-flex gap-4">
                    <div class="text-muted">
                        <small>Total Terverifikasi: <span class="badge bg-success">{{ $totalTerverifikasi }}</span></small>
                    </div>
                    <div class="text-muted">
                        <small>Total Ditolak: <span class="badge bg-danger">{{ $totalDitolak }}</span></small>
                    </div>
                    <div class="text-muted">
                        <small>Total Nominal: <span class="badge bg-info text-white">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="row mx-0">
        <div class="col-12 px-3">
            <div class="card">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-credit-card me-2"></i>
                        Data Pembayaran Terverifikasi
                    </h5>
                </div>

                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="filter-section p-3 bg-light rounded">
                                <form method="GET" class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold text-muted">Status</label>
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="">Semua Status</option>
                                            @foreach($statuses as $key => $value)
                                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold text-muted">Jenis Pembayaran</label>
                                        <select name="jenis_pembayaran" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="">Semua Jenis</option>
                                            @foreach($jenisPembayaran as $key => $value)
                                                <option value="{{ $key }}" {{ request('jenis_pembayaran') == $key ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-muted">Pencarian</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                                            <input type="text" name="search" class="form-control" 
                                                   placeholder="Cari no pendaftaran, nama siswa, atau metode..." 
                                                   value="{{ request('search') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                            <i class="bx bx-filter me-1"></i> Filter
                                        </button>
                                    </div>
                                    <div class="col-md-1">
                                        <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                                            <i class="bx bx-refresh me-1"></i> Reset
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Table Section -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="60" class="text-center">#</th>
                                    <th width="150">No Pendaftaran</th>
                                    <th>Nama Siswa</th>
                                    <th width="150" class="text-center">Jenis</th>
                                    <th width="130" class="text-end">Jumlah</th>
                                    <th width="120" class="text-center">Tanggal Bayar</th>
                                    <th width="120" class="text-center">Metode</th>
                                    <th width="120" class="text-center">Status</th>
                                    <th width="150" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $key => $item)
                                <tr>
                                    <td class="text-center">
                                        <span class="text-muted">{{ $data->firstItem() + $key }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-primary">{{ $item->no_pendaftaran }}</strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $item->nama_siswa }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2">
                                            {{ $jenisPembayaran[$item->jenis_pembayaran] ?? $item->jenis_pembayaran }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-success">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($item->tanggal_bayar)
                                            <span class="text-muted small">
                                                {{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                            {{ $item->metode_pembayaran ?? 'Transfer' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($item->status == 'diverifikasi')
                                            <span class="badge bg-success">
                                                <i class="bx bx-check-circle me-1"></i>
                                                Terverifikasi
                                            </span>
                                        @elseif($item->status == 'ditolak')
                                            <span class="badge bg-danger">
                                                <i class="bx bx-x-circle me-1"></i>
                                                Ditolak
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="bx bx-time me-1"></i>
                                                Menunggu
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Tombol Detail -->
                                            <button class="btn btn-info btn-sm px-3" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#detailModal{{ $item->id }}"
                                                    title="Lihat Detail">
                                                <i class="bx bx-show-alt me-1"></i> Detail
                                            </button>

                                            <!-- Tombol Download Bukti -->
                                            @if($item->bukti_pembayaran)
                                            <a href="{{ route('pembayaran.download-bukti', $item->id) }}" 
                                               class="btn btn-success btn-sm px-3" title="Download Bukti">
                                                <i class="bx bx-download me-1"></i> Download
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <!-- Detail Modal -->
                                <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">
                                                    <i class="bx bx-detail me-2"></i>
                                                    Detail Pembayaran
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <!-- Informasi Siswa -->
                                                    <div class="col-md-6 mb-4">
                                                        <div class="card border-0 bg-light">
                                                            <div class="card-header bg-transparent border-bottom py-3">
                                                                <h6 class="mb-0 text-primary">
                                                                    <i class="bx bx-user me-2"></i>
                                                                    Informasi Siswa
                                                                </h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="info-item mb-2">
                                                                    <small class="text-muted d-block">No Pendaftaran</small>
                                                                    <strong class="text-primary">{{ $item->no_pendaftaran }}</strong>
                                                                </div>
                                                                <div class="info-item mb-2">
                                                                    <small class="text-muted d-block">Nama Siswa</small>
                                                                    <strong>{{ $item->nama_siswa }}</strong>
                                                                </div>
                                                                <div class="info-item">
                                                                    <small class="text-muted d-block">Jenis Pembayaran</small>
                                                                    <span class="badge bg-primary">{{ $jenisPembayaran[$item->jenis_pembayaran] ?? $item->jenis_pembayaran }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Informasi Pembayaran -->
                                                    <div class="col-md-6 mb-4">
                                                        <div class="card border-0 bg-light">
                                                            <div class="card-header bg-transparent border-bottom py-3">
                                                                <h6 class="mb-0 text-primary">
                                                                    <i class="bx bx-credit-card me-2"></i>
                                                                    Informasi Pembayaran
                                                                </h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="info-item mb-2">
                                                                    <small class="text-muted d-block">Jumlah</small>
                                                                    <strong class="text-success fs-5">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</strong>
                                                                </div>
                                                                <div class="info-item mb-2">
                                                                    <small class="text-muted d-block">Metode Bayar</small>
                                                                    <span>{{ $item->metode_pembayaran ?? 'Transfer' }}</span>
                                                                </div>
                                                                <div class="info-item mb-2">
                                                                    <small class="text-muted d-block">Tanggal Bayar</small>
                                                                    <span>{{ $item->tanggal_bayar ? \Carbon\Carbon::parse($item->tanggal_bayar)->format('d M Y') : '-' }}</span>
                                                                </div>
                                                                <div class="info-item">
                                                                    <small class="text-muted d-block">Status</small>
                                                                    @if($item->status == 'diverifikasi')
                                                                        <span class="badge bg-success">
                                                                            <i class="bx bx-check-circle me-1"></i>
                                                                            Terverifikasi
                                                                        </span>
                                                                    @elseif($item->status == 'ditolak')
                                                                        <span class="badge bg-danger">
                                                                            <i class="bx bx-x-circle me-1"></i>
                                                                            Ditolak
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-warning text-dark">
                                                                            <i class="bx bx-time me-1"></i>
                                                                            Menunggu
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Informasi Verifikasi -->
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="card border-0 bg-light">
                                                            <div class="card-header bg-transparent border-bottom py-3">
                                                                <h6 class="mb-0 text-primary">
                                                                    <i class="bx bx-check-shield me-2"></i>
                                                                    Informasi Verifikasi
                                                                </h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="info-item mb-2">
                                                                            <small class="text-muted d-block">Diverifikasi Oleh</small>
                                                                            <strong>{{ $item->verifier->name ?? 'System' }}</strong>
                                                                        </div>
                                                                        <div class="info-item">
                                                                            <small class="text-muted d-block">Tanggal Verifikasi</small>
                                                                            <strong>{{ $item->verified_at ? \Carbon\Carbon::parse($item->verified_at)->format('d M Y H:i') : '-' }}</strong>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        @if($item->catatan)
                                                                        <div class="info-item">
                                                                            <small class="text-muted d-block">Catatan Verifikasi</small>
                                                                            <div class="alert alert-light border mt-1">
                                                                                {{ $item->catatan }}
                                                                            </div>
                                                                        </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Bukti Pembayaran -->
                                                @if($item->bukti_pembayaran)
                                                <div class="row mt-4">
                                                    <div class="col-12">
                                                        <div class="card border-0 bg-light">
                                                            <div class="card-header bg-transparent border-bottom py-3">
                                                                <h6 class="mb-0 text-primary">
                                                                    <i class="bx bx-image me-2"></i>
                                                                    Bukti Pembayaran
                                                                </h6>
                                                            </div>
                                                            <div class="card-body text-center">
                                                                <div class="mb-3">
                                                                    <img src="{{ Storage::url($item->bukti_pembayaran) }}" 
                                                                         alt="Bukti Pembayaran" 
                                                                         class="img-fluid rounded border shadow-sm" 
                                                                         style="max-height: 300px;">
                                                                </div>
                                                                <div class="d-flex justify-content-center gap-2">
                                                                    <a href="{{ Storage::url($item->bukti_pembayaran) }}" 
                                                                       target="_blank" 
                                                                       class="btn btn-info btn-sm">
                                                                        <i class="bx bx-zoom-in me-1"></i> Lihat Full
                                                                    </a>
                                                                    <a href="{{ route('pembayaran.download-bukti', $item->id) }}" 
                                                                       class="btn btn-success btn-sm">
                                                                        <i class="bx bx-download me-1"></i> Download
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="bx bx-x me-1"></i> Tutup
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="text-center text-muted">
                                            <i class="bx bx-credit-card display-4 text-primary opacity-50"></i>
                                            <h5 class="mt-3 fw-semibold">Tidak ada data pembayaran</h5>
                                            <p>Belum ada pembayaran yang tercatat</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted small">
                            Menampilkan {{ $data->firstItem() }} - {{ $data->lastItem() }} dari {{ $data->total() }} data
                        </div>
                        <nav>
                            {{ $data->links() }}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    border: 1px solid #e3e6f0;
    border-radius: 0.5rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.table th {
    font-weight: 700;
    font-size: 0.8rem;
    text-transform: uppercase;
    color: #6e707e;
    border-bottom: 2px solid #e3e6f0;
}

.table td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
    border-color: #e3e6f0;
}

.badge {
    font-weight: 600;
    padding: 0.5em 0.75em;
}

.btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem;
    border-radius: 0.35rem;
}

.modal-header {
    padding: 1.25rem;
}

.modal-body {
    padding: 1.5rem;
}

.filter-section {
    background-color: #f8f9fc;
    border: 1px solid #e3e6f0;
}

.info-item {
    padding: 0.5rem 0;
}

.info-item:not(:last-child) {
    border-bottom: 1px solid #f0f0f0;
}

.page-title-box {
    border-bottom: 1px solid #e3e6f0;
    padding-bottom: 1.5rem;
    margin-bottom: 1.5rem;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Success message
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        timer: 3000,
        showConfirmButton: false
    });
    @endif

    // Error message
    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '{{ session('error') }}',
        confirmButtonText: 'OK'
    });
    @endif
});
</script>
@endpush