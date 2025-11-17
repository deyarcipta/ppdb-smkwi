@extends('admin.layouts.app')
@section('title', 'Kuota Per Jurusan')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Kuota Per Jurusan</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addKuotaModal">
      <i class="bx bx-plus"></i> Tambah
    </button>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light text-center">
          <tr>
            <th>No</th>
            <th>Jurusan</th>
            <th>Gelombang</th>
            <th>Kuota</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($data as $row)
          <tr class="text-center">
            <td>{{ $loop->iteration }}</td>
            <td>{{ $row->jurusan->nama_jurusan ?? '-' }}</td>
            <td>{{ $row->gelombang->nama_gelombang ?? '-' }}</td>
            <td>{{ $row->kuota }}</td>
            <td>
              @if($row->status)
                <span class="badge bg-success">Aktif</span>
              @else
                <span class="badge bg-secondary">Nonaktif</span>
              @endif
            </td>
            <td>
              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editKuotaModal{{ $row->id }}">
                <i class="bx bx-edit"></i>
              </button>

              <form action="{{ route('kuota-jurusan.destroy', $row->id) }}" method="POST" class="d-inline form-delete">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button>
              </form>

              @if($row->status)
                <a href="{{ route('kuota-jurusan.nonaktifkan', $row->id) }}" class="btn btn-secondary btn-sm" title="Nonaktifkan">
                  <i class="bx bx-block"></i>
                </a>
              @else
                <a href="{{ route('kuota-jurusan.aktifkan', $row->id) }}" class="btn btn-success btn-sm" title="Aktifkan">
                  <i class="bx bx-check"></i>
                </a>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="text-center text-muted">Belum ada data kuota.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <!-- Pagination Component -->
      <x-pagination :paginator="$data" />
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addKuotaModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('kuota-jurusan.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Kuota Per Jurusan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Jurusan</label>
          <select name="jurusan_id" class="form-select" required>
            <option value="">-- Pilih Jurusan --</option>
            @foreach($jurusans as $jurusan)
              <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label>Gelombang</label>
          <select name="gelombang_id" class="form-select" required>
            <option value="">-- Pilih Gelombang --</option>
            @foreach($gelombangs as $gelombang)
              <option value="{{ $gelombang->id }}">{{ $gelombang->nama_gelombang }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label>Kuota</label>
          <input type="number" name="kuota" class="form-control" required>
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
<div class="modal fade" id="editKuotaModal{{ $row->id }}" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('kuota-jurusan.update', $row->id) }}">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Kuota Per Jurusan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Jurusan</label>
          <select name="jurusan_id" class="form-select" required>
            @foreach($jurusans as $jurusan)
              <option value="{{ $jurusan->id }}" {{ $jurusan->id == $row->jurusan_id ? 'selected' : '' }}>
                {{ $jurusan->nama_jurusan }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label>Gelombang</label>
          <select name="gelombang_id" class="form-select" required>
            @foreach($gelombangs as $gelombang)
              <option value="{{ $gelombang->id }}" {{ $gelombang->id == $row->gelombang_id ? 'selected' : '' }}>
                {{ $gelombang->nama_gelombang }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label>Kuota</label>
          <input type="number" name="kuota" value="{{ $row->kuota }}" class="form-control" required>
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
