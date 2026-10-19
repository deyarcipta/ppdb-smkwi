@extends('admin.layouts.app')

@section('title', 'Verifikasi Pendaftar')

@section('content')
<div class="container-fluid p-0">
    <div class="card card-full-width">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
            <div>
                <h5 class="mb-1"><i class="bx bx-user-check me-2 text-primary"></i>Verifikasi Pendaftar</h5>
                <small class="text-muted">Aktivasi dan verifikasi akun pendaftar baru yang menunggu persetujuan.</small>
            </div>

            <div class="d-flex align-items-center flex-wrap gap-2">
                <form action="{{ route('verifikasi-pendaftar.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <select name="jurusan_id" class="form-select form-select-sm" style="width: auto; min-width: 150px;" onchange="this.form.submit()">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusanList as $j)
                            <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
                        @endforeach
                    </select>

                    <select name="gelombang_id" class="form-select form-select-sm" style="width: auto; min-width: 160px;" onchange="this.form.submit()">
                        <option value="">Semua Gelombang</option>
                        @foreach($gelombangList as $g)
                            <option value="{{ $g->id }}" {{ request('gelombang_id') == $g->id ? 'selected' : '' }}>{{ $g->nama_gelombang }}</option>
                        @endforeach
                    </select>

                    <div class="input-group input-group-sm" style="width: 280px; min-width: 250px;">
                        <input type="text" name="search" class="form-control" placeholder="Cari Username / Nama / Asal..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i></button>
                    </div>

                    @if(request()->hasAny(['search', 'jurusan_id', 'gelombang_id']))
                        <a href="{{ route('verifikasi-pendaftar.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter"><i class="bx bx-refresh"></i></a>
                    @endif
                </form>

                <div class="d-flex align-items-center gap-2 ms-sm-1">
                    <span class="badge bg-label-warning px-3 py-2">
                        <i class="bx bx-time me-1"></i>Menunggu: <strong>{{ $data->total() }}</strong>
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Alert Info -->
            @if($data->total() == 0)
            <div class="alert alert-info">
                <i class="bx bx-info-circle"></i> Tidak ada data pendaftar yang perlu diverifikasi.
            </div>
            @endif

            <!-- BULK ACTION BAR -->
            <div id="bulkActionBar" class="alert alert-primary d-none align-items-center justify-content-between p-2 mb-3 shadow-sm rounded border border-primary">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary p-2 fs-6">
                        <i class="bx bx-check-shield"></i>
                    </span>
                    <div>
                        <strong class="text-primary"><span id="selectedCount">0</span> Pendaftar Terpilih</strong>
                        <div class="small text-muted">Aksi massal verifikasi pendaftar baru</div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCancelBulk">
                        <i class="bx bx-x me-1"></i>Batal
                    </button>
                    <button type="button" class="btn btn-sm btn-success fw-bold shadow-sm" id="btnBulkDiterima" title="Langsung aktifkan dan ubah status menjadi Diterima">
                        <i class="bx bx-check-double me-1"></i>Verifikasi & Jadikan Diterima
                    </button>
                    <button type="button" class="btn btn-sm btn-primary fw-bold shadow-sm" id="btnBulkVerifikasi" title="Aktifkan akun pendaftar">
                        <i class="bx bx-check me-1"></i>Verifikasi Akun
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th width="4%" class="text-center">
                                <input type="checkbox" class="form-check-input" id="checkAll" title="Pilih Semua">
                            </th>
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
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input check-item" value="{{ $item->id }}" data-nama="{{ $item->dataSiswa->nama_lengkap ?? $item->username }}">
                            </td>
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

                                    <!-- Tombol Hapus (Hanya Superadmin) -->
                                    @if(auth()->user()->role === 'superadmin')
                                    <form action="{{ route('verifikasi-pendaftar.destroy', $item->id) }}" method="POST" class="d-inline form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
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
                        <p class="border-bottom pb-2">
                            {{ $item->dataSiswa->asal_sekolah ?? '-' }}
                            @php
                                $subdistrict = $item->dataSiswa->kecamatan_desa_sekolah_asal ?? $item->dataSiswa->kota_sekolah_asal;
                            @endphp
                            @if(!empty($item->dataSiswa->jenjang_sekolah_asal) || !empty($item->dataSiswa->status_sekolah_asal) || !empty($subdistrict))
                                <br><small class="text-muted">{{ implode(' • ', array_filter([$item->dataSiswa->jenjang_sekolah_asal, $item->dataSiswa->status_sekolah_asal, $subdistrict])) }}</small>
                            @endif
                        </p>
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

    // ==========================================
    // LOGIKA BULK ACTION VERIFIKASI PENDAFTAR
    // ==========================================
    const checkAll = document.getElementById('checkAll');
    const checkItems = document.querySelectorAll('.check-item');
    const bulkActionBar = document.getElementById('bulkActionBar');
    const selectedCount = document.getElementById('selectedCount');
    const btnCancelBulk = document.getElementById('btnCancelBulk');
    const btnBulkDiterima = document.getElementById('btnBulkDiterima');
    const btnBulkVerifikasi = document.getElementById('btnBulkVerifikasi');

    function updateBulkBar() {
        const checkedBoxes = document.querySelectorAll('.check-item:checked');
        const count = checkedBoxes.length;

        if (count > 0) {
            selectedCount.textContent = count;
            bulkActionBar.classList.remove('d-none');
            bulkActionBar.classList.add('d-flex');
        } else {
            bulkActionBar.classList.remove('d-flex');
            bulkActionBar.classList.add('d-none');
        }

        if (checkItems.length > 0) {
            checkAll.checked = (checkedBoxes.length === checkItems.length);
            checkAll.indeterminate = (checkedBoxes.length > 0 && checkedBoxes.length < checkItems.length);
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            const isChecked = this.checked;
            checkItems.forEach(cb => { cb.checked = isChecked; });
            updateBulkBar();
        });
    }

    checkItems.forEach(cb => {
        cb.addEventListener('change', updateBulkBar);
    });

    if (btnCancelBulk) {
        btnCancelBulk.addEventListener('click', function () {
            checkItems.forEach(cb => { cb.checked = false; });
            if (checkAll) {
                checkAll.checked = false;
                checkAll.indeterminate = false;
            }
            updateBulkBar();
        });
    }

    function executeBulkAction(status, title, confirmText) {
        const checkedBoxes = document.querySelectorAll('.check-item:checked');
        const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);

        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih minimal 1 pendaftar terlebih dahulu.',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        Swal.fire({
            title: title,
            html: `Apakah Anda yakin ingin memproses <b>${selectedIds.length} pendaftar</b> terpilih?<br><small class="text-muted">Notifikasi pesan WhatsApp otomatis diproses melalui antrean.</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: (status === 'diterima') ? '#28a745' : '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `<i class="bx bx-check me-1"></i>${confirmText}`,
            cancelButtonText: 'Batal',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch('{{ route("verifikasi-pendaftar.bulk-update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        user_ids: selectedIds,
                        status_pendaftar: status
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message || 'Gagal memproses data'); });
                    }
                    return response.json();
                })
                .catch(error => {
                    Swal.showValidationMessage(`Gagal: ${error.message}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed && result.value && result.value.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.value.message,
                    timer: 1600,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            }
        });
    }

    if (btnBulkDiterima) {
        btnBulkDiterima.addEventListener('click', function () {
            executeBulkAction('diterima', 'Verifikasi & Jadikan Diterima', 'Ya, Terima Semua');
        });
    }

    if (btnBulkVerifikasi) {
        btnBulkVerifikasi.addEventListener('click', function () {
            executeBulkAction('sudah_verifikasi', 'Verifikasi Akun Pendaftar', 'Ya, Verifikasi Akun');
        });
    }
});
</script>
@endpush