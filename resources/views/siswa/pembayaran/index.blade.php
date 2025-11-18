@extends('siswa.layouts.app')
@section('title', 'Pembayaran PPDB')

@section('content')
<div class="container-fluid">
    <!-- Header Card -->
    <div class="card gradient-card shadow-lg border-0 mb-4">
        <div class="card-body text-center text-white py-4">
            <h2 class="mb-2 text-white"><i class="fas fa-credit-card me-2"></i>PEMBAYARAN PPDB</h2>
            <p class="mb-0 opacity-75">Kelola pembayaran dan lihat status verifikasi</p>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Informasi Biaya -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-white"><i class="bx bx-receipt me-2"></i>Rincian Biaya</h5>
                </div>
                <div class="card-body mt-3">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Biaya</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($MasterBiaya as $biaya)
                                <tr>
                                    <td>{{ strtoupper(str_replace('_', ' ', $biaya->jenis_biaya)) }}</td>
                                    <td class="text-end">Rp {{ number_format($biaya->total_biaya, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-primary">
                                <tr>
                                    <th>TOTAL BIAYA</th>
                                    <th class="text-end">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Total Pembayaran -->
                    <div class="card border-0 bg-light mt-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold">Total Pembayaran:</span>
                                <span class="fw-bold text-success">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold">Sisa Bayar:</span>
                                <span class="fw-bold text-danger">Rp {{ number_format($sisaBayar, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Status:</span>
                                @if($status == 'LUNAS')
                                    <span class="badge bg-success fs-6">LUNAS</span>
                                @elseif($status == 'BELUM LUNAS')
                                    <span class="badge bg-warning text-dark fs-6">BELUM LUNAS</span>
                                @else
                                    <span class="badge bg-danger fs-6">BELUM BAYAR</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Rekening Sekolah -->
                    <div class="card border-0 mt-3" style="background-color: #f8f9fa;">
                        <div class="card-body">
                            <h6 class="card-title">Informasi Pembayaran Transfer</h6>
                            <div class="mb-2">
                                <small class="text-muted">Nama Bank</small>
                                <div class="fw-bold">{{ $infoPembayaran->nama_bank ? '-' }}</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Nomor Rekening</small>
                                <div class="fw-bold">{{ $infoPembayaran->nomor_rekening ?? '-' }}</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Atas Nama</small>
                                <div class="fw-bold">{{ $infoPembayaran->atas_nama ?? '-' }}</div>
                            </div>
                            <div>
                                <small class="text-muted">Keterangan</small>
                                <div class="fw-bold">{{ $infoPembayaran->keterangan ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="d-grid gap-2 mt-4">
                        <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#tambahPembayaranModal">
                            <i class="fas fa-plus me-2"></i>Tambah Pembayaran
                        </button>
                        <button class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-download me-2"></i>Download Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content - Data Pembayaran -->
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Pembayaran</h5>
                    <span class="badge bg-primary">{{ $pembayaran->count() }} Transaksi</span>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($pembayaran->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada riwayat pembayaran</h5>
                            <p class="text-muted">Silakan tambah pembayaran pertama Anda</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahPembayaranModal">
                                <i class="fas fa-plus me-2"></i>Tambah Pembayaran
                            </button>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Jenis Bayar</th>
                                        <th>Jumlah Bayar</th>
                                        <th>Tgl Bayar</th>
                                        <th>Status</th>
                                        <th>Bukti</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pembayaran as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $loop->iteration }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                {{ strtoupper(str_replace('_', ' ', $item->jenis_pembayaran)) }}
                                            </span>
                                        </td>
                                        <td class="fw-bold text-success">
                                            Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            {{ $item->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $item->status_badge_class }}">
                                                {{ $item->status_label }}
                                            </span>
                                            @if($item->verified_at)
                                                <br>
                                                <small class="text-muted">
                                                    Diverifikasi: {{ $item->verified_at->format('d-m-Y H:i') }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->bukti_pembayaran)
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#buktiModal{{ $item->id }}">
                                                    <i class="fas fa-eye me-1"></i>Lihat
                                                </button>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                              <button type="button" class="btn btn-sm btn-outline-info" 
                                                  data-bs-toggle="modal" 
                                                  data-bs-target="#detailModal{{ $item->id }}">
                                                  <i class="bx bx-show"></i>
                                              </button>
                                              
                                              @if($item->status == 'pending')
                                              <button type="button" class="btn btn-sm btn-outline-warning" 
                                                  data-bs-toggle="modal" 
                                                  data-bs-target="#editPembayaranModal{{ $item->id }}">
                                                  <i class="bx bx-edit"></i>
                                              </button>
                                              
                                              <button type="button" class="btn btn-sm btn-outline-danger" 
                                                  data-bs-toggle="modal" 
                                                  data-bs-target="#hapusModal{{ $item->id }}">
                                                  <i class="bx bx-trash"></i>
                                              </button>
                                              @endif
                                          </div>
                                        </td>
                                    </tr>

                                    <!-- Modal Bukti Bayar -->
                                    <div class="modal fade" id="buktiModal{{ $item->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Bukti Pembayaran - #{{ $item->id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img src="{{ asset('storage/' . $item->bukti_pembayaran) }}" 
                                                         alt="Bukti Bayar" 
                                                         class="img-fluid rounded" 
                                                         style="max-height: 500px;">
                                                    <div class="mt-3">
                                                        <a href="{{ asset('storage/' . $item->bukti_pembayaran) }}" 
                                                           download 
                                                           class="btn btn-primary btn-sm">
                                                            <i class="fas fa-download me-1"></i>Download
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Detail -->
                                    <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Detail Pembayaran</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row mb-2">
                                                        <div class="col-4 fw-bold">ID Transaksi:</div>
                                                        <div class="col-8">#{{ $item->id }}</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-4 fw-bold">Jenis Pembayaran:</div>
                                                        <div class="col-8">
                                                            {{ strtoupper(str_replace('_', ' ', $item->jenis_pembayaran)) }}
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-4 fw-bold">Jumlah Bayar:</div>
                                                        <div class="col-8 text-success fw-bold">
                                                            Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-4 fw-bold">Metode Bayar:</div>
                                                        <div class="col-8">{{ $item->metode_pembayaran }}</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-4 fw-bold">Tanggal Bayar:</div>
                                                        <div class="col-8">
                                                            {{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d F Y') }}
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-4 fw-bold">Status:</div>
                                                        <div class="col-8">
                                                            <span class="badge {{ $item->status_badge_class }}">
                                                                {{ $item->status_label }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    @if($item->catatan)
                                                    <div class="row mb-2">
                                                        <div class="col-4 fw-bold">Keterangan:</div>
                                                        <div class="col-8">{{ $item->catatan }}</div>
                                                    </div>
                                                    @endif
                                                    @if($item->verified_at)
                                                    <div class="row mb-2">
                                                        <div class="col-4 fw-bold">Diverifikasi:</div>
                                                        <div class="col-8">
                                                            {{ \Carbon\Carbon::parse($item->verified_at)->format('d F Y H:i') }}
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @if($item->catatan_verifikasi)
                                                    <div class="row">
                                                        <div class="col-4 fw-bold">Catatan:</div>
                                                        <div class="col-8">{{ $item->catatan_verifikasi }}</div>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Edit Pembayaran -->
                                    <div class="modal fade" id="editPembayaranModal{{ $item->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Pembayaran #{{ $item->id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('siswa.pembayaran.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-3">
                                                                    <label for="edit_jenis_pembayaran{{ $item->id }}" class="form-label fw-semibold">
                                                                        Jenis Pembayaran <span class="text-danger">*</span>
                                                                    </label>
                                                                    <select class="form-select" id="edit_jenis_pembayaran{{ $item->id }}" name="jenis_pembayaran" required>
                                                                        <option value="">Pilih Jenis Pembayaran</option>
                                                                        @foreach($MasterBiaya as $biaya)
                                                                            <option value="{{ $biaya->jenis_biaya }}" 
                                                                                {{ $item->jenis_pembayaran == $biaya->jenis_biaya ? 'selected' : '' }}>
                                                                                {{ strtoupper(str_replace('_', ' ', $biaya->jenis_biaya)) }} - Rp {{ number_format($biaya->total_biaya, 0, ',', '.') }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-3">
                                                                    <label for="edit_jumlah{{ $item->id }}" class="form-label fw-semibold">
                                                                        Jumlah Bayar <span class="text-danger">*</span>
                                                                    </label>
                                                                    <input type="number" class="form-control" 
                                                                           id="edit_jumlah{{ $item->id }}" name="jumlah" 
                                                                           value="{{ $item->jumlah }}" 
                                                                           placeholder="Masukkan jumlah bayar" required>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-3">
                                                                    <label for="edit_metode_pembayaran{{ $item->id }}" class="form-label fw-semibold">
                                                                        Metode Pembayaran <span class="text-danger">*</span>
                                                                    </label>
                                                                    <select class="form-select" id="edit_metode_pembayaran{{ $item->id }}" name="metode_pembayaran" required>
                                                                        <option value="">Pilih Metode</option>
                                                                        <option value="transfer" {{ $item->metode_pembayaran == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                                                        <option value="tunai" {{ $item->metode_pembayaran == 'tunai' ? 'selected' : '' }}>Tunai</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-3">
                                                                    <label for="edit_tanggal_bayar{{ $item->id }}" class="form-label fw-semibold">
                                                                        Tanggal Bayar <span class="text-danger">*</span>
                                                                    </label>
                                                                    <input type="date" class="form-control" 
                                                                           id="edit_tanggal_bayar{{ $item->id }}" name="tanggal_bayar" 
                                                                           value="{{ $item->tanggal_bayar->format('Y-m-d') }}" required>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="edit_bukti_pembayaran{{ $item->id }}" class="form-label fw-semibold">
                                                                Bukti Pembayaran
                                                            </label>
                                                            <input type="file" class="form-control" 
                                                                   id="edit_bukti_pembayaran{{ $item->id }}" name="bukti_pembayaran" 
                                                                   accept="image/*">
                                                            <small class="form-text text-muted">
                                                                Biarkan kosong jika tidak ingin mengubah bukti pembayaran
                                                            </small>
                                                            @if($item->bukti_pembayaran)
                                                            <div class="mt-2">
                                                                <small>Bukti Saat Ini:</small>
                                                                <br>
                                                                <img src="{{ asset('storage/' . $item->bukti_pembayaran) }}" 
                                                                     alt="Bukti Saat Ini" 
                                                                     class="img-thumbnail mt-1" 
                                                                     style="max-height: 100px;">
                                                            </div>
                                                            @endif
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="edit_catatan{{ $item->id }}" class="form-label fw-semibold">
                                                                Keterangan
                                                            </label>
                                                            <textarea class="form-control" 
                                                                      id="edit_catatan{{ $item->id }}" name="catatan" 
                                                                      rows="3" placeholder="Tambahkan keterangan jika diperlukan">{{ $item->catatan }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Hapus -->
                                    <div class="modal fade" id="hapusModal{{ $item->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Hapus Pembayaran</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Apakah Anda yakin ingin menghapus pembayaran ini?</p>
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        Tindakan ini tidak dapat dibatalkan!
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Detail Pembayaran:</strong>
                                                        <div>Jenis: {{ strtoupper(str_replace('_', ' ', $item->jenis_pembayaran)) }}</div>
                                                        <div>Jumlah: Rp {{ number_format($item->jumlah, 0, ',', '.') }}</div>
                                                        <div>Tanggal: {{ $item->tanggal_bayar_formatted }}</div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <form action="{{ route('siswa.pembayaran.destroy', $item->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Pembayaran -->
<div class="modal fade" id="tambahPembayaranModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pembayaran Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('siswa.pembayaran.store') }}" method="POST" enctype="multipart/form-data" id="tambahPembayaranForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="jenis_pembayaran" class="form-label fw-semibold">
                                    Jenis Pembayaran <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('jenis_pembayaran') is-invalid @enderror" 
                                        id="jenis_pembayaran" name="jenis_pembayaran" required>
                                    <option value="">Pilih Jenis Pembayaran</option>
                                    @foreach($MasterBiaya as $biaya)
                                        <option value="{{ $biaya->jenis_biaya }}" 
                                            {{ old('jenis_pembayaran') == $biaya->jenis_biaya ? 'selected' : '' }}>
                                            {{ strtoupper(str_replace('_', ' ', $biaya->jenis_biaya)) }} - Rp {{ number_format($biaya->total_biaya, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jenis_pembayaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="jumlah" class="form-label fw-semibold">
                                    Jumlah Bayar <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control @error('jumlah') is-invalid @enderror" 
                                       id="jumlah" name="jumlah" 
                                       value="{{ old('jumlah') }}" 
                                       placeholder="Masukkan jumlah bayar" required>
                                @error('jumlah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="metode_pembayaran" class="form-label fw-semibold">
                                    Metode Pembayaran <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('metode_pembayaran') is-invalid @enderror" 
                                        id="metode_pembayaran" name="metode_pembayaran" required>
                                    <option value="">Pilih Metode Pembayaran</option>
                                    <option value="transfer" {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                    <option value="tunai" {{ old('metode_pembayaran') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                                </select>
                                @error('metode_pembayaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="tanggal_bayar" class="form-label fw-semibold">
                                    Tanggal Bayar <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('tanggal_bayar') is-invalid @enderror" 
                                       id="tanggal_bayar" name="tanggal_bayar" 
                                       value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required>
                                @error('tanggal_bayar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="bukti_pembayaran" class="form-label fw-semibold">
                            Bukti Pembayaran <span class="text-danger">*</span>
                        </label>
                        <input type="file" class="form-control @error('bukti_pembayaran') is-invalid @enderror" 
                               id="bukti_pembayaran" name="bukti_pembayaran" 
                               accept="image/*" required>
                        @error('bukti_pembayaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Format: JPG, PNG, GIF (Maksimal 2MB). Pastikan bukti transfer terbaca dengan jelas.
                        </small>
                    </div>

                    <!-- Preview Image -->
                    <div class="form-group mb-3">
                        <label class="form-label fw-semibold">Preview Bukti Bayar</label>
                        <div id="imagePreview" class="text-center border rounded p-3" style="display: none;">
                            <img id="preview" class="img-fluid rounded" style="max-height: 200px;">
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearImage()">
                                    <i class="fas fa-times me-1"></i>Hapus Gambar
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="catatan" class="form-label fw-semibold">
                            Keterangan
                        </label>
                        <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                  id="catatan" name="catatan" 
                                  rows="3" placeholder="Tambahkan keterangan jika diperlukan (contoh: nama bank, nomor rekening, dll)">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-warning">
                        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Perhatian</h6>
                        <ul class="mb-0">
                            <li>Pastikan bukti pembayaran jelas dan terbaca</li>
                            <li>Pembayaran akan diverifikasi oleh admin dalam 1x24 jam</li>
                            <li>Anda dapat menghapus bukti bayar selama status masih "Menunggu Verifikasi"</li>
                            <li>Pastikan jumlah yang dibayar sesuai dengan jenis pembayaran yang dipilih</li>
                            <li>Hubungi admin jika ada pertanyaan</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.gradient-card {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
}

.card {
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.text-white {
    color: white !important;
} 

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.badge {
    font-size: 0.75em;
    padding: 0.5em 0.75em;
}

.btn {
    border-radius: 10px;
    font-weight: 500;
}

.modal-content {
    border-radius: 15px;
    border: none;
}

.modal-header {
    border-bottom: 1px solid #e9ecef;
    background-color: #f8f9fa;
    border-radius: 15px 15px 0 0;
}

.alert {
    border-radius: 12px;
    border: none;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
}

.form-control, .form-select {
    border-radius: 8px;
    border: 2px solid #e9ecef;
}

.form-control:focus, .form-select:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

#imagePreview {
    background-color: #f8f9fa;
    border: 2px dashed #dee2e6;
}

#imagePreview img {
    max-width: 100%;
    border-radius: 8px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto close alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Preview image for tambah modal
    const buktiPembayaranInput = document.getElementById('bukti_pembayaran');
    const preview = document.getElementById('preview');
    const imagePreview = document.getElementById('imagePreview');
    
    if (buktiPembayaranInput) {
        buktiPembayaranInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Check file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file maksimal 2MB');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                
                reader.addEventListener('load', function() {
                    preview.src = reader.result;
                    imagePreview.style.display = 'block';
                });
                
                reader.readAsDataURL(file);
            } else {
                imagePreview.style.display = 'none';
            }
        });
    }

    // Set maksimal tanggal ke hari ini
    const today = new Date().toISOString().split('T')[0];
    const tanggalBayarInput = document.getElementById('tanggal_bayar');
    if (tanggalBayarInput) {
        tanggalBayarInput.max = today;
    }

    // Form validation before submit for tambah modal
    const tambahForm = document.getElementById('tambahPembayaranForm');
    if (tambahForm) {
        tambahForm.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        });
    }

    // Clear image function
    window.clearImage = function() {
        if (buktiPembayaranInput) {
            buktiPembayaranInput.value = '';
        }
        if (imagePreview) {
            imagePreview.style.display = 'none';
        }
    };

    // Set max date for edit modals
    document.querySelectorAll('[id^="edit_tanggal_bayar"]').forEach(function(input) {
        input.max = today;
    });

    // Reset form when modal is closed
    const tambahModal = document.getElementById('tambahPembayaranModal');
    if (tambahModal) {
        tambahModal.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('tambahPembayaranForm');
            if (form) {
                form.reset();
            }
            clearImage();
        });
    }
});
</script>
@endsection