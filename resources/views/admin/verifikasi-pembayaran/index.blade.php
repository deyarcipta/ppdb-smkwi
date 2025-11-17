@extends('admin.layouts.app')
@section('title', 'Verifikasi Pembayaran')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Section -->
    <div class="row mb-4 mx-0">
        <div class="col-12 px-3">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between py-3">
                <h4 class="mb-sm-0">Verifikasi Pembayaran</h4>
                <div class="d-flex gap-4">
                    <div class="text-muted">
                        <small>Total Menunggu: <span class="badge bg-warning text-dark">{{ $totalMenunggu }}</span></small>
                    </div>
                    <div class="text-muted">
                        <small>Total Nominal: <span class="badge bg-info text-white">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card - Full Width dengan Radius -->
    <div class="row mx-0">
        <div class="col-12 px-3">
            <div class="card">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-list-check me-2"></i>
                        Daftar Pembayaran Menunggu Verifikasi
                    </h5>
                </div>

                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="filter-section p-3 bg-light rounded">
                                <form method="GET" class="row g-3 align-items-end">
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
                                                   placeholder="Cari no pendaftaran atau nama siswa..." 
                                                   value="{{ request('search') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                            <i class="bx bx-filter me-1"></i> Filter
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="{{ route('verifikasi-pembayaran.index') }}" class="btn btn-outline-secondary btn-sm w-100">
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
                                    <th width="200" class="text-center">Aksi</th>
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
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Detail Button -->
                                            <button class="btn btn-info btn-sm px-3" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#detailModal{{ $item->id }}"
                                                    title="Lihat Detail">
                                                <i class="bx bx-show-alt me-1"></i> Detail
                                            </button>

                                            <!-- Verify Button -->
                                            <button class="btn btn-success btn-sm px-3" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#verifyModal{{ $item->id }}"
                                                    title="Verifikasi Pembayaran">
                                                <i class="bx bx-check-shield me-1"></i> Verify
                                            </button>

                                            <!-- Delete Button -->
                                            <button class="btn btn-danger btn-sm px-3 btn-delete" 
                                                    data-id="{{ $item->id }}"
                                                    data-name="{{ $item->nama_siswa }}"
                                                    title="Hapus Data">
                                                <i class="bx bx-trash me-1"></i> Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-center text-muted">
                                            <i class="bx bx-check-circle display-4 text-success opacity-50"></i>
                                            <h5 class="mt-3 fw-semibold">Tidak ada pembayaran yang menunggu verifikasi</h5>
                                            <p>Semua pembayaran sudah diverifikasi</p>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bx bx-trash me-2"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="bx bx-error-circle display-4 text-danger"></i>
                </div>
                <p class="text-center">Apakah Anda yakin ingin menghapus data pembayaran untuk:</p>
                <h6 class="text-center fw-bold" id="deleteStudentName"></h6>
                <div class="alert alert-warning mt-3">
                    <small>
                        <i class="bx bx-info-circle"></i>
                        <strong>Perhatian:</strong> Data yang sudah dihapus tidak dapat dikembalikan!
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i> Batal
                </button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bx bx-trash me-1"></i> Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modals di luar main content untuk menghindari masalah rendering -->
@foreach ($data as $item)
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
                                    <span class="badge bg-warning text-dark">
                                        <i class="bx bx-time me-1"></i>
                                        Menunggu Verifikasi
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bukti Pembayaran -->
                @if($item->bukti_pembayaran)
                <div class="row">
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
                                    <a href="{{ route('verifikasi-pembayaran.download-bukti', $item->id) }}" 
                                       class="btn btn-success btn-sm">
                                        <i class="bx bx-download me-1"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Informasi Tambahan -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-header bg-transparent border-bottom py-3">
                                <h6 class="mb-0 text-primary">
                                    <i class="bx bx-info-circle me-2"></i>
                                    Informasi Tambahan
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Tanggal Upload</small>
                                        <strong>{{ $item->created_at->format('d M Y H:i') }}</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Terakhir Update</small>
                                        <strong>{{ $item->updated_at->format('d M Y H:i') }}</strong>
                                    </div>
                                </div>
                                @if($item->catatan)
                                <div class="mt-3">
                                    <small class="text-muted d-block">Catatan</small>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i> Tutup
                </button>
                <button type="button" class="btn btn-primary" 
                        data-bs-toggle="modal" 
                        data-bs-target="#verifyModal{{ $item->id }}">
                    <i class="bx bx-check-shield me-1"></i> Verifikasi Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Verify Modal -->
