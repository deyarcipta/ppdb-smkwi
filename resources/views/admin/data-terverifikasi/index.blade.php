@extends('admin.layouts.app')
@section('title', 'Data Terverifikasi')

@section('content')
<div class="container-fluid p-0">
    <div class="card card-full-width">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Terverifikasi</h5>
            <div class="text-muted small">
                Total: {{ $data->total() }} Data Terverifikasi
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="40">#</th>
                            <th>No Daftar</th>
                            <th>Password</th>
                            <th width="200">Nama Pendaftar</th>
                            <th width="200">Asal Sekolah</th>
                            <th>Gelombang</th>
                            <th>No HP</th>
                            <th>Total Bayar</th>
                            <th>Status Bayar</th>
                            <th>Status</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $key => $row)
                        @php
                            // Hitung total pembayaran yang sudah diverifikasi
                            $totalBayar = $row->pembayaran->where('status', 'diverifikasi')->sum('jumlah');
                            $totalBiaya = $totalBiaya ?? 0;
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
                            <td class="text-center">Gelombang 1</td>
                            <td class="text-center">{{ $row->dataSiswa->no_hp ?? '-' }}</td>
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
                                @if($row->dataSiswa?->status_pendaftar == 'diterima')
                                    <span class="badge bg-success text-white">Diterima</span>
                                @elseif($row->dataSiswa?->status_pendaftar == 'ditolak')
                                    <span class="badge bg-danger text-white">Ditolak</span>
                                @else
                                    <span class="badge bg-warning text-white">Pending</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <!-- Tombol Detail -->
                                    <button class="btn btn-primary btn-sm p-1" data-bs-toggle="modal" data-bs-target="#detailModal{{ $row->id }}" title="Detail Data">
                                        <i class="bx bx-show fs-12"></i>
                                    </button>

                                    <!-- Tombol Edit -->
                                    <button class="btn btn-warning btn-sm p-1" data-bs-toggle="modal" data-bs-target="#editStatusModal{{ $row->id }}" title="Edit Status">
                                        <i class="bx bx-edit fs-12"></i>
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('data-terverifikasi.destroy', $row->id) }}" method="POST" class="d-inline form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm p-1" title="Hapus">
                                            <i class="bx bx-trash fs-12"></i>
                                        </button>
                                    </form>

                                    <!-- Tombol Verifikasi Pembayaran -->
                                    <a href="{{ route('verifikasi-pembayaran.index') }}?search={{ $row->no_pendaftaran }}" class="btn btn-info btn-sm p-1" title="Verifikasi Pembayaran">
                                        <i class="bx bx-credit-card fs-12"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bx bx-data display-4"></i>
                                    <p class="mt-2">Belum ada data terverifikasi</p>
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

