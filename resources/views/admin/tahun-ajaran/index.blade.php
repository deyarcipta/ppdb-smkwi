@extends('admin.layouts.app')
@section('title', 'Data Tahun Ajaran')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Daftar Tahun Ajaran</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTahunAjaranModal">
      <i class="bx bx-plus"></i> Tambah
    </button>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr class="text-center">
            <th width="5%">No</th>
            <th>Nama Tahun Ajaran</th>
            <th>Status</th>
            <th width="25%">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($data as $row)
            <tr>
              <td class="text-center">{{ $loop->iteration }}</td>
              <td>{{ $row->nama }}</td>
              <td class="text-center">
                @if($row->status == 'aktif')
                  <span class="badge bg-success">Aktif</span>
                @else
                  <span class="badge bg-secondary">Nonaktif</span>
                @endif
              </td>
              <td class="text-center">
                <!-- Tombol Edit -->
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editTahunAjaranModal{{ $row->id }}">
                  <i class="bx bx-edit"></i>
                </button>

                <!-- Tombol Hapus -->
                <form action="{{ route('tahun-ajaran.destroy', $row->id) }}" method="POST" class="d-inline form-delete">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">
                    <i class="bx bx-trash"></i>
                  </button>
                </form>

                <!-- Tombol Aktif / Nonaktif -->
                @if($row->status == 'aktif')
                  <a href="{{ route('tahun-ajaran.nonaktifkan', $row->id) }}" class="btn btn-secondary btn-sm" title="Nonaktifkan">
                    <i class="bx bx-block"></i>
                  </a>
                @else
                  <a href="{{ route('tahun-ajaran.aktifkan', $row->id) }}" class="btn btn-success btn-sm" title="Aktifkan">
                    <i class="bx bx-check"></i>
                  </a>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-muted">Belum ada data tahun ajaran</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <!-- Pagination Component -->
      <x-pagination :paginator="$data" />
  </div>
</div>

<!-- Modal Tambah Tahun Ajaran -->
<div class="modal fade" id="addTahunAjaranModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('tahun-ajaran.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Tahun Ajaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nama Tahun Ajaran</label>
          <input type="text" name="nama" class="form-control" placeholder="Contoh: 2025/2026" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Tahun Ajaran -->
@foreach ($data as $row)
  <div class="modal fade" id="editTahunAjaranModal{{ $row->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form class="modal-content" method="POST" action="{{ route('tahun-ajaran.update', $row->id) }}">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Edit Tahun Ajaran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Tahun Ajaran</label>
            <input type="text" name="nama" class="form-control" value="{{ $row->nama }}" required>
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
@endpush
