@extends('admin.layouts.app')
@section('title', 'Data Ditolak')

@section('content')
<div class="container-fluid p-0">
    <div class="card card-full-width">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
            <div>
                <h5 class="mb-1"><i class="bx bx-user-x me-2 text-danger"></i>Data Siswa Ditolak</h5>
                <small class="text-muted">Menampilkan daftar pendaftar yang tidak memenuhi syarat / ditolak.</small>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <form action="{{ route('data-ditolak.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
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
                        <input type="text" name="search" class="form-control" placeholder="Cari Nama / NISN / No. Daftar..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i></button>
                    </div>

                    @if(request()->hasAny(['search', 'jurusan_id', 'gelombang_id']))
                        <a href="{{ route('data-ditolak.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter"><i class="bx bx-refresh"></i></a>
                    @endif
                </form>
                
                <a href="{{ route('data-ditolak.export', request()->all()) }}" class="btn btn-success btn-sm text-nowrap">
                    <i class="bx bx-download me-1"></i>Download Excel
                </a>
            </div>
        </div>


        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="40">#</th>
                            <th>No Daftar</th>
                            <th width="260">Nama Pendaftar</th>
                            <th width="200">Asal Sekolah</th>
                            <th>Gelombang</th>
                            <th>Jurusan</th>
                            <th>Total Bayar</th>
                            <th>Status Bayar</th>
                            <th>Alasan</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $key => $row)
                        @php
                            // Hitung total pembayaran yang sudah diverifikasi
                            $totalBayar = $row->pembayaran->where('status', 'diverifikasi')->sum('jumlah');
                            $statusPembayaran = 'belum_bayar';
                            $badgeClass = 'bg-secondary';
                            $statusText = 'Belum Bayar';

                            if ($totalBayar > 0) {
                                if ($totalBayar >= $totalBiaya) {
                                    $statusPembayaran = 'lunas';
                                    $badgeClass = 'bg-success';
                                    $statusText = 'Lunas';
                                } else {
                                    $statusPembayaran = 'belum_lunas';
                                    $badgeClass = 'bg-warning';
                                    $statusText = 'Belum Lunas';
                                }
                            }

                            // Cek jika ada pembayaran pending
                            $pembayaranPending = $row->pembayaran->where('status', 'pending')->count() > 0;
                            if ($pembayaranPending) {
                                $statusPembayaran = 'menunggu_verifikasi';
                                $badgeClass = 'bg-info';
                                $statusText = 'Menunggu Verifikasi';
                            }
                        @endphp
                        <tr>
                            <td class="text-center">{{ $data->firstItem() + $key }}</td>
                            <td class="text-center">{{ $row->username }}</td>
                            <td style="min-width: 260px;">
                                <div class="fw-bold text-dark">{{ $row->dataSiswa->nama_lengkap ?? '-' }}</div>
                                <small class="text-primary fw-semibold d-block">{{ $row->dataSiswa->no_pendaftaran ?? '-' }}</small>
                            </td>
                            <td style="min-width: 200px;">{{ $row->dataSiswa->asal_sekolah ?? '-' }}</td>
                            <td class="text-center">{{ $row->dataSiswa->gelombang->nama_gelombang ?? 'Gelombang 1' }}</td>
                            <td class="text-center">{{ $row->dataSiswa->jurusan->nama_jurusan ?? '-' }}</td>
                            <td class="text-center">
                                @if($totalBayar > 0)
                                    Rp {{ number_format($totalBayar, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $badgeClass }} text-white">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($row->dataSiswa?->ket_pendaftaran)
                                  {{ $row->dataSiswa->ket_pendaftaran }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <!-- Tombol Detail/Lihat Formulir -->
                                    <button class="btn btn-primary btn-sm p-1" data-bs-toggle="modal" data-bs-target="#detailModal{{ $row->id }}" title="Lihat Formulir Lengkap">
                                        <i class="bx bx-show fs-12"></i>
                                    </button>

                                    <!-- Tombol Edit Status -->
                                    {{-- <button class="btn btn-warning btn-sm p-1" data-bs-toggle="modal" data-bs-target="#editStatusModal{{ $row->id }}" title="Edit Status">
                                        <i class="bx bx-edit fs-12"></i>
                                    </button> --}}

                                    <!-- Tombol Hapus -->
                                    {{-- <form action="{{ route('data-ditolak.destroy', $row->id) }}" method="POST" class="d-inline form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm p-1" title="Hapus">
                                            <i class="bx bx-trash fs-12"></i>
                                        </button>
                                    </form> --}}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bx bx-data display-4"></i>
                                    <p class="mt-2">Belum ada data siswa yang ditolak</p>
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

<!-- Modal Detail Formulir Lengkap Siswa -->
@foreach ($data as $row)
@php
    $totalBayar = $row->pembayaran->where('status', 'diverifikasi')->sum('jumlah');
    $statusPembayaran = 'belum_bayar';
    $badgeClass = 'bg-secondary';
    $statusText = 'Belum Bayar';

    if ($totalBayar > 0) {
        if ($totalBayar >= $totalBiaya) {
            $statusPembayaran = 'lunas';
            $badgeClass = 'bg-success';
            $statusText = 'Lunas';
        } else {
            $statusPembayaran = 'belum_lunas';
            $badgeClass = 'bg-warning';
            $statusText = 'Belum Lunas';
        }
    }

    $pembayaranPending = $row->pembayaran->where('status', 'pending')->count() > 0;
    if ($pembayaranPending) {
        $statusPembayaran = 'menunggu_verifikasi';
        $badgeClass = 'bg-info';
        $statusText = 'Menunggu Verifikasi';
    }
@endphp
<div class="modal fade" id="detailModal{{ $row->id }}" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Formulir Pendaftaran - {{ $row->dataSiswa->nama_lengkap ?? $row->username }} <span class="badge bg-white text-danger ms-2">DITOLAK</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Konten modal sama seperti data diterima, tapi dengan badge DITOLAK -->
                <!-- Anda bisa copy-paste modal content dari data-diterima -->
                
                @if($row->dataSiswa?->catatan)
                <div class="alert alert-danger">
                    <h6 class="alert-heading">Alasan Penolakan:</h6>
                    {{ $row->dataSiswa->catatan }}
                </div>
                @endif
                
                <!-- Sisanya sama seperti modal data-diterima -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i>Tutup
                </button>
                <a href="{{ route('admin.cetak-formulir', $row->id) }}" target="_blank" class="btn btn-primary">
                    <i class="bx bx-printer me-1"></i>Print Formulir
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Status -->
<div class="modal fade" id="editStatusModal{{ $row->id }}" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form class="modal-content" method="POST" action="{{ route('data-ditolak.update') }}">
            @csrf
            <input type="hidden" name="id" value="{{ $row->id }}">
            <div class="modal-header">
                <h5 class="modal-title">Edit Status - {{ $row->dataSiswa->nama_lengkap ?? $row->username }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Status Pendaftar</label>
                    <select name="status_pendaftar" class="form-select form-select-sm" required>
                        <option value="pending" {{ $row->dataSiswa?->status_pendaftar == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="diterima" {{ $row->dataSiswa?->status_pendaftar == 'diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="ditolak" {{ $row->dataSiswa?->status_pendaftar == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alasan (jika ditolak)</label>
                    <textarea name="catatan" class="form-control form-control-sm" rows="3" placeholder="Masukkan alasan penolakan...">{{ $row->dataSiswa?->catatan }}</textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success btn-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endforeach
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
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Konfirmasi Hapus
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    document.querySelectorAll('.form-delete').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: "Data siswa yang ditolak akan dihapus permanen. Anda yakin?",
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

// Fungsi Print Formulir
function printFormulir(id) {
    window.open('/panel/data-pendaftar/' + id + '/cetak-formulir', '_blank');
}

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchTable');
    const table = document.getElementById('dataTable');
    const rows = table.querySelectorAll('tbody tr');

    searchInput.addEventListener('keyup', function () {
        const keyword = this.value.toLowerCase();

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(keyword) ? '' : 'none';
        });
    });
});
</script>
@endpush