<!-- Modal Detail Data Siswa -->
@foreach ($data as $row)
@php
    $totalBayar = $row->pembayaran->where('status', 'diverifikasi')->sum('jumlah');
    $totalBiaya = $totalBiaya ?? 0;
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Data Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-3 text-primary">Informasi Akun</h6>
                        <div class="mb-2">
                            <strong>Username:</strong> {{ $row->username }}
                        </div>
                        <div class="mb-2">
                            <strong>Password:</strong> {{ $row->password_plain ?? 'password123' }}
                        </div>
                        <div class="mb-2">
                            <strong>Status Akun:</strong>
                            <span class="badge bg-success text-white">Aktif</span>
                        </div>
                        <div class="mb-2">
                            <strong>Gelombang:</strong> {{ $row->dataSiswa->gelombang->nama_gelombang ?? '-' }}
                        </div>
                        <div class="mb-2">
                            <strong>Jurusan:</strong> {{ $row->dataSiswa->jurusan->nama_jurusan ?? '-' }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3 text-primary">Informasi Pribadi</h6>
                        <div class="mb-2">
                            <strong>Nama Lengkap:</strong> {{ $row->dataSiswa->nama_lengkap ?? '-' }}
                        </div>
                        <div class="mb-2">
                            <strong>Asal Sekolah:</strong> {{ $row->dataSiswa->asal_sekolah ?? '-' }}
                        </div>
                        <div class="mb-2">
                            <strong>No HP:</strong> {{ $row->dataSiswa->no_hp ?? '-' }}
                        </div>
                        <div class="mb-2">
                            <strong>Email:</strong> {{ $row->email ?? '-' }}
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h6 class="mb-3 text-primary">Status Pendaftaran</h6>
                        <div class="mb-2">
                            <strong>Status Pendaftar:</strong>
                            @if($row->dataSiswa?->status_pendaftar == 'diterima')
                                <span class="badge bg-success text-white">Diterima</span>
                            @elseif($row->dataSiswa?->status_pendaftar == 'ditolak')
                                <span class="badge bg-danger text-white">Ditolak</span>
                            @else
                                <span class="badge bg-warning text-white">Pending</span>
                            @endif
                        </div>
                        <div class="mb-2">
                            <strong>Status Pembayaran:</strong>
                            <span class="badge {{ $badgeClass }} text-white">
                                {{ $statusText }}
                            </span>
                        </div>
                        <div class="mb-2">
                            <strong>Total Biaya:</strong> Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                        </div>
                        <div class="mb-2">
                            <strong>Total Dibayar:</strong> Rp {{ number_format($totalBayar, 0, ',', '.') }}
                        </div>
                        <div class="mb-2">
                            <strong>Sisa Pembayaran:</strong> 
                            @if($totalBayar >= $totalBiaya)
                                <span class="text-success">LUNAS</span>
                            @else
                                Rp {{ number_format($totalBiaya - $totalBayar, 0, ',', '.') }}
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3 text-primary">Informasi Tambahan</h6>
                        <div class="mb-2">
                            <strong>Tanggal Daftar:</strong> {{ $row->created_at->format('d M Y') }}
                        </div>
                        <div class="mb-2">
                            <strong>Terakhir Update:</strong> {{ $row->updated_at->format('d M Y H:i') }}
                        </div>
                    </div>
                </div>

                <!-- Riwayat Pembayaran -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="mb-3 text-primary">Riwayat Pembayaran</h6>
                        @if($row->pembayaran->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Jumlah</th>
                                            <th>Status</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($row->pembayaran as $pembayaran)
                                        <tr>
                                            <td>{{ $pembayaran->created_at->format('d M Y') }}</td>
                                            <td>Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                                            <td>
                                                @if($pembayaran->status == 'diverifikasi')
                                                    <span class="badge bg-success">Terverifikasi</span>
                                                @elseif($pembayaran->status == 'pending')
                                                    <span class="badge bg-warning">Menunggu</span>
                                                @else
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @endif
                                            </td>
                                            <td>{{ $pembayaran->ket_pendaftaran ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-light border text-center">
                                <i class="bx bx-info-circle"></i> Belum ada riwayat pembayaran
                            </div>
                        @endif
                    </div>
                </div>

                @if($row->dataSiswa?->ket_pendaftaran)
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="mb-3 text-primary">Catatan</h6>
                        <div class="alert alert-light border">
                            {{ $row->dataSiswa->ket_pendaftaran }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Edit Status -->
@foreach ($data as $row)
<div class="modal fade" id="editStatusModal{{ $row->id }}" tabindex="-1">
    <div class="modal-dialog modal-md">
        <form class="modal-content" method="POST" action="{{ route('data-terverifikasi.update') }}">
            @csrf
            <input type="hidden" name="id" value="{{ $row->id }}">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Status Pendaftaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <label class="form-label fw-semibold">Nama Siswa</label>
                    <div class="form-control bg-light">{{ $row->dataSiswa->nama_lengkap ?? $row->username }}</div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Status Pendaftar <span class="text-danger">*</span></label>
                    <select name="status_pendaftar" class="form-select" id="statusSelect{{ $row->id }}" required>
                        <option value="pending" {{ $row->dataSiswa?->status_pendaftar == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                        <option value="diterima" {{ $row->dataSiswa?->status_pendaftar == 'diterima' ? 'selected' : '' }}>✅ Diterima</option>
                        <option value="ditolak" {{ $row->dataSiswa?->status_pendaftar == 'ditolak' ? 'selected' : '' }}>❌ Ditolak</option>
                    </select>
                </div>

                <!-- Field Alasan Penolakan -->
                <div class="mb-3" id="alasanPenolakan{{ $row->id }}" style="display: none;">
                    <label class="form-label fw-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea name="ket_pendaftaran" class="form-control" rows="4" placeholder="Masukkan alasan penolakan secara jelas dan detail..." maxlength="500" style="resize: none;">{{ $row->dataSiswa?->ket_pendaftaran ?? '' }}</textarea>
                    <div class="form-text">
                        <i class="bx bx-info-circle"></i> Wajib diisi ketika status dipilih "Ditolak". Maksimal 500 karakter.
                    </div>
                </div>

                <!-- Info Status Saat Ini -->
                <div class="alert alert-info border">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-info-circle me-2"></i>
                        <div>
                            <small class="fw-semibold">Status Saat Ini:</small>
                            <br>
                            @if($row->dataSiswa?->status_pendaftar == 'diterima')
                                <span class="badge bg-success">Diterima</span>
                            @elseif($row->dataSiswa?->status_pendaftar == 'ditolak')
                                <span class="badge bg-danger">Ditolak</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i>Batal
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-check me-1"></i>Simpan Perubahan
                </button>
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

/* Modal Edit Status Styles */
.modal-header.bg-primary {
    border-bottom: 2px solid #0d6efd;
}

.form-control.bg-light {
    border: 1px solid #dee2e6;
    font-weight: 500;
}

.alert-info {
    background-color: #f0f9ff;
    border-color: #b6e0fe;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Konfirmasi Hapus
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.form-delete').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: "Data terverifikasi akan dihapus permanen. Anda yakin?",
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

    // Fungsi untuk toggle alasan penolakan
    function toggleAlasanPenolakan(userId) {
        const statusSelect = document.getElementById('statusSelect' + userId);
        const alasanDiv = document.getElementById('alasanPenolakan' + userId);
        const textarea = alasanDiv ? alasanDiv.querySelector('textarea') : null;
        
        if (statusSelect && alasanDiv && textarea) {
            if (statusSelect.value === 'ditolak') {
                alasanDiv.style.display = 'block';
                textarea.required = true;
            } else {
                alasanDiv.style.display = 'none';
                textarea.required = false;
            }
        }
    }

    // Event listener untuk perubahan status
    document.querySelectorAll('select[name="status_pendaftar"]').forEach(select => {
        select.addEventListener('change', function() {
            const userId = this.id.replace('statusSelect', '');
            toggleAlasanPenolakan(userId);
        });
    });

    // Inisialisasi saat modal dibuka
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('show.bs.modal', function() {
            const selectElement = this.querySelector('select[name="status_pendaftar"]');
            if (selectElement) {
                const userId = selectElement.id.replace('statusSelect', '');
                toggleAlasanPenolakan(userId);
            }
        });
    });

    // Validasi form sebelum submit
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const statusSelect = this.querySelector('select[name="status_pendaftar"]');
            const alasanDiv = this.querySelector('div[id^="alasanPenolakan"]');
            const textarea = alasanDiv ? alasanDiv.querySelector('textarea[name="ket_pendaftaran"]') : null;
            
            if (statusSelect && statusSelect.value === 'ditolak') {
                if (!textarea || !textarea.value.trim()) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Alasan Penolakan Wajib Diisi',
                        text: 'Harap masukkan alasan penolakan sebelum menyimpan.',
                        confirmButtonColor: '#d33'
                    });
                    if (textarea) textarea.focus();
                    return false;
                }
                
                if (textarea && textarea.value.length > 500) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Alasan Terlalu Panjang',
                        text: 'Alasan penolakan maksimal 500 karakter.',
                        confirmButtonColor: '#d33'
                    });
                    textarea.focus();
                    return false;
                }
            }
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