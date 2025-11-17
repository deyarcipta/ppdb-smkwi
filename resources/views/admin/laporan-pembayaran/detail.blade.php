@if($siswa)
<div class="detail-content">
    <!-- Header Info -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h4 class="text-primary mb-1">{{ $siswa->datasiswa->nama_lengkap ?? 'N/A' }}</h4>
                    <p class="text-muted mb-0">
                        <strong>No Pendaftaran:</strong> {{ $siswa->datasiswa->no_pendaftaran ?? '-' }} | 
                        <strong>Email:</strong> {{ $siswa->email }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-end">
            <div class="total-section">
                <h5 class="text-success mb-0">Total Pembayaran</h5>
                <h3 class="text-success fw-bold">Rp {{ number_format($totalSemua, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <!-- Ringkasan Pembayaran -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-light">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="mb-0 text-primary">
                        <i class="bx bx-pie-chart-alt me-2"></i>
                        Ringkasan Pembayaran
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 bg-white">
                                <h6 class="text-muted mb-2">Formulir</h6>
                                <h4 class="text-primary fw-bold">Rp {{ number_format($totalFormulir, 0, ',', '.') }}</h4>
                                <small class="text-muted">{{ $pembayaran->where('jenis_pembayaran', 'formulir')->count() }} transaksi</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 bg-white">
                                <h6 class="text-muted mb-2">PPDB</h6>
                                <h4 class="text-info fw-bold">Rp {{ number_format($totalPPDB, 0, ',', '.') }}</h4>
                                <small class="text-muted">{{ $pembayaran->where('jenis_pembayaran', 'ppdb')->count() }} transaksi</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Transaksi -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="mb-0 text-primary">
                        <i class="bx bx-list-ul me-2"></i>
                        Detail Transaksi Pembayaran
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($pembayaran->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="60" class="text-center">#</th>
                                    <th class="text-center">Jenis Pembayaran</th>
                                    <th class="text-center">Tanggal Bayar</th>
                                    <th class="text-end">Jumlah</th>
                                    <th class="text-center">Metode</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pembayaran as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">
                                        <span class="badge 
                                            @if($item->jenis_pembayaran == 'formulir') bg-primary
                                            @elseif($item->jenis_pembayaran == 'ppdb') bg-info
                                            @else bg-secondary @endif">
                                            {{ ucfirst(str_replace('_', ' ', $item->jenis_pembayaran)) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y') }}
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">
                                            {{ ucfirst($item->metode_pembayaran ?? 'Transfer') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">
                                            <i class="bx bx-check-circle me-1"></i>
                                            Terverifikasi
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <small class="text-muted">{{ $item->keterangan ?? '-' }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total Keseluruhan:</td>
                                    <td class="text-end fw-bold text-success fs-5">
                                        Rp {{ number_format($totalSemua, 0, ',', '.') }}
                                    </td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-5">
                        <i class="bx bx-receipt display-4 opacity-50"></i>
                        <p class="mt-3 mb-0 fs-5">Belum ada transaksi pembayaran</p>
                        <small>Data transaksi pembayaran akan muncul di sini</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Tambahan -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-0 bg-light">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="mb-0 text-primary">
                        <i class="bx bx-info-circle me-2"></i>
                        Informasi Siswa
                    </h6>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <small class="text-muted d-block">Nama Lengkap</small>
                        <strong>{{ $siswa->datasiswa->nama_lengkap ?? 'N/A' }}</strong>
                    </div>
                    <div class="info-item">
                        <small class="text-muted d-block">No Pendaftaran</small>
                        <strong>{{ $siswa->datasiswa->no_pendaftaran ?? '-' }}</strong>
                    </div>
                    <div class="info-item">
                        <small class="text-muted d-block">Email</small>
                        <span>{{ $siswa->email }}</span>
                    </div>
                    <div class="info-item">
                        <small class="text-muted d-block">Total Transaksi</small>
                        <span class="badge bg-info">{{ $pembayaran->count() }} kali pembayaran</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 bg-light">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="mb-0 text-primary">
                        <i class="bx bx-calendar me-2"></i>
                        Periode Pembayaran
                    </h6>
                </div>
                <div class="card-body">
                    @if($pembayaran->count() > 0)
                    <div class="info-item">
                        <small class="text-muted d-block">Pembayaran Pertama</small>
                        <strong>{{ \Carbon\Carbon::parse($pembayaran->last()->tanggal_bayar)->format('d F Y') }}</strong>
                    </div>
                    <div class="info-item">
                        <small class="text-muted d-block">Pembayaran Terakhir</small>
                        <strong>{{ \Carbon\Carbon::parse($pembayaran->first()->tanggal_bayar)->format('d F Y') }}</strong>
                    </div>
                    @else
                    <div class="text-center text-muted py-3">
                        <i class="bx bx-time display-6 opacity-50"></i>
                        <p class="mt-2 mb-0">Belum ada riwayat pembayaran</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="alert alert-danger">
    <i class="bx bx-error-alt me-2"></i>
    Data siswa tidak ditemukan.
</div>
@endif