<div class="modal fade" id="verifyModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bx bx-check-shield me-2"></i>
                    Verifikasi Pembayaran
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('verifikasi-pembayaran.verify', $item->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Verifikasi <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="">Pilih Status</option>
                            <option value="diverifikasi">✓ Setujui & Verifikasi</option>
                            <option value="ditolak">✗ Tolak Pembayaran</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="4" 
                                  placeholder="Berikan catatan verifikasi...&#10;Contoh: Bukti pembayaran sudah sesuai, nominal tepat, dll."></textarea>
                        <div class="form-text">
                            Catatan akan ditampilkan kepada siswa
                        </div>
                    </div>
                    <div class="alert alert-info border">
                        <small>
                            <i class="bx bx-info-circle"></i>
                            <strong>Informasi:</strong><br>
                            - <strong>Setujui</strong>: Pembayaran akan divalidasi dan status berubah menjadi "Terverifikasi"<br>
                            - <strong>Tolak</strong>: Pembayaran akan ditolak dan siswa perlu mengupload ulang bukti
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> Simpan Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('styles')
<style>
/* Reset container padding */
.container-fluid.p-0 {
    padding-left: 0 !important;
    padding-right: 0 !important;
}

/* Full width card dengan radius */
.card {
    border-radius: 0.5rem !important;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    margin-left: 0.75rem;
    margin-right: 0.75rem;
}

/* Remove side margins from rows */
.row.mx-0 {
    margin-left: 0 !important;
    margin-right: 0 !important;
}

/* Header styling */
.page-title-box {
    border-bottom: 1px solid #e3e6f0;
    padding: 1rem 1.5rem !important;
    margin: 0 !important;
    background: #fff;
}

/* Card header styling */
.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

/* Table styles */
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

/* Badge styles */
.badge {
    font-weight: 600;
    padding: 0.5em 0.75em;
}

/* Button styles */
.btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem;
    border-radius: 0.35rem;
}

/* Filter section */
.filter-section {
    background-color: #f8f9fc;
    border: 1px solid #e3e6f0;
    border-radius: 0.5rem;
}

/* Info item styles */
.info-item {
    padding: 0.5rem 0;
}

.info-item:not(:last-child) {
    border-bottom: 1px solid #f0f0f0;
}

/* Modal styles */
.modal {
    background-color: rgba(0, 0, 0, 0.5) !important;
}

.modal-backdrop {
    background-color: #000 !important;
}

.modal-backdrop.show {
    opacity: 0.5 !important;
}

.modal-content {
    background-color: #ffffff !important;
    border: none !important;
    border-radius: 0.5rem !important;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.modal-header {
    border-bottom: 1px solid #dee2e6 !important;
    border-radius: 0.5rem 0.5rem 0 0 !important;
}

.modal-footer {
    border-top: 1px solid #dee2e6 !important;
    border-radius: 0 0 0.5rem 0.5rem !important;
}

/* Ensure modal content is opaque */
.modal-content {
    opacity: 1 !important;
    background: white !important;
}

/* Fix untuk modal header */
.modal-header.bg-primary {
    background: linear-gradient(135deg, #007bff, #0056b3) !important;
}

.modal-header.bg-success {
    background: linear-gradient(135deg, #28a745, #1e7e34) !important;
}

.modal-header.bg-danger {
    background: linear-gradient(135deg, #dc3545, #c82333) !important;
}

/* Z-index fixes */
.modal-backdrop {
    z-index: 1040 !important;
}

.modal {
    z-index: 1050 !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card {
        margin-left: 0.5rem;
        margin-right: 0.5rem;
    }
    
    .page-title-box {
        padding: 1rem 1rem !important;
    }
    
    .btn-sm {
        padding: 0.3rem 0.6rem;
        font-size: 0.75rem;
    }
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

    // Auto focus pada modal verifikasi
    document.addEventListener('shown.bs.modal', function (event) {
        const modal = event.target;
        if (modal.id.includes('verifyModal')) {
            const select = modal.querySelector('select[name="status"]');
            if (select) select.focus();
        }
    });

    // Delete button handler
    const deleteButtons = document.querySelectorAll('.btn-delete');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const deleteForm = document.getElementById('deleteForm');
    const deleteStudentName = document.getElementById('deleteStudentName');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            
            deleteStudentName.textContent = name;
            deleteForm.action = `{{ route('verifikasi-pembayaran.destroy', '') }}/${id}`;
            
            deleteModal.show();
        });
    });

    // Force modal backdrop
    const modalElements = document.querySelectorAll('.modal');
    modalElements.forEach(modal => {
        modal.addEventListener('show.bs.modal', function () {
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);
        });

        modal.addEventListener('hidden.bs.modal', function () {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
        });
    });
});
</script>
@endpush