@extends('admin.layouts.app')
@section('title', 'Template Pesan')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
    <div>
      <h5 class="mb-1"><i class="bx bx-message-detail me-2 text-primary"></i>Template Pesan</h5>
      <small class="text-muted">Kelola template naskah balasan dan notifikasi otomatis WhatsApp.</small>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-outline-info btn-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#modalPanduanVariabel">
        <i class="bx bx-help-circle me-1"></i>Panduan Variabel
      </button>
      <button class="btn btn-primary btn-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#addPesanModal">
        <i class="bx bx-plus me-1"></i>Tambah Template
      </button>
    </div>
  </div>


  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light text-center">
          <tr>
            <th>No</th>
            <th>Jenis Pesan</th>
            <th>Judul</th>
            <th>Isi Pesan</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($data as $row)
          <tr class="text-center">
            <td>{{ $loop->iteration }}</td>
            <td>{{ \App\Models\TemplatePesan::getLabel($row->jenis_pesan) }}</td>
            <td>{{ $row->judul }}</td>
            <td class="text-start">{{ Str::limit($row->isi_pesan, 80) }}</td>
            <td>
              @if($row->status)
                <span class="badge bg-success">Aktif</span>
              @else
                <span class="badge bg-secondary">Nonaktif</span>
              @endif
            </td>
            <td>
              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editPesanModal{{ $row->id }}">
                <i class="bx bx-edit"></i>
              </button>

              @if(auth()->user()->role === 'superadmin')
              <form action="{{ route('template-pesan.destroy', $row->id) }}" method="POST" class="d-inline form-delete">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button>
              </form>
              @endif

              @if($row->status)
                <a href="{{ route('template-pesan.nonaktifkan', $row->id) }}" class="btn btn-secondary btn-sm">
                  <i class="bx bx-block"></i>
                </a>
              @else
                <a href="{{ route('template-pesan.aktifkan', $row->id) }}" class="btn btn-success btn-sm">
                  <i class="bx bx-check"></i>
                </a>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="text-center text-muted">Belum ada template pesan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <!-- Pagination Component -->
      <x-pagination :paginator="$data" />
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addPesanModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('template-pesan.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Template Pesan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Jenis Pesan</label>
          <select name="jenis_pesan" class="form-select" required>
            <option value="">-- Pilih Jenis Pesan --</option>
            @foreach($jenisList as $key => $value)
              <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label>Judul</label>
          <input type="text" name="judul" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Isi Pesan</label>
          <textarea name="isi_pesan" class="form-control" rows="4" required></textarea>
          <small class="text-muted">Gunakan variabel seperti <code>{nama}</code>, <code>{no_pendaftaran}</code> untuk personalisasi pesan.</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit -->
@foreach ($data as $row)
<div class="modal fade" id="editPesanModal{{ $row->id }}" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('template-pesan.update', $row->id) }}">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Template Pesan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Jenis Pesan</label>
          <select name="jenis_pesan" class="form-select" required>
            @if(!array_key_exists($row->jenis_pesan, $jenisList))
              <option value="{{ $row->jenis_pesan }}" selected>{{ \App\Models\TemplatePesan::getLabel($row->jenis_pesan) }}</option>
            @endif
            @foreach($jenisList as $key => $value)
              <option value="{{ $key }}" {{ $key == $row->jenis_pesan ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label>Judul</label>
          <input type="text" name="judul" value="{{ $row->judul }}" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Isi Pesan</label>
          <textarea name="isi_pesan" class="form-control" rows="4" required>{{ $row->isi_pesan }}</textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endforeach

<!-- Modal Panduan Variabel -->
<div class="modal fade" id="modalPanduanVariabel" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title"><i class="bx bx-code-alt me-2 text-primary"></i>Panduan Variabel Personalisasi Pesan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <p class="text-muted small">Variabel di dalam kurung kurawal <code>{...}</code> akan otomatis diganti oleh sistem dengan data riil siswa saat pesan dikirimkan.</p>

        <h6 class="fw-bold text-primary mt-3"><i class="bx bx-user-plus me-1"></i> 1. Pesan Pendaftaran Baru (<code>pendaftaran_baru</code>)</h6>
        <div class="table-responsive mb-3">
          <table class="table table-sm table-bordered">
            <thead class="table-light"><tr><th width="35%">Variabel</th><th>Keterangan Data</th></tr></thead>
            <tbody>
              <tr><td><code>{nama}</code></td><td>Nama lengkap calon siswa</td></tr>
              <tr><td><code>{username}</code></td><td>Username login portal siswa (contoh: <code>PPDB2026001</code>)</td></tr>
              <tr><td><code>{password}</code></td><td>Password awal akun siswa (<code>password123</code>)</td></tr>
              <tr><td><code>{gelombang}</code></td><td>Nama gelombang pendaftaran yang dipilih</td></tr>
              <tr><td><code>{tahun_ajaran}</code></td><td>Tahun ajaran aktif (contoh: <code>2026/2027</code>)</td></tr>
              <tr><td><code>{rekening}</code></td><td>Nomor rekening pembayaran sekolah</td></tr>
              <tr><td><code>{an}</code></td><td>Atas nama pemilik rekening</td></tr>
              <tr><td><code>{no_admin}</code></td><td>Nomor kontak panitia/admin</td></tr>
              <tr><td><code>{url_sistem}</code></td><td>Tautan URL portal login siswa</td></tr>
            </tbody>
          </table>
        </div>

        <h6 class="fw-bold text-success mt-3"><i class="bx bx-receipt me-1"></i> 2. Pesan Verifikasi Formulir (<code>verifikasi_formulir</code>)</h6>
        <div class="table-responsive mb-3">
          <table class="table table-sm table-bordered">
            <thead class="table-light"><tr><th width="35%">Variabel</th><th>Keterangan Data</th></tr></thead>
            <tbody>
              <tr><td><code>{nama}</code> / <code>{nama_lengkap}</code></td><td>Nama lengkap siswa</td></tr>
              <tr><td><code>{no_pendaftaran}</code></td><td>Nomor pendaftaran resmi siswa</td></tr>
              <tr><td><code>{jurusan}</code></td><td>Nama kompetensi keahlian / jurusan</td></tr>
              <tr><td><code>{jumlah}</code></td><td>Nominal uang formulir yang diverifikasi (Rp)</td></tr>
              <tr><td><code>{status_judul}</code></td><td>Status judul ("TELAH DIVERIFIKASI")</td></tr>
              <tr><td><code>{status_badge}</code></td><td>Ikon checklist hijau (✅)</td></tr>
              <tr><td><code>{status_detail}</code></td><td>Rincian status ("LUNAS & TERVERIFIKASI")</td></tr>
              <tr><td><code>{tahap_selanjutnya}</code></td><td>Instruksi tahap selanjutnya (lengkapi berkas/pembayaran)</td></tr>
              <tr><td><code>{no_admin}</code></td><td>Nomor kontak panitia/admin</td></tr>
              <tr><td><code>{url_sistem}</code></td><td>Tautan login portal siswa</td></tr>
            </tbody>
          </table>
        </div>

        <h6 class="fw-bold text-warning mt-3"><i class="bx bx-credit-card me-1"></i> 3. Pesan Verifikasi Pembayaran PPDB (<code>verifikasi_ppdb</code>)</h6>
        <div class="table-responsive mb-3">
          <table class="table table-sm table-bordered">
            <thead class="table-light"><tr><th width="35%">Variabel</th><th>Keterangan Data</th></tr></thead>
            <tbody>
              <tr><td><code>{nama}</code> / <code>{nama_lengkap}</code></td><td>Nama lengkap siswa</td></tr>
              <tr><td><code>{no_pendaftaran}</code></td><td>Nomor pendaftaran resmi siswa</td></tr>
              <tr><td><code>{jurusan}</code></td><td>Nama jurusan yang dipilih</td></tr>
              <tr><td><code>{jumlah}</code></td><td>Nominal transaksi yang baru diverifikasi (Rp)</td></tr>
              <tr><td><code>{total_biaya}</code></td><td>Total seluruh tagihan biaya PPDB gelombang tersebut</td></tr>
              <tr><td><code>{total_dibayar}</code></td><td>Total akumulasi pembayaran yang sudah diverifikasi</td></tr>
              <tr><td><code>{sisa_pembayaran}</code></td><td>Sisa kekurangan tagihan yang belum dibayar</td></tr>
              <tr><td><code>{status_detail}</code></td><td>"LUNAS & TERVERIFIKASI" atau "TERVERIFIKASI (Belum Lunas)"</td></tr>
              <tr><td><code>{tahap_selanjutnya}</code></td><td>Instruksi lanjutan sesuai status lunas/belum</td></tr>
              <tr><td><code>{no_admin}</code></td><td>Nomor kontak panitia/admin</td></tr>
              <tr><td><code>{url_sistem}</code></td><td>Tautan login portal siswa</td></tr>
            </tbody>
          </table>
        </div>

        <h6 class="fw-bold text-info mt-3"><i class="bx bx-check-circle me-1"></i> 4. Pesan Pendaftar Diterima (<code>pendaftar_diterima</code>)</h6>
        <div class="table-responsive mb-3">
          <table class="table table-sm table-bordered">
            <thead class="table-light"><tr><th width="35%">Variabel</th><th>Keterangan Data</th></tr></thead>
            <tbody>
              <tr><td><code>{nama}</code></td><td>Nama panggilan siswa</td></tr>
              <tr><td><code>{nama_lengkap}</code></td><td>Nama lengkap siswa</td></tr>
              <tr><td><code>{no_pendaftaran}</code></td><td>Nomor pendaftaran resmi siswa</td></tr>
              <tr><td><code>{jurusan}</code></td><td>Nama kompetensi keahlian yang diterima</td></tr>
              <tr><td><code>{gelombang}</code></td><td>Nama gelombang pendaftaran</td></tr>
              <tr><td><code>{tahun_ajaran}</code></td><td>Tahun ajaran (contoh: <code>2026/2027</code>)</td></tr>
              <tr><td><code>{batas_daftar_ulang}</code></td><td>Jadwal batas akhir daftar ulang</td></tr>
              <tr><td><code>{tanggal_mpls}</code></td><td>Tanggal pelaksanaan kegiatan MPLS</td></tr>
              <tr><td><code>{tanggal_awal_semester}</code></td><td>Tanggal masuk sekolah hari pertama</td></tr>
              <tr><td><code>{sekolah}</code></td><td>Nama resmi sekolah</td></tr>
              <tr><td><code>{alamat_sekolah}</code></td><td>Alamat lengkap sekolah</td></tr>
              <tr><td><code>{telepon_sekolah}</code></td><td>Nomor telepon sekolah</td></tr>
              <tr><td><code>{email_sekolah}</code></td><td>Email resmi sekolah</td></tr>
              <tr><td><code>{no_admin}</code></td><td>Nomor WhatsApp panitia/admin</td></tr>
              <tr><td><code>{website}</code></td><td>Website utama sekolah</td></tr>
              <tr><td><code>{url_sistem}</code></td><td>Tautan portal pengumuman siswa</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.form-delete').forEach(form => {
  form.addEventListener('submit', e => {
    e.preventDefault();
    Swal.fire({
      title: 'Yakin ingin menghapus?',
      text: 'Data ini tidak dapat dikembalikan!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal'
    }).then(result => {
      if (result.isConfirmed) form.submit();
    });
  });
});

@if(session('success'))
Swal.fire({
  icon: 'success',
  title: 'Berhasil!',
  text: '{{ session('success') }}',
  timer: 1500,
  showConfirmButton: false
});
@endif
</script>
@endpush
