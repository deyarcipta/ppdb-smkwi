@extends('siswa.layouts.app')
@section('title', 'Dashboard Siswa')

@section('content')
@php
    $siswaId = auth()->user()->id;
@endphp
<!-- Header Welcome -->
<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <div class="card bg-gradient-primary text-white">
      <div class="d-flex align-items-center row">
        <div class="col-sm-8">
          <div class="card-body">
            <h5 class="card-title text-white">Selamat Datang, {{ $dataSiswa->nama_lengkap ?? 'Calon Siswa' }}! 🎓</h5>
            <p class="mb-0">
              Selamat datang di sistem PPDB SMK Wisata Indonesia. Ikuti langkah-langkah pendaftaran berikut untuk menyelesaikan proses pendaftaran Anda.
            </p>
          </div>
        </div>
        <div class="col-sm-4 text-center text-sm-left">
          <img src="{{ asset('sneat/img/illustrations/man-with-laptop.png') }}" height="140" alt="Student Dashboard">
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Upload Bukti Pembayaran Formulir (Hanya tampil jika belum upload) -->
@if($showUploadForm)
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
          <i class='bx bx-upload me-2'></i>
          @if($pembayaran && $pembayaran->status == 'ditolak')
            Upload Ulang Bukti Pembayaran Formulir
          @else
            Upload Bukti Pembayaran Formulir
          @endif
        </h5>
      </div>
      <div class="card-body">
        @if($pembayaran && $pembayaran->status == 'ditolak')
        <div class="alert alert-danger">
          <i class='bx bx-error-circle me-2'></i>
          <strong>Bukti Pembayaran Ditolak!</strong> {{ $pembayaran->catatan ?? 'Silakan upload ulang bukti pembayaran yang valid.' }}
        </div>
        @else
        <div class="alert alert-info">
          <i class='bx bx-info-circle me-2'></i>
          <strong>Langkah Pertama!</strong> Silakan upload bukti pembayaran formulir untuk memulai proses pendaftaran.
        </div>
        @endif
        
        <form action="{{ route('siswa.upload-bukti-formulir') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="col-md-8">
              <!-- Metode Pembayaran -->
              <div class="mb-3">
                <label for="metode_pembayaran" class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required>
                  <option value="">Pilih Metode Pembayaran</option>
                  <option value="transfer" {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                  <option value="cash" {{ old('metode_pembayaran') == 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                </select>
                <div class="form-text">Pilih metode pembayaran yang digunakan</div>
              </div>

              <!-- Informasi Transfer (Hanya tampil jika pilih transfer) -->
              <div class="mb-3" id="info-transfer" style="display: none;">
                <div class="alert alert-primary">
                  <h6 class="alert-heading">Informasi Transfer Bank:</h6>
                  <p class="mb-1"><strong>Bank:</strong> {{ $infoPembayaran->nama_bank ?? '-' }}</p>
                  <p class="mb-1"><strong>No. Rekening:</strong> {{ $infoPembayaran->nomor_rekening ?? '-' }}</p>
                  <p class="mb-1"><strong>Atas Nama:</strong> {{ $infoPembayaran->atas_nama ?? '-' }}</p>
                  <p class="mb-0"><strong>Jumlah:</strong> Rp 100.000</p>
                </div>
              </div>

              <!-- Informasi Cash (Hanya tampil jika pilih cash) -->
              <div class="mb-3" id="info-cash" style="display: none;">
                <div class="alert alert-warning">
                  <h6 class="alert-heading">Informasi Pembayaran Tunai:</h6>
                  <p class="mb-1"><strong>Lokasi Pembayaran:</strong> Kantor Administrasi SMP Contoh Jakarta</p>
                  <p class="mb-1"><strong>Jam Operasional:</strong> Senin - Jumat, 08:00 - 15:00 WIB</p>
                  <p class="mb-1"><strong>Jumlah:</strong> Rp 100.000</p>
                  <p class="mb-0"><strong>Bawa:</strong> Formulir pendaftaran dan uang tepat</p>
                </div>
              </div>

              <div class="mb-3">
                <label for="bukti_pembayaran" class="form-label">Upload Bukti Pembayaran <span class="text-danger">*</span></label>
                <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*,.pdf" required>
                <div class="form-text">
                  <span id="file-requirements">
                    Format: JPG, PNG, PDF (Maks. 2MB)
                  </span>
                </div>
              </div>
              
              <div class="mb-3">
                <label class="form-label">Keterangan (Opsional)</label>
                <textarea class="form-control" name="keterangan" rows="2" placeholder="Tambahkan keterangan jika diperlukan...">{{ old('keterangan') }}</textarea>
              </div>
            </div>
            <div class="col-md-4">
              <div class="alert alert-warning">
                <h6>Informasi Penting:</h6>
                <p class="mb-2"><strong>Biaya Formulir:</strong> Rp 100.000</p>
                <p class="mb-0 small">Setelah upload, bukti akan diverifikasi oleh admin dalam 1-2 hari kerja.</p>
              </div>

              <!-- Informasi Kontak -->
              <div class="card border-0 bg-light">
                <div class="card-body">
                  <h6 class="card-title">Butuh Bantuan?</h6>
                  <p class="small mb-2">
                    <i class='bx bx-phone me-2'></i>
                    <strong>Telepon:</strong> {{$pengaturan_aplikasi->telepon ?? '021-12345678'}}
                  </p>
                  <p class="small mb-0">
                    <i class='bx bx-envelope me-2'></i>
                    <strong>Email:</strong> {{$pengaturan_aplikasi->email ?? '-'}}
                  </p>
                </div>
              </div>
            </div>
          </div>
          
          <button type="submit" class="btn btn-primary">
            <i class='bx bx-upload me-2'></i>
            @if($pembayaran && $pembayaran->status == 'ditolak')
              Upload Ulang Bukti
            @else
              Upload Bukti Pembayaran
            @endif
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Progress Pendaftaran (6 Step) -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Progress Pendaftaran</h5>
        <span class="badge bg-{{ $statusColor }}">{{ $statusText }}</span>
      </div>
      <div class="card-body">
        <!-- Progress Steps Modern -->
        <div class="progress-modern">
          <!-- Step 1: Upload Bukti Pembayaran Formulir -->
          <div class="progress-step {{ $currentStep >= 1 ? 'active' : '' }} {{ $currentStep == 1 ? 'current' : '' }}">
            <div class="step-circle">
              <span class="step-number">1</span>
              <i class='bx bx-check step-icon'></i>
            </div>
            <div class="step-label">UPLOAD</div>
            <div class="step-title">Bukti Pembayaran Formulir</div>
          </div>
          
          <div class="progress-connector {{ $currentStep >= 2 ? 'active' : '' }}"></div>
          
          <!-- Step 2: Pilih Jurusan -->
          <div class="progress-step {{ $currentStep >= 2 ? 'active' : '' }} {{ $currentStep == 2 ? 'current' : '' }}">
            <div class="step-circle">
              <span class="step-number">2</span>
              <i class='bx bx-check step-icon'></i>
            </div>
            <div class="step-label">SELECT</div>
            <div class="step-title">Pilih Jurusan</div>
          </div>
          
          <div class="progress-connector {{ $currentStep >= 3 ? 'active' : '' }}"></div>
          
          <!-- Step 3: Pengisian Formulir -->
          <div class="progress-step {{ $currentStep >= 3 ? 'active' : '' }} {{ $currentStep == 3 ? 'current' : '' }}">
            <div class="step-circle">
              <span class="step-number">3</span>
              <i class='bx bx-check step-icon'></i>
            </div>
            <div class="step-label">FORM</div>
            <div class="step-title">Pengisian Formulir</div>
          </div>
          
          <div class="progress-connector {{ $currentStep >= 4 ? 'active' : '' }}"></div>
          
          <!-- Step 4: Pembayaran PPDB (DIPINDAH KE POSISI INI) -->
          <div class="progress-step {{ $currentStep >= 4 ? 'active' : '' }} {{ $currentStep == 4 ? 'current' : '' }}">
            <div class="step-circle">
              <span class="step-number">4</span>
              <i class='bx bx-check step-icon'></i>
            </div>
            <div class="step-label">PAYMENT</div>
            <div class="step-title">Pembayaran PPDB</div>
          </div>
          
          <div class="progress-connector {{ $currentStep >= 5 ? 'active' : '' }}"></div>
          
          <!-- Step 5: Review/Verifikasi Data (DIPINDAH KE POSISI INI) -->
          <div class="progress-step {{ $currentStep >= 5 ? 'active' : '' }} {{ $currentStep == 5 ? 'current' : '' }}">
            <div class="step-circle">
              <span class="step-number">5</span>
              <i class='bx bx-check step-icon'></i>
            </div>
            <div class="step-label">REVIEW</div>
            <div class="step-title">Verifikasi Data</div>
          </div>
          
          <div class="progress-connector {{ $currentStep >= 6 ? 'active' : '' }}"></div>
          
          <!-- Step 6: Selesai -->
          <div class="progress-step {{ $currentStep >= 6 ? 'active' : '' }} {{ $currentStep == 6 ? 'current' : '' }}">
            <div class="step-circle">
              <span class="step-number">6</span>
              <i class='bx bx-check step-icon'></i>
            </div>
            <div class="step-label">SUCCESS</div>
            <div class="step-title">Selesai</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Status Saat Ini Berdasarkan Step -->
@if($currentStep == 1)
<!-- Step 1: UPLOAD - Upload Bukti Pembayaran Formulir -->
<!-- Form upload sudah ditampilkan di atas melalui $showUploadForm -->

@elseif($currentStep == 2)
<!-- Step 2: SELECT - Pilih Jurusan -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body text-center py-5">
        <div class="mb-4">
          <i class='bx bx-book-open bx-lg text-primary'></i>
        </div>
        <h4 class="text-primary">Pilih Jurusan Pendidikan</h4>
        <p class="text-muted mb-4">
          Silakan pilih jurusan yang Anda minati untuk melanjutkan proses pendaftaran.
        </p>
        
        <!-- Tombol untuk membuka modal -->
        <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#pilihJurusanModal">
          <i class='bx bx-book me-2'></i>Pilih Jurusan
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Pilih Jurusan -->
<div class="modal fade" id="pilihJurusanModal" tabindex="-1" aria-labelledby="pilihJurusanModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pilihJurusanModalLabel">
          <i class='bx bx-book me-2'></i>Pilih Jurusan Pendidikan
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
          <i class='bx bx-info-circle me-2'></i>
          Silakan pilih salah satu jurusan yang tersedia. Pilihan ini akan menentukan program pendidikan yang akan Anda ikuti.
        </div>

        <!-- Daftar Jurusan -->
        <div class="row" id="daftarJurusan">
          <!-- Data jurusan akan diisi via JavaScript/AJAX -->
          <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Memuat jurusan...</span>
            </div>
            <p class="mt-2 text-muted">Memuat daftar jurusan...</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class='bx bx-x me-2'></i>Batal
        </button>
        <button type="button" class="btn btn-primary" id="simpanJurusanBtn" disabled>
          <i class='bx bx-save me-2'></i>Simpan Pilihan
        </button>
      </div>
    </div>
  </div>
</div>

@elseif($currentStep == 3)
<!-- Step 3: FORM - Pengisian Formulir -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body text-center py-5">
        <div class="mb-4">
          <i class='bx bx-edit bx-lg text-info'></i>
        </div>
        <h4 class="text-info">Pengisian Formulir Pendaftaran</h4>
        <p class="text-muted mb-4">
          Silakan lengkapi formulir pendaftaran dengan data yang benar dan lengkap.
        </p>
        <a href="{{ route('siswa.formulir') }}" class="btn btn-info btn-lg">
          <i class='bx bx-edit me-2'></i>Isi Formulir
        </a>
      </div>
    </div>
  </div>
</div>

@elseif($currentStep == 4)
<!-- Step 4: PAYMENT - Pembayaran PPDB -->
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-5">
        <!-- Header -->
        <div class="text-center mb-5">
          <div class="bg-light-success rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
            <i class='bx bx-credit-card bx-lg text-success'></i>
          </div>
          <h3 class="text-success fw-bold mb-3">Pembayaran Biaya PPDB</h3>
          <p class="text-muted mb-0">
            Formulir pendaftaran Anda telah lengkap. Silakan lakukan pembayaran biaya PPDB untuk melanjutkan proses verifikasi.
          </p>
        </div>

        @php
          // Inisialisasi variable
          $totalBiaya = $masterPPDB->total_biaya ?? 0;
          $totalDibayar = 0;
          $sisaPembayaran = $totalBiaya;
          $statusPembayaran = 'belum_bayar';
          
          // Ambil data pembayaran
          if (isset($dataSiswa->pembayaran) && $dataSiswa->pembayaran->isNotEmpty()) {
              // Gunakan data dari relasi jika ada
              $pembayaranPPDB = $dataSiswa->pembayaran->where('jenis_pembayaran', 'ppdb');
          } else {
              // Query langsung jika relasi tidak ada
              $pembayaranPPDB = \App\Models\Pembayaran::where('user_id', auth()->id())
                  ->where('jenis_pembayaran', 'ppdb')
                  ->get();
          }
          
          // Proses perhitungan jika ada data pembayaran
          if ($pembayaranPPDB && $pembayaranPPDB->isNotEmpty()) {
              // Hitung total pembayaran yang sudah diverifikasi
              $totalDibayar = $pembayaranPPDB->where('status', 'diverifikasi')->sum('jumlah');
              $sisaPembayaran = $totalBiaya - $totalDibayar;
              
              // Tentukan status berdasarkan total pembayaran
              if ($totalDibayar >= $totalBiaya) {
                  $statusPembayaran = 'lunas';
              } elseif ($totalDibayar > 0) {
                  $statusPembayaran = 'belum_lunas';
              }
              
              // Override status jika ada pembayaran pending
              if ($pembayaranPPDB->where('status', 'pending')->isNotEmpty()) {
                  $statusPembayaran = 'menunggu_verifikasi';
              }
              
              // Override status jika ada pembayaran ditolak
              if ($pembayaranPPDB->where('status', 'ditolak')->isNotEmpty()) {
                  $statusPembayaran = 'ditolak';
              }
          }
      @endphp

        <!-- Detail Pembayaran -->
        <div class="card bg-light border-0 mb-4">
          <div class="card-body">
            <h5 class="card-title text-dark mb-4">Detail Pembayaran</h5>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label text-muted small mb-1">Jurusan</label>
                <p class="fw-semibold mb-0 text-dark">{{ $dataSiswa->jurusan->nama_jurusan ?? '-' }}</p>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label text-muted small mb-1">Biaya PPDB</label>
                <p class="fw-semibold mb-0 text-dark">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</p>
              </div>
              <div class="col-12">
                <label class="form-label text-muted small mb-1">Status Pembayaran</label>
                <div>
                  @if($statusPembayaran == 'lunas')
                    <span class="badge bg-success bg-opacity-20 text-light border border-success border-opacity-25 px-3 py-2 rounded-pill">
                      <i class='bx bx-check-circle me-1'></i>Lunas
                    </span>
                  @elseif($statusPembayaran == 'belum_lunas')
                    <span class="badge bg-warning bg-opacity-20 text-dark border border-warning border-opacity-25 px-3 py-2 rounded-pill">
                      <i class='bx bx-time-five me-1'></i>Belum Lunas
                    </span>
                  @else
                    <span class="badge bg-danger bg-opacity-20 text-light border border-danger border-opacity-25 px-3 py-2 rounded-pill">
                      <i class='bx bx-x-circle me-1'></i>Belum Bayar
                    </span>
                  @endif
                </div>
              </div>
            </div>

            <!-- Ringkasan Pembayaran -->
            <div class="mt-4 p-3 bg-white rounded border">
              <h6 class="text-dark mb-3">
                <i class='bx bx-receipt me-2 text-primary'></i>Ringkasan Pembayaran
              </h6>
              <div class="row">
                <div class="col-md-4 mb-2">
                  <small class="text-muted d-block">Total Biaya</small>
                  <small class="fw-semibold text-dark">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</small>
                </div>
                <div class="col-md-4 mb-2">
                  <small class="text-muted d-block">Total Dibayar</small>
                  <small class="fw-semibold text-success">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</small>
                </div>
                <div class="col-md-4 mb-2">
                  <small class="text-muted d-block">Sisa Pembayaran</small>
                  <small class="fw-semibold text-warning">Rp {{ number_format($sisaPembayaran, 0, ',', '.') }}</small>
                </div>
              </div>
            </div>

            <!-- Riwayat Pembayaran Jika Ada -->
            @if($pembayaranPPDB && $pembayaranPPDB->count() > 0)
              <div class="mt-4 p-3 bg-white rounded border">
                <h6 class="text-dark mb-3">
                  <i class='bx bx-history me-2 text-primary'></i>Riwayat Pembayaran
                </h6>
                <div class="table-responsive">
                  <table class="table table-sm table-borderless mb-0">
                    <thead>
                      <tr>
                        <th class="text-muted small">Tanggal</th>
                        <th class="text-muted small">Nominal</th>
                        <th class="text-muted small">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($pembayaranPPDB as $pembayaran)
                      <tr>
                        <td class="small">{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') }}</td>
                        <td class="small fw-semibold text-success">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                        <td class="small">
                          @if($pembayaran->status == 'diverifikasi')
                            <span class="badge bg-success bg-opacity-10 text-success">Terverifikasi</span>
                          @elseif($pembayaran->status == 'pending')
                            <span class="badge bg-warning bg-opacity-10 text-warning">Menunggu</span>
                          @else
                            <span class="badge bg-danger bg-opacity-10 text-danger">Ditolak</span>
                          @endif
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            @endif
          </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="text-center">
          @if($statusPembayaran == 'lunas')
            <div class="alert alert-success mx-auto" style="max-width: 400px;">
              <i class='bx bx-check-circle me-2'></i>Pembayaran Anda sudah lunas. Data sedang diverifikasi.
            </div>
          @elseif($statusPembayaran == 'belum_lunas')
            <a href="{{ route('siswa.pembayaran.index') }}" class="btn btn-warning btn-lg px-5 py-3 fw-semibold">
              <i class='bx bx-credit-card me-2'></i>Lanjutkan Pembayaran
            </a>
            <div class="mt-3">
              <small class="text-muted">
                <i class='bx bx-info-circle me-1'></i>Anda sudah melakukan pembayaran Rp {{ number_format($totalDibayar, 0, ',', '.') }}. Silakan lunasi sisa pembayaran Rp {{ number_format($sisaPembayaran, 0, ',', '.') }}.
              </small>
            </div>
          @else
            <a href="{{ route('siswa.pembayaran.index') }}" class="btn btn-success btn-lg px-5 py-3 fw-semibold">
              <i class='bx bx-credit-card me-2'></i>Bayar Sekarang
            </a>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

@elseif($currentStep == 5)
<!-- Step 5: REVIEW - Verifikasi Data (DIPINDAH KE POSISI INI) -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body text-center py-5">
        <div class="mb-4">
          <i class='bx bx-check-circle bx-lg text-warning'></i>
        </div>
        <h4 class="text-warning">Verifikasi Data Pendaftaran</h4>
        <p class="text-muted mb-4">
          Pembayaran PPDB Anda telah berhasil. Data pendaftaran Anda sedang dalam proses verifikasi oleh admin. 
          Silakan tunggu konfirmasi untuk menyelesaikan proses pendaftaran.
        </p>
        
        <div class="alert alert-info mx-auto" style="max-width: 400px;">
          <h6>Status Verifikasi:</h6>
          <p class="mb-1"><strong>Jurusan:</strong> {{ $dataSiswa->jurusan->nama_jurusan ?? 'Belum dipilih' }}</p>
          <p class="mb-1"><strong>Pembayaran:</strong> <span class="badge bg-success">Terverifikasi</span></p>
          <p class="mb-0"><strong>Status Data:</strong> <span class="badge bg-info">Dalam Review</span></p>
        </div>
        
        <div class="spinner-border text-warning" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3 small text-muted">Proses verifikasi membutuhkan waktu 1-2 hari kerja</p>
      </div>
    </div>
  </div>
</div>

@elseif($currentStep == 6)
<!-- Step 6: SUCCESS - Selesai -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body text-center py-5">
        <div class="mb-4">
          <i class='bx bx-party bx-lg text-success'></i>
        </div>
        <h4 class="text-success">Pendaftaran Berhasil!</h4>
        <p class="text-muted mb-4">
          Selamat! Anda telah menyelesaikan seluruh proses pendaftaran PPDB SMK Wisata Indonesia.
        </p>
        
        <div class="alert alert-success mx-auto" style="max-width: 500px;">
          <h6>Detail Pendaftaran:</h6>
          <div class="row text-start">
            <div class="col-md-6">
              <p class="mb-1"><strong>Nama:</strong> {{ $dataSiswa->nama_lengkap }}</p>
              <p class="mb-1"><strong>Jurusan:</strong> {{ $dataSiswa->jurusan->nama_jurusan ?? '-' }}</p>
            </div>
            <div class="col-md-6">
              <p class="mb-1"><strong>No. Pendaftaran:</strong> {{ $dataSiswa->no_pendaftaran }}</p>
              <p class="mb-1"><strong>Status:</strong> <span class="badge bg-success">Terverifikasi</span></p>
            </div>
          </div>
        </div>
        
        <div class="d-flex justify-content-center gap-3 mt-4">
          <!-- Tombol Lihat Formulir dengan Modal -->
          <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailModal{{ $siswaId }}">
            <i class='bx bx-file me-2'></i>Lihat Formulir
          </button>
          
          <a href="{{ route('siswa.pengumuman.index') }}" class="btn btn-outline-success">
            <i class='bx bx-news me-2'></i>Pengumuman
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Modal Formulir untuk Siswa -->
<div class="modal fade" id="detailModal{{ $siswaId }}" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true"
     data-nama="{{ $dataSiswa->nama_lengkap ?? '-' }}"
     data-no-pendaftaran="{{ $dataSiswa->no_pendaftaran ?? '-' }}"
     data-nisn="{{ $dataSiswa->nisn ?? '-' }}"
     data-nik="{{ $dataSiswa->nik ?? '-' }}"
     data-no-kk="{{ $dataSiswa->no_kk ?? '-' }}"
     data-tempat-lahir="{{ $dataSiswa->tempat_lahir ?? '-' }}"
     data-tanggal-lahir="{{ $dataSiswa->tanggal_lahir ? \Carbon\Carbon::parse($dataSiswa->tanggal_lahir)->format('d-m-Y') : '-' }}"
     data-jenis-kelamin="{{ $dataSiswa->jenis_kelamin ?? '-' }}"
     data-agama="{{ $dataSiswa->agama ?? '-' }}"
     data-no-hp="{{ $dataSiswa->no_hp ?? '-' }}"
     data-asal-sekolah="{{ $dataSiswa->asal_sekolah ?? '-' }}"
     data-alamat="{{ $dataSiswa->alamat ?? '-' }}"
     data-rt="{{ $dataSiswa->rt ?? '-' }}"
     data-rw="{{ $dataSiswa->rw ?? '-' }}"
     data-desa="{{ $dataSiswa->desa ?? '-' }}"
     data-kecamatan="{{ $dataSiswa->kecamatan ?? '-' }}"
     data-kota="{{ $dataSiswa->kota ?? '-' }}"
     data-provinsi="{{ $dataSiswa->provinsi ?? '-' }}"
     data-kode-pos="{{ $dataSiswa->kode_pos ?? '-' }}"
     data-tinggi-badan="{{ $dataSiswa->tinggi_badan ?? '-' }}"
     data-berat-badan="{{ $dataSiswa->berat_badan ?? '-' }}"
     data-anak-ke="{{ $dataSiswa->anak_ke ?? '-' }}"
     data-jumlah-saudara="{{ $dataSiswa->jumlah_saudara ?? '-' }}"
     data-status-dalam-keluarga="{{ $dataSiswa->status_dalam_keluarga ?? '-' }}"
     data-tinggal-bersama="{{ $dataSiswa->tinggal_bersama ?? '-' }}"
     data-jarak-kesekolah="{{ $dataSiswa->jarak_kesekolah ?? '-' }}"
     data-waktu-tempuh="{{ $dataSiswa->waktu_tempuh ?? '-' }}"
     data-transportasi="{{ $dataSiswa->transportasi ?? '-' }}"
     data-ukuran-baju="{{ $dataSiswa->ukuran_baju ?? '-' }}"
     data-hobi="{{ $dataSiswa->hobi ?? '-' }}"
     data-cita-cita="{{ $dataSiswa->cita_cita ?? '-' }}"
     data-tahun-lulus="{{ $dataSiswa->tahun_lulus ?? '-' }}"
     data-no-kip="{{ $dataSiswa->no_kip ?? '-' }}"
     data-referensi="{{ $dataSiswa->referensi ?? '-' }}"
     data-ket-referensi="{{ $dataSiswa->ket_referensi ?? '-' }}"
     data-nik-ayah="{{ $dataSiswa->nik_ayah ?? '-' }}"
     data-nama-ayah="{{ $dataSiswa->nama_ayah ?? '-' }}"
     data-tempat-lahir-ayah="{{ $dataSiswa->tempat_lahir_ayah ?? '-' }}"
     data-tanggal-lahir-ayah="{{ $dataSiswa->tanggal_lahir_ayah ? \Carbon\Carbon::parse($dataSiswa->tanggal_lahir_ayah)->format('d-m-Y') : '-' }}"
     data-pendidikan-ayah="{{ $dataSiswa->pendidikan_ayah ?? '-' }}"
     data-pekerjaan-ayah="{{ $dataSiswa->pekerjaan_ayah ?? '-' }}"
     data-penghasilan-ayah="{{ $dataSiswa->penghasilan_ayah ?? '-' }}"
     data-no-hp-ayah="{{ $dataSiswa->no_hp_ayah ?? '-' }}"
     data-nik-ibu="{{ $dataSiswa->nik_ibu ?? '-' }}"
     data-nama-ibu="{{ $dataSiswa->nama_ibu ?? '-' }}"
     data-tempat-lahir-ibu="{{ $dataSiswa->tempat_lahir_ibu ?? '-' }}"
     data-tanggal-lahir-ibu="{{ $dataSiswa->tanggal_lahir_ibu ? \Carbon\Carbon::parse($dataSiswa->tanggal_lahir_ibu)->format('d-m-Y') : '-' }}"
     data-pendidikan-ibu="{{ $dataSiswa->pendidikan_ibu ?? '-' }}"
     data-pekerjaan-ibu="{{ $dataSiswa->pekerjaan_ibu ?? '-' }}"
     data-penghasilan-ibu="{{ $dataSiswa->penghasilan_ibu ?? '-' }}"
     data-no-hp-ibu="{{ $dataSiswa->no_hp_ibu ?? '-' }}"
     data-nik-wali="{{ $dataSiswa->nik_wali ?? '-' }}"
     data-nama-wali="{{ $dataSiswa->nama_wali ?? '-' }}"
     data-tempat-lahir-wali="{{ $dataSiswa->tempat_lahir_wali ?? '-' }}"
     data-tanggal-lahir-wali="{{ $dataSiswa->tanggal_lahir_wali ? \Carbon\Carbon::parse($dataSiswa->tanggal_lahir_wali)->format('d-m-Y') : '-' }}"
     data-pendidikan-wali="{{ $dataSiswa->pendidikan_wali ?? '-' }}"
     data-pekerjaan-wali="{{ $dataSiswa->pekerjaan_wali ?? '-' }}"
     data-penghasilan-wali="{{ $dataSiswa->penghasilan_wali ?? '-' }}"
     data-no-hp-wali="{{ $dataSiswa->no_hp_wali ?? '-' }}"
     data-gelombang="{{ $dataSiswa->gelombang->nama_gelombang ?? '-' }}"
     data-jurusan="{{ $dataSiswa->jurusan->nama_jurusan ?? '-' }}"
     data-email="{{ auth()->user()->email ?? '-' }}"
     data-tanggal-daftar="{{ auth()->user()->created_at->format('d-m-Y H:i') }}">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailModalLabel">Formulir Pendaftaran Lengkap</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @php
                    // Hitung total pembayaran yang sudah diverifikasi
                    $totalBayar = isset($dataSiswa->pembayaran) ? $dataSiswa->pembayaran->where('status', 'diverifikasi')->sum('jumlah') : 0;
                    $statusPembayaran = 'belum_bayar';
                    $badgeClass = 'bg-secondary';
                    $statusText = 'Belum Bayar';
                    
                    $totalBiayaPPDB = isset($masterPPDB->total_biaya) ? $masterPPDB->total_biaya : 0;
                    
                    if ($totalBayar > 0) {
                        if ($totalBayar >= $totalBiayaPPDB) {
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
                    $pembayaranPending = isset($dataSiswa->pembayaran) && $dataSiswa->pembayaran->where('status', 'pending')->count() > 0;
                    if ($pembayaranPending) {
                        $statusPembayaran = 'menunggu_verifikasi';
                        $badgeClass = 'bg-info';
                        $statusText = 'Menunggu Verifikasi';
                    }
                @endphp
                
                <!-- Informasi Akun & Pendaftaran -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 text-primary">Informasi Akun & Pendaftaran</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Username:</strong></div>
                                    <div class="col-sm-8">{{ auth()->user()->username ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Password:</strong></div>
                                    <div class="col-sm-8">••••••••</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Email:</strong></div>
                                    <div class="col-sm-8">{{ auth()->user()->email ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Status Akun:</strong></div>
                                    <div class="col-sm-8">
                                        @if($currentStep >= 6)
                                            <span class="badge bg-success text-white">DITERIMA</span>
                                        @else
                                            <span class="badge bg-warning text-white">Dalam Proses</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 text-primary">Informasi Pendaftaran</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>No. Pendaftaran:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->no_pendaftaran ?? auth()->user()->username }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Gelombang:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->gelombang->nama_gelombang ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Jurusan:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->jurusan->nama_jurusan ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tanggal Daftar:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->created_at->format('d M Y H:i') ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Pribadi -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">Data Pribadi</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Nama Lengkap:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->nama_lengkap ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>NISN:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->nisn ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>NIK:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->nik ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>No. KK:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->no_kk ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Jenis Kelamin:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->jenis_kelamin ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tempat Lahir:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->tempat_lahir ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tanggal Lahir:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->tanggal_lahir ? \Carbon\Carbon::parse($dataSiswa->tanggal_lahir)->format('d M Y') : '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Agama:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->agama ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>No. HP:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->no_hp ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Email:</strong></div>
                                    <div class="col-sm-8">{{ auth()->user()->email ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Ukuran Baju:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->ukuran_baju ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Hobi:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->hobi ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Cita-cita:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->cita_cita ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Anak Ke:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->anak_ke ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Jumlah Saudara:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->jumlah_saudara ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tinggi Badan:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->tinggi_badan ? $dataSiswa->tinggi_badan . ' cm' : '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Berat Badan:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->berat_badan ? $dataSiswa->berat_badan . ' kg' : '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Asal Sekolah:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->asal_sekolah ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alamat -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">Alamat Tempat Tinggal</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Alamat Lengkap:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->alamat ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>RT/RW:</strong></div>
                                    <div class="col-sm-8">
                                        @if($dataSiswa->rt || $dataSiswa->rw)
                                            RT {{ $dataSiswa->rt ?? '-' }} / RW {{ $dataSiswa->rw ?? '-' }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Desa/Kelurahan:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->desa ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Kecamatan:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->kecamatan ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Kota/Kabupaten:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->kota ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Provinsi:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->provinsi ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Kode Pos:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->kode_pos ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Status dalam Keluarga:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->status_dalam_keluarga ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tempat Tinggal -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">Informasi Tempat Tinggal</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tinggal Bersama:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->tinggal_bersama ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Jarak ke Sekolah:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->jarak_kesekolah ? $dataSiswa->jarak_kesekolah . ' km' : '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Waktu Tempuh:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->waktu_tempuh ? $dataSiswa->waktu_tempuh . ' menit' : '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Transportasi:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->transportasi ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Ayah -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">Data Ayah</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>NIK Ayah:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->nik_ayah ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Nama Ayah:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->nama_ayah ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tempat Lahir:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->tempat_lahir_ayah ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tanggal Lahir:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->tanggal_lahir_ayah ? \Carbon\Carbon::parse($dataSiswa->tanggal_lahir_ayah)->format('d M Y') : '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Pendidikan:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->pendidikan_ayah ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Pekerjaan:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->pekerjaan_ayah ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Penghasilan:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->penghasilan_ayah ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>No. HP:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->no_hp_ayah ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Ibu -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">Data Ibu</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>NIK Ibu:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->nik_ibu ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Nama Ibu:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->nama_ibu ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tempat Lahir:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->tempat_lahir_ibu ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tanggal Lahir:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->tanggal_lahir_ibu ? \Carbon\Carbon::parse($dataSiswa->tanggal_lahir_ibu)->format('d M Y') : '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Pendidikan:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->pendidikan_ibu ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Pekerjaan:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->pekerjaan_ibu ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Penghasilan:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->penghasilan_ibu ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>No. HP:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->no_hp_ibu ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Wali (jika ada) -->
                @if($dataSiswa->nik_wali || $dataSiswa->nama_wali)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">Data Wali</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>NIK Wali:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->nik_wali ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Nama Wali:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->nama_wali ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tempat Lahir:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->tempat_lahir_wali ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tanggal Lahir:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->tanggal_lahir_wali ? \Carbon\Carbon::parse($dataSiswa->tanggal_lahir_wali)->format('d M Y') : '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Pendidikan:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->pendidikan_wali ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Pekerjaan:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->pekerjaan_wali ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Penghasilan:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->penghasilan_wali ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>No. HP:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->no_hp_wali ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Informasi Tambahan -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">Informasi Tambahan</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>No. KIP:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->no_kip ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Referensi:</strong></div>
                                    <div class="col-sm-8">{{ $dataSiswa->referensi ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                        @if($dataSiswa->ket_referensi)
                        <div class="row">
                            <div class="col-12">
                                <div class="row mb-2">
                                    <div class="col-sm-2"><strong>Keterangan Referensi:</strong></div>
                                    <div class="col-sm-10">{{ $dataSiswa->ket_referensi }}</div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Informasi Pembayaran -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">Informasi Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="row mb-2">
                                    <div class="col-sm-6"><strong>Total Biaya PPDB:</strong></div>
                                    <div class="col-sm-6">Rp {{ number_format($totalBiayaPPDB, 0, ',', '.') }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-6"><strong>Total Dibayar:</strong></div>
                                    <div class="col-sm-6">Rp {{ number_format($totalBayar, 0, ',', '.') }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-6"><strong>Sisa Pembayaran:</strong></div>
                                    <div class="col-sm-6">
                                        @if($totalBayar >= $totalBiayaPPDB)
                                            <span class="text-success">LUNAS</span>
                                        @else
                                            Rp {{ number_format($totalBiayaPPDB - $totalBayar, 0, ',', '.') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-6"><strong>Status:</strong></div>
                                    <div class="col-sm-6">
                                        <span class="badge {{ $badgeClass }} text-white">{{ $statusText }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted">Riwayat Pembayaran</h6>
                                @if(isset($dataSiswa->pembayaran) && $dataSiswa->pembayaran->count() > 0)
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
                                                @foreach($dataSiswa->pembayaran as $pembayaran)
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
                                                    <td>{{ $pembayaran->catatan ?? '-' }}</td>
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
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class='bx bx-x me-1'></i>Tutup
                </button>
                <button type="button" class="btn btn-primary" onclick="printFormulir({{ $siswaId }})">
                    <i class='bx bx-printer me-1'></i>Print Formulir
                </button>
                {{-- <button type="button" class="btn btn-success" onclick="downloadFormulir()">
                    <i class='bx bx-download me-1'></i>Download PDF
                </button> --}}
            </div>
        </div>
    </div>
</div>

<!-- CSS Styles -->
<style>
/* Progress Modern Style */
.progress-modern {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  position: relative;
  margin: 2rem 0;
}

.progress-step {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
  z-index: 2;
  flex: 1;
  text-align: center;
  min-width: 0;
}

.step-circle {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: #e9ecef;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.5rem;
  border: 3px solid white;
  position: relative;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.step-number {
  font-size: 1.1rem;
  font-weight: 600;
  color: #6c757d;
  transition: all 0.3s ease;
}

.step-icon {
  display: none;
  font-size: 1.5rem;
  color: white;
  transition: all 0.3s ease;
}

.progress-step.active .step-circle {
  background: #0d6efd;
  transform: scale(1.1);
  box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
}

.progress-step.active .step-number {
  display: none;
}

.progress-step.active .step-icon {
  display: block;
}

.progress-step.current .step-circle {
  box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.3), 0 4px 8px rgba(13, 110, 253, 0.2);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(13, 110, 253, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(13, 110, 253, 0);
  }
}

.step-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  padding: 0 8px;
}

.step-label {
  font-size: 0.75rem;
  font-weight: 700;
  color: #6c757d;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 0.25rem;
  text-align: center;
  width: 100%;
  line-height: 1.2;
}

.step-title {
  font-size: 0.75rem;
  color: #6c757d;
  text-align: center;
  width: 100%;
  line-height: 1.3;
  font-weight: 500;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

.progress-step.active .step-label,
.progress-step.active .step-title {
  color: #0d6efd;
  font-weight: 700;
}

.progress-connector {
  flex: 1;
  height: 3px;
  background: #e9ecef;
  margin: 0 0.5rem;
  position: relative;
  top: 30px;
  transition: all 0.3s ease;
  border-radius: 2px;
}

.progress-connector.active {
  background: #0d6efd;
  box-shadow: 0 1px 3px rgba(13, 110, 253, 0.3);
}

/* Modal Styles */
.modal-xl {
  max-width: 1200px;
}

/* Print Styles untuk Formulir */
@media print {
  body * {
    visibility: hidden;
  }
  #detailModal{{ $siswaId }}, #detailModal{{ $siswaId }} * {
    visibility: visible;
  }
  #detailModal{{ $siswaId }} {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
  .modal-footer {
    display: none !important;
  }
}
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('sneat/js/printFormulir.js') }}"></script>
<script>
// Fungsi untuk print formulir
window.printFormulir = function(id) {
    doPrintFormulir(id, "detailModal");
};
document.addEventListener('DOMContentLoaded', function() {
    // JavaScript untuk upload bukti pembayaran
    const fileInput = document.getElementById('bukti_pembayaran');
    const metodePembayaran = document.getElementById('metode_pembayaran');
    const infoTransfer = document.getElementById('info-transfer');
    const infoCash = document.getElementById('info-cash');

    // Tampilkan info pembayaran berdasarkan metode
    if (metodePembayaran && infoTransfer && infoCash) {
        metodePembayaran.addEventListener('change', function() {
            if (this.value === 'transfer') {
                infoTransfer.style.display = 'block';
                infoCash.style.display = 'none';
            } else if (this.value === 'cash') {
                infoTransfer.style.display = 'none';
                infoCash.style.display = 'block';
            } else {
                infoTransfer.style.display = 'none';
                infoCash.style.display = 'none';
            }
        });

        // Trigger change event untuk set initial state
        metodePembayaran.dispatchEvent(new Event('change'));
    }

    // Validasi file upload
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const fileError = document.getElementById('fileError');
            const fileInfo = document.getElementById('fileInfo');

            if (fileError) fileError.style.display = 'none';
            if (fileInfo) fileInfo.style.display = 'none';

            if (file) {
                // Validasi ukuran file (2MB = 2097152 bytes)
                if (file.size > 2097152) {
                    showFileError('❌ Ukuran file terlalu besar! Maksimal 2MB.');
                    return;
                }

                // Validasi tipe file
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                if (!allowedTypes.includes(file.type)) {
                    showFileError('❌ Format file tidak didukung! Gunakan JPG, JPEG, PNG, atau PDF.');
                    return;
                }

                // Tampilkan info file yang valid
                showFileInfo(`✅ File valid: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`);
            }
        });
    }

    function showFileError(message) {
        let fileError = document.getElementById('fileError');
        if (!fileError) {
            fileError = document.createElement('div');
            fileError.id = 'fileError';
            fileError.className = 'text-danger small mt-1';
            fileInput.parentNode.appendChild(fileError);
        }
        fileError.textContent = message;
        fileError.style.display = 'block';
    }

    function showFileInfo(message) {
        let fileInfo = document.getElementById('fileInfo');
        if (!fileInfo) {
            fileInfo = document.createElement('div');
            fileInfo.id = 'fileInfo';
            fileInfo.className = 'text-success small mt-1';
            fileInput.parentNode.appendChild(fileInfo);
        }
        fileInfo.textContent = message;
        fileInfo.style.display = 'block';
    }

    // JavaScript untuk modal pilih jurusan
    let selectedJurusanId = null;
    let selectedGelombangId = null; // Simpan gelombang_id dari jurusan yang dipilih
    
    // Load daftar jurusan ketika modal dibuka
    $('#pilihJurusanModal').on('show.bs.modal', function() {
        loadDaftarJurusan();
    });

    // Reset selection ketika modal ditutup
    $('#pilihJurusanModal').on('hidden.bs.modal', function() {
        selectedJurusanId = null;
        selectedGelombangId = null;
        $('#simpanJurusanBtn').prop('disabled', true);
        $('.jurusan-card').removeClass('selected');
    });

    // Fungsi untuk memuat daftar jurusan
    function loadDaftarJurusan() {
        console.log('🔍 loadDaftarJurusan called');
        
        $('#daftarJurusan').html(`
            <div class="col-12 text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Memuat jurusan...</span>
                </div>
                <p class="mt-2 text-muted">Memuat daftar jurusan...</p>
            </div>
        `);

        $.ajax({
            url: '{{ route("siswa.get-jurusan") }}',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('✅ AJAX Success:', response);
                
                if (response.success && response.data) {
                    renderDaftarJurusan(response.data);
                } else {
                    showErrorModal(response.message || 'Gagal memuat daftar jurusan.');
                }
            },
            error: function(xhr, status, error) {
                console.log('❌ AJAX Error:', { status: xhr.status, statusText: xhr.statusText, error: error });
                showErrorModal('Terjadi kesalahan saat memuat jurusan. Status: ' + xhr.status);
            }
        });
    }

    function showErrorModal(message) {
        $('#daftarJurusan').html(`
            <div class="col-12 text-center py-4">
                <i class='bx bx-error bx-lg text-danger mb-3'></i>
                <p class="text-danger">${message}</p>
                <button class="btn btn-sm btn-outline-primary mt-2" onclick="loadDaftarJurusan()">
                    <i class='bx bx-refresh me-1'></i>Coba Lagi
                </button>
            </div>
        `);
    }

    // Fungsi untuk merender daftar jurusan
    function renderDaftarJurusan(jurusans) {
        console.log('🎨 Rendering jurusan:', jurusans);
        
        if (!jurusans || jurusans.length === 0) {
            $('#daftarJurusan').html(`
                <div class="col-12 text-center py-4">
                    <i class='bx bx-package bx-lg text-muted mb-3'></i>
                    <p class="text-muted">Belum ada jurusan yang tersedia.</p>
                </div>
            `);
            return;
        }

        let html = '';
        jurusans.forEach(jurusan => {
            const kuotaTersedia = jurusan.kuota_tersedia !== undefined ? jurusan.kuota_tersedia : (jurusan.kuota - jurusan.jumlah_pendaftar);
            const kuotaPersen = jurusan.kuota > 0 ? (jurusan.jumlah_pendaftar / jurusan.kuota) * 100 : 0;
            const isKuotaTersedia = kuotaTersedia > 0;
            
            // Tampilkan info gelombang jika ada
            const gelombangInfo = jurusan.gelombang_id ? `<small class="badge bg-info mt-1 mb-1">Gelombang ${jurusan.gelombang_id}</small>` : '';
            
            html += `
                <div class="col-md-6 mb-3">
                    <div class="card jurusan-card ${isKuotaTersedia ? '' : 'opacity-75'}" 
                         data-jurusan-id="${jurusan.id}"
                         data-gelombang-id="${jurusan.gelombang_id}"
                         style="cursor: ${isKuotaTersedia ? 'pointer' : 'not-allowed'}; border: ${isKuotaTersedia ? '2px solid transparent' : '2px solid #dee2e6'}; transition: all 0.3s ease;">
                        <div class="card-body text-center">
                            <div class="jurusan-icon text-${isKuotaTersedia ? 'primary' : 'secondary'} mb-3">
                                <i class='bx ${getJurusanIcon(jurusan.kode_jurusan)}' style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="card-title">${jurusan.nama_jurusan}</h5>
                            ${gelombangInfo}
                            <p class="card-text text-muted small">${jurusan.deskripsi || 'Tidak ada deskripsi'}</p>
                            
                            <div class="mt-3">
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar ${isKuotaTersedia ? 'bg-success' : 'bg-danger'}" 
                                         role="progressbar" 
                                         style="width: ${Math.min(kuotaPersen, 100)}%"
                                         aria-valuenow="${Math.min(kuotaPersen, 100)}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted jurusan-kuota">
                                    <span class="${isKuotaTersedia ? 'text-success fw-bold' : 'text-danger'}">
                                        ${isKuotaTersedia ? kuotaTersedia + ' tersedia' : 'Kuota penuh'}
                                    </span>
                                    / ${jurusan.kuota} kuota
                                </small>
                                <div class="mt-1">
                                    <small class="text-muted">
                                        ${jurusan.jumlah_pendaftar} pendaftar
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        $('#daftarJurusan').html(html);

        // Event listener untuk memilih jurusan
        $('.jurusan-card').on('click', function() {
            const jurusanId = $(this).data('jurusan-id');
            const gelombangId = $(this).data('gelombang-id');
            const card = $(this);
            
            // Cek apakah kuota masih tersedia
            const isKuotaTersedia = !card.hasClass('opacity-75');
            
            if (!isKuotaTersedia) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Kuota Penuh',
                    text: 'Maaf, kuota untuk jurusan ini sudah penuh. Silakan pilih jurusan lain.',
                    confirmButtonColor: '#0d6efd'
                });
                return;
            }

            // Hapus selection dari semua card
            $('.jurusan-card').removeClass('selected border-primary');
            
            // Tambahkan selection ke card yang dipilih
            card.addClass('selected border-primary');
            
            // Simpan ID jurusan dan gelombang yang dipilih
            selectedJurusanId = jurusanId;
            selectedGelombangId = gelombangId;
            
            // Aktifkan tombol simpan
            $('#simpanJurusanBtn').prop('disabled', false);
            
            console.log('🎯 Jurusan dipilih:', jurusanId, 'Gelombang:', gelombangId);
        });
    }

    // Fungsi untuk mendapatkan icon berdasarkan kode jurusan
    function getJurusanIcon(kodeJurusan) {
        const iconMap = {
            'TJKT': 'bx-network-chart',
            'PH': 'bx-building-house',
            'KUL': 'bx-bowl-hot',
            'default': 'bx-book'
        };
        return iconMap[kodeJurusan] || iconMap.default;
    }

    // Event listener untuk tombol simpan jurusan
    $('#simpanJurusanBtn').on('click', function() {
        if (!selectedJurusanId) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih jurusan terlebih dahulu.',
                confirmButtonColor: '#0d6efd'
            });
            return;
        }

        // Tampilkan loading
        const $btn = $(this);
        const originalText = $btn.html();
        $btn.html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');
        $btn.prop('disabled', true);

        // Kirim data ke server (termasuk gelombang_id dari jurusan yang dipilih)
        $.ajax({
            url: '{{ route("siswa.pilih-jurusan") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                jurusan_id: selectedJurusanId,
                gelombang_id: selectedGelombangId // Kirim gelombang_id dari jurusan yang dipilih
            },
            success: function(response) {
                console.log('Response pilih jurusan:', response);
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        confirmButtonColor: '#198754',
                        timer: 2000
                    }).then(() => {
                        $('#pilihJurusanModal').modal('hide');
                        location.reload(); // Reload halaman untuk update progress
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message,
                        confirmButtonColor: '#dc3545'
                    });
                    $btn.html(originalText);
                    $btn.prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error pilih jurusan:', error);
                let message = 'Terjadi kesalahan saat menyimpan pilihan jurusan.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: message,
                    confirmButtonColor: '#dc3545'
                });
                $btn.html(originalText);
                $btn.prop('disabled', false);
            }
        });
    });

    // Fungsi untuk download formulir sebagai PDF
    window.downloadFormulir = function() {
        Swal.fire({
            title: 'Download Formulir',
            text: 'Apakah Anda ingin mengunduh formulir sebagai PDF?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Download',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Menyiapkan PDF...',
                    text: 'Sedang memproses formulir Anda',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Kirim request untuk generate PDF
                setTimeout(() => {
                    // Simulasi download (ganti dengan endpoint yang sesuai)
                    const link = document.createElement('a');
                    link.href = '{{ route("siswa.download-formulir") }}';
                    link.target = '_blank';
                    link.click();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Formulir sedang didownload',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }, 1500);
            }
        });
    };

    // Debug info
    console.log('Route get-jurusan:', '{{ route("siswa.get-jurusan") }}');
    console.log('Route pilih-jurusan:', '{{ route("siswa.pilih-jurusan") }}');
});
</script>
@endpush