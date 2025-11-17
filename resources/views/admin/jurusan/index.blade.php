@extends('admin.layouts.app')
@section('title', 'Data Jurusan')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Daftar Jurusan</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addJurusanModal">
      <i class="bx bx-plus"></i> Tambah
    </button>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr class="text-center">
            <th width="5%">No</th>
            <th width="10%">Kode</th>
            <th>Nama Jurusan</th>
            <th>Deskripsi</th>
            <th width="10%">Status</th>
            <th width="25%">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($jurusans as $jurusan)
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td>{{ $jurusan->kode_jurusan }}</td>
              <td>{{ $jurusan->nama_jurusan }}</td>
              <td>{{ $jurusan->deskripsi ?? '-' }}</td>
              <td class="text-center">
                @if($jurusan->status)
                  <span class="badge bg-success">Aktif</span>
                @else
                  <span class="badge bg-secondary">Nonaktif</span>
                @endif
              </td>
              <td class="text-center">
                <!-- Tombol Edit -->
                <button class="btn btn-warning btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#editJurusanModal{{ $jurusan->id }}">
                  <i class="bx bx-edit"></i>
                </button>

                <!-- Tombol Hapus -->
                <form action="{{ route('jurusan.destroy', $jurusan->id) }}" method="POST" class="d-inline form-delete">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">
                    <i class="bx bx-trash"></i>
                  </button>
                </form>

                <!-- Tombol Aktif / Nonaktif -->
                @if($jurusan->status)
                  <a href="{{ route('jurusan.nonaktifkan', $jurusan->id) }}" class="btn btn-secondary btn-sm" title="Nonaktifkan">
                    <i class="bx bx-block"></i>
                  </a>
                @else
                  <a href="{{ route('jurusan.aktifkan', $jurusan->id) }}" class="btn btn-success btn-sm" title="Aktifkan">
                    <i class="bx bx-check"></i>
                  </a>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted">Belum ada data jurusan</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <!-- Pagination Component -->
      <x-pagination :paginator="$jurusans" />
  </div>
</div>

<!-- Modal Tambah Jurusan -->
<div class="modal fade" id="addJurusanModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('jurusan.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Jurusan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Kode Jurusan</label>
          <input type="text" name="kode_jurusan" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Nama Jurusan</label>
          <input type="text" name="nama_jurusan" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Deskripsi</label>
          <textarea name="deskripsi" class="form-control"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Jurusan -->
@foreach ($jurusans as $jurusan)
  <div class="modal fade" id="editJurusanModal{{ $jurusan->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form class="modal-content" method="POST" action="{{ route('jurusan.update', $jurusan->id) }}">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Edit Jurusan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Kode Jurusan</label>
            <input type="text" name="kode_jurusan" class="form-control" value="{{ $jurusan->kode_jurusan }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Nama Jurusan</label>
            <input type="text" name="nama_jurusan" class="form-control" value="{{ $jurusan->nama_jurusan }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control">{{ $jurusan->deskripsi }}</textarea>
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
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  // Konfirmasi hapus
  document.querySelectorAll('.form-delete').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: 'Data ini tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) form.submit();
      });
    });
  });

  // Notifikasi sukses/error
  @if(session('success'))
    Swal.fire({
      icon: 'success',
      title: 'Berhasil',
      text: '{{ session('success') }}',
      timer: 1500,
      showConfirmButton: false
    });
  @endif

  @if(session('error'))
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: '{{ session('error') }}',
    });
  @endif
</script>

<!-- Responsif tabel untuk HP -->
<style>
  @media (max-width: 576px) {
    .table thead {
      font-size: 0.85rem;
    }
    .table td, .table th {
      padding: 0.5rem;
    }
    .table-responsive {
      border-radius: 0.5rem;
      overflow-x: auto;
    }
  }
</style>
@endpush
