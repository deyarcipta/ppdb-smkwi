@extends('admin.layouts.app')
@section('title', 'Data Ditolak')

@section('content')
<div class="container-fluid p-0">
    <div class="card card-full-width">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Siswa Ditolak</h5>
            <div class="d-flex align-items-center gap-3">
                <!-- Form Search -->
                <div class="input-group input-group-sm" style="width: 300px;">
                    <span class="input-group-text">
                        <i class="bx bx-search"></i>
                    </span>
                    <input type="text" id="searchTable" class="form-control"
                        placeholder="Cari No Daftar / Nama / Asal Sekolah / No HP...">
                </div>
                <a href="{{ route('data-ditolak.export') }}" class="btn btn-success btn-sm">
                    <i class="bx bx-download me-1"></i>Download Excel
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-sm table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="40">#</th>
                            <th>No Daftar</th>
                            <th>Password</th>
                            <th width="200">Nama Pendaftar</th>
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
                            <td class="text-center">{{ $row->password_plain ?? 'password123' }}</td>
                            <td style="min-width: 200px;">{{ $row->dataSiswa->nama_lengkap ?? '-' }}</td>
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
                            <td colspan="11" class="text-center py-4">
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
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    Menampilkan {{ $data->firstItem() }} hingga {{ $data->lastItem() }} dari {{ $data->total() }} data
                </div>
                {{ $data->links() }}
            </div>
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
                <button type="button" class="btn btn-primary" onclick="printFormulir({{ $row->id }})">
                    <i class="bx bx-printer me-1"></i>Print Formulir
                </button>
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
    const modal = document.getElementById('detailModal' + id);
    const printContent = modal.querySelector('.modal-content').cloneNode(true);
    
    // Hapus tombol-tombol aksi
    const footer = printContent.querySelector('.modal-footer');
    if (footer) footer.remove();
    
    // Buat window print
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Formulir Pendaftaran - ${id}</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
            <style>
                body { font-family: Arial, sans-serif; }
                .card { border: 1px solid #ddd; margin-bottom: 20px; }
                .card-header { background-color: #f8f9fa !important; color: #000 !important; border-bottom: 2px solid #dee2e6; }
                .badge { font-size: 0.7rem; padding: 0.35em 0.65em; }
                .alert-danger { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
                @media print {
                    .card { break-inside: avoid; }
                }
            </style>
        </head>
        <body>
            ${printContent.innerHTML}
            <script>
                window.onload = function() {
                    window.print();
                    setTimeout(function() {
                        window.close();
                    }, 500);
                }
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
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