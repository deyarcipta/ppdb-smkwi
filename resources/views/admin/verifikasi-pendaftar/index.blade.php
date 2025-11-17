@extends('admin.layouts.app')

@section('title', 'Verifikasi Pendaftar')

@section('content')
<div class="container-fluid p-0">
    <div class="card card-full-width">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Verifikasi Pendaftar</h5>
            <div class="text-muted small">
                Total: {{ $data->total() }} Pendaftar
            </div>
        </div>
        <div class="card-body">
            <!-- Alert Info -->
            @if($data->total() == 0)
            <div class="alert alert-info">
                <i class="bx bx-info-circle"></i> Tidak ada data pendaftar yang perlu diverifikasi.
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th width="15%">Username</th>
                            <th width="20%">Nama Lengkap</th>
                            <th width="20%">Asal Sekolah</th>
                            <th width="15%">No HP</th>
                            <th width="15%">Status</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $key => $item)
                        <tr>
                            <td class="text-center">{{ $data->firstItem() + $key }}</td>
                            <td>{{ $item->username ?? '-' }}</td>
                            <td>{{ $item->dataSiswa->nama_lengkap ?? '-' }}</td>
                            <td>{{ $item->dataSiswa->asal_sekolah ?? '-' }}</td>
                            <td>{{ $item->dataSiswa->no_hp ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge 
                                    @if($item->dataSiswa?->status_pendaftar == 'sudah_verifikasi') 
                                        bg-success text-white
                                    @else 
                                        bg-warning text-white
                                    @endif">
                                    @if($item->dataSiswa?->status_pendaftar == 'sudah_verifikasi')
                                        Sudah Verifikasi
                                    @else
                                        Belum Verifikasi
                                    @endif
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <!-- Tombol Verifikasi -->
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#verifikasiModal{{ $item->id }}" title="Verifikasi">
                                        <i class="bx bx-check-circle"></i>
                                    </button>

                                    <!-- Tombol Detail -->
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}" title="Detail">
                                        <i class="bx bx-show"></i>
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('verifikasi-pendaftar.destroy', $item->id) }}" method="POST" class="d-inline form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bx bx-data display-4"></i>
                                    <p class="mt-2">Tidak ada data pendaftar</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Component -->
            <x-pagination :paginator="$data" />
        </div>
    </div>
</div>

<!-- Modals -->
@foreach($data as $item)
<!-- Modal Verifikasi -->
<div class="modal fade" id="verifikasiModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('verifikasi-pendaftar.update') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Verifikasi Pendaftar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" value="{{ $item->id }}">
                    <div class="mb-3">
                        <label class="form-label">Status Pendaftar</label>
                        <select name="status_pendaftar" class="form-select" required>
                            <option value="belum_verifikasi" {{ ($item->dataSiswa->status_pendaftar ?? '') == 'belum_verifikasi' ? 'selected' : '' }}>Belum Verifikasi</option>
                            <option value="sudah_verifikasi" {{ ($item->dataSiswa->status_pendaftar ?? '') == 'sudah_verifikasi' ? 'selected' : '' }}>Sudah Verifikasi</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pendaftar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-primary">Username</label>
                        <p class="border-bottom pb-2">{{ $item->username ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-primary">Nama Lengkap</label>
                        <p class="border-bottom pb-2">{{ $item->dataSiswa->nama_lengkap ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-primary">Asal Sekolah</label>
                        <p class="border-bottom pb-2">{{ $item->dataSiswa->asal_sekolah ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-primary">No HP</label>
                        <p class="border-bottom pb-2">{{ $item->dataSiswa->no_hp ?? '-' }}</p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold text-primary">Status Verifikasi</label>
                        <p>
                            <span class="badge 
                                @if($item->dataSiswa?->status_pendaftar == 'sudah_verifikasi') 
                                    bg-success text-white
                                @else 
                                    bg-warning text-white
                                @endif">
                                @if($item->dataSiswa?->status_pendaftar == 'sudah_verifikasi')
                                    Sudah Verifikasi
                                @else
                                    Belum Verifikasi
                                @endif
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('styles')
<style>
/* Card Full Width */
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

/* Container fluid tanpa padding */
.container-fluid.p-0 {
    padding: 0 !important;
}

/* Badge dengan text putih */
.badge.text-white {
    color: white !important;
    font-weight: 500;
}

/* Table styling */
.table th {
    font-weight: 600;
    background-color: #f8f9fa;
    border-bottom: 2px solid #e4e6e8;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.badge {
    font-size: 0.7rem;
    padding: 0.35em 0.65em;
}

/* Reduce spacing between table and pagination */
.card-body {
    padding-bottom: 0.5rem;
}

.border-top {
    border-top: 1px solid #dee2e6 !important;
    margin-top: 1rem;
    padding-top: 1rem;
}

/* Custom Pagination Styles */
.pagination-btn {
    border-radius: 6px !important;
    min-width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    font-size: 0.8rem;
    font-weight: 500;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease-in-out;
}

.pagination-btn:hover:not(.disabled) {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.pagination-btn.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.pagination-btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// SweetAlert for delete confirmation
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.form-delete').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: "Data pendaftar akan dihapus permanen. Anda yakin?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Success message
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        timer: 2000,
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