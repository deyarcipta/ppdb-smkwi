@extends('admin.layouts.app')
@section('title', 'Persyaratan & Alur Pendaftaran')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Persyaratan & Alur Pendaftaran</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPersyaratanModal">
      <i class="bx bx-plus"></i> Tambah Data
    </button>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light text-center">
          <tr>
            <th width="5%">No</th>
            <th>Judul</th>
            <th width="18%">Tipe</th>
            <th width="8%">Urutan</th>
            <th width="10%">Status</th>
            <th width="20%">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($data as $row)
          <tr class="text-center">
            <td>{{ $loop->iteration }}</td>
            <td class="text-start">
              <strong>{{ $row->judul }}</strong>
              @if($row->sub_konten)
                <div class="small text-muted mt-1"><i class="bx bx-list-check me-1"></i>Memiliki detail poin</div>
              @endif
            </td>
            <td>
              <span class="badge 
                @if($row->tipe == 'jadwal') bg-primary
                @elseif($row->tipe == 'umum') bg-success
                @elseif($row->tipe == 'alur') bg-warning text-dark
                @else bg-info @endif">
                @if($row->tipe == 'alur') Alur Pendaftaran @else {{ ucfirst($row->tipe) }} @endif
              </span>
            </td>
            <td class="fw-bold">{{ $row->urutan }}</td>
            <td>
              @if($row->status)
                <span class="badge bg-success">Aktif</span>
              @else
                <span class="badge bg-secondary">Nonaktif</span>
              @endif
            </td>
            <td>
              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editPersyaratanModal{{ $row->id }}">
                <i class="bx bx-edit"></i>
              </button>

              <form action="{{ route('persyaratan-pendaftaran.destroy', $row->id) }}" method="POST" class="d-inline form-delete">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button>
              </form>

              @if($row->status)
                <a href="{{ route('persyaratan-pendaftaran.nonaktifkan', $row->id) }}" class="btn btn-secondary btn-sm" title="Nonaktifkan">
                  <i class="bx bx-block"></i>
                </a>
              @else
                <a href="{{ route('persyaratan-pendaftaran.aktifkan', $row->id) }}" class="btn btn-success btn-sm" title="Aktifkan">
                  <i class="bx bx-check"></i>
                </a>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="text-center text-muted">Belum ada data persyaratan atau alur pendaftaran.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <!-- Pagination Component -->
    <x-pagination :paginator="$data" />
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addPersyaratanModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('persyaratan-pendaftaran.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Persyaratan / Alur Pendaftaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Judul Langkah / Persyaratan</label>
          <input type="text" name="judul" class="form-control" placeholder="Contoh: Registrasi Awal / Persyaratan Umum" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Tipe</label>
          <select name="tipe" class="form-select" required>
            <option value="">-- Pilih Tipe --</option>
            <option value="alur">Alur Pendaftaran (Cara Mendaftar)</option>
            <option value="jadwal">Jadwal Pendaftaran</option>
            <option value="umum">Persyaratan Umum</option>
            <option value="dokumen">Dokumen yang Diperlukan</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Urutan</label>
          <input type="number" name="urutan" class="form-control" value="1" min="1" required>
          <small class="text-muted">Urutan langkah (angka kecil akan tampil pertama)</small>
        </div>
        <div class="mb-3">
          <label class="form-label">Konten / Deskripsi Utama</label>
          <textarea name="konten" class="form-control" rows="4" placeholder="Masukkan ringkasan/deskripsi langkah..." required></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Sub Konten / Rincian Poin-poin (Opsional)</label>
          <textarea name="sub_konten" class="form-control" rows="4" placeholder="Pisahkan tiap rincian poin dengan menekan Enter / Baris Baru..."></textarea>
          <small class="text-muted">Setiap baris baru akan ditampilkan sebagai poin list di halaman website.</small>
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
<div class="modal fade" id="editPersyaratanModal{{ $row->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('persyaratan-pendaftaran.update', $row->id) }}">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Persyaratan / Alur Pendaftaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Judul Langkah / Persyaratan</label>
          <input type="text" name="judul" value="{{ $row->judul }}" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Tipe</label>
          <select name="tipe" class="form-select" required>
            <option value="alur" {{ $row->tipe == 'alur' ? 'selected' : '' }}>Alur Pendaftaran (Cara Mendaftar)</option>
            <option value="jadwal" {{ $row->tipe == 'jadwal' ? 'selected' : '' }}>Jadwal Pendaftaran</option>
            <option value="umum" {{ $row->tipe == 'umum' ? 'selected' : '' }}>Persyaratan Umum</option>
            <option value="dokumen" {{ $row->tipe == 'dokumen' ? 'selected' : '' }}>Dokumen yang Diperlukan</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Urutan</label>
          <input type="number" name="urutan" value="{{ $row->urutan }}" class="form-control" min="1" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Konten / Deskripsi Utama</label>
          <textarea name="konten" class="form-control" rows="4" required>{{ $row->konten }}</textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Sub Konten / Rincian Poin-poin (Opsional)</label>
          <textarea name="sub_konten" class="form-control" rows="4">{{ $row->sub_konten }}</textarea>
          <small class="text-muted">Setiap baris baru akan ditampilkan sebagai poin list di halaman website.</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>
@endforeach

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // Konfirmasi Hapus
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

  // Notifikasi Swal
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