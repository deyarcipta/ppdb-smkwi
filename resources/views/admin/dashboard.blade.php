@extends('admin.layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
<div class="row">

  <!-- Statistik Utama -->
  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar flex-shrink-0 me-3 bg-label-primary">
            <i class="bx bx-user-plus bx-sm"></i>
          </div>
          <div>
            <h5 class="mb-0">{{ $totalPendaftar }}</h5>
            <small>Total Pendaftar</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar flex-shrink-0 me-3 bg-label-warning">
            <i class="bx bx-time-five bx-sm"></i>
          </div>
          <div>
            <h5 class="mb-0">{{ $pendingVerifikasi }}</h5>
            <small>Menunggu Verifikasi</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar flex-shrink-0 me-3 bg-label-success">
            <i class="bx bx-check-circle bx-sm"></i>
          </div>
          <div>
            <h5 class="mb-0">{{ $diterima }}</h5>
            <small>Diterima</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar flex-shrink-0 me-3 bg-label-danger">
            <i class="bx bx-x-circle bx-sm"></i>
          </div>
          <div>
            <h5 class="mb-0">{{ $ditolak }}</h5>
            <small>Ditolak</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Grafik dan Sidebar -->
  <div class="col-lg-8 col-md-12 mb-4">
    <!-- Grafik Pendaftar per Jurusan -->
    <div class="card">
      <div class="card-header">
        <h5 class="card-title m-0">Statistik Pendaftar per Jurusan</h5>
      </div>
      <div class="card-body" style="position: relative; height: 300px;">
        @if(count($jurusanLabels) > 0 && !empty($jurusanLabels[0]))
        <canvas id="chartPendaftar"></canvas>
        @else
        <div class="text-center py-4">
          <p class="text-muted">Belum ada data pendaftar per jurusan</p>
        </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-12 mb-4">
    <!-- Pembayaran Menunggu -->
    <div class="card">
      <div class="card-header">
        <h5 class="card-title m-0">Pembayaran Menunggu</h5>
      </div>
      <div class="card-body text-center">
        <div class="avatar avatar-lg bg-label-warning mb-3">
          <i class="bx bx-money bx-sm"></i>
        </div>
        <h4>{{ $pembayaranPending }}</h4>
        <p class="text-muted">Pembayaran perlu diverifikasi</p>
        <a href="{{ route('verifikasi-pembayaran.index') }}" class="btn btn-sm btn-outline-warning">Verifikasi Sekarang</a>
      </div>
    </div>
  </div>

  <!-- Bagian Bawah -->
  <div class="col-lg-6 col-md-12 mb-4">
    <!-- Pendaftar Terbaru -->
    <div class="card">
      <div class="card-header">
        <h5 class="card-title m-0">Pendaftar Terbaru</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>Nama</th>
                <th>Jurusan</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($pendaftarTerbaru as $siswa)
              <tr>
                <td class="py-2">
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2">
                      <span class="avatar-initial rounded-circle bg-label-primary">
                        {{ substr($siswa->nama_lengkap, 0, 1) }}
                      </span>
                    </div>
                    <div>
                      <div class="fw-semibold">{{ \Str::limit($siswa->nama_lengkap, 15) }}</div>
                    </div>
                  </div>
                </td>
                <td class="py-2">
                  <span class="badge bg-label-secondary">
                    {{ $siswa->jurusan ? $siswa->jurusan->nama_jurusan : 'Belum memilih' }}
                  </span>
                </td>
                <td class="py-2">
                  <span class="badge 
                    @if($siswa->status_pendaftar == 'diterima') bg-label-success
                    @elseif($siswa->status_pendaftar == 'ditolak') bg-label-danger
                    @elseif($siswa->status_pendaftar == 'pending') bg-label-warning
                    @else bg-label-secondary @endif">
                    {{ $siswa->status_pendaftar ? ucfirst($siswa->status_pendaftar) : 'Pending' }}
                  </span>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="3" class="text-center py-3">Tidak ada data pendaftar</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($pendaftarTerbaru->count() > 0)
        <div class="card-footer text-center">
          <a href="{{ route('verifikasi-pendaftar.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-6 col-md-12">
    <!-- Aktivitas Admin Terbaru -->
    <div class="card">
      <div class="card-header">
        <h5 class="card-title m-0">Aktivitas Admin Terbaru</h5>
      </div>
      <div class="card-body">
        <div class="timeline">
          <div class="timeline-item">
            <div class="timeline-marker bg-primary"></div>
            <div class="timeline-content">
              <h6 class="mb-1">Super Admin</h6>
              <p class="mb-1">Menambah data pendaftar baru</p>
              <small class="text-muted">22 Okt 2025, 10:15</small>
            </div>
          </div>
          <div class="timeline-item">
            <div class="timeline-marker bg-warning"></div>
            <div class="timeline-content">
              <h6 class="mb-1">Admin 1</h6>
              <p class="mb-1">Menghapus data tidak valid</p>
              <small class="text-muted">22 Okt 2025, 09:45</small>
            </div>
          </div>
          <div class="timeline-item">
            <div class="timeline-marker bg-info"></div>
            <div class="timeline-content">
              <h6 class="mb-1">Admin 2</h6>
              <p class="mb-1">Mengupdate status pendaftar</p>
              <small class="text-muted">21 Okt 2025, 16:20</small>
            </div>
          </div>
          <div class="timeline-item">
            <div class="timeline-marker bg-success"></div>
            <div class="timeline-content">
              <h6 class="mb-1">Super Admin</h6>
              <p class="mb-1">Memverifikasi pembayaran</p>
              <small class="text-muted">21 Okt 2025, 14:30</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('styles')
<style>
.timeline {
  position: relative;
  padding-left: 2rem;
}

.timeline-item {
  position: relative;
  margin-bottom: 1.5rem;
}

.timeline-marker {
  position: absolute;
  left: -2rem;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  top: 0.25rem;
}

.timeline-content {
  padding-bottom: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.timeline-item:last-child .timeline-content {
  border-bottom: none;
  padding-bottom: 0;
}

/* Fix chart container */
.card-body canvas {
  max-height: 250px !important;
  width: 100% !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    @if(count($jurusanLabels) > 0 && !empty($jurusanLabels[0]))
    const ctx = document.getElementById('chartPendaftar');
    
    // Pastikan data valid
    const labels = {!! json_encode($jurusanLabels) !!};
    const data = {!! json_encode($jurusanData) !!};
    
    // Filter data yang valid
    const validLabels = labels.filter(label => label && label !== 'Belum Ada Data');
    const validData = data.slice(0, validLabels.length);
    
    if (validLabels.length > 0 && validData.length > 0) {
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: validLabels,
          datasets: [{
            label: 'Jumlah Pendaftar',
            data: validData,
            backgroundColor: [
              '#696cff', '#71dd37', '#ff3e1d', '#ffb400', '#00b4ff',
              '#8592a3', '#ff6b6b', '#51cf66', '#fcc419', '#339af0'
            ],
            borderWidth: 0,
            borderRadius: 4,
            barPercentage: 0.6,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: { 
              beginAtZero: true,
              ticks: {
                stepSize: 1,
                precision: 0
              },
              grid: {
                color: 'rgba(0,0,0,0.1)',
                drawBorder: false
              }
            },
            x: {
              grid: {
                display: false
              },
              ticks: {
                maxRotation: 45,
                minRotation: 0
              }
            }
          },
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              backgroundColor: 'rgba(0,0,0,0.8)',
              titleFont: {
                size: 12
              },
              bodyFont: {
                size: 11
              }
            }
          }
        }
      });
    } else {
      // Hide canvas if no valid data
      ctx.style.display = 'none';
      ctx.parentElement.innerHTML = '<div class="text-center py-4"><p class="text-muted">Belum ada data pendaftar per jurusan</p></div>';
    }
    @endif
  });
</script>
@endpush