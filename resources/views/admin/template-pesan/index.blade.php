@extends('admin.layouts.app')
@section('title', 'Template Pesan')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Template Pesan</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPesanModal">
      <i class="bx bx-plus"></i> Tambah
    </button>
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
            <td>{{ \App\Models\TemplatePesan::jenisList()[$row->jenis_pesan] ?? '-' }}</td>
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

              <form action="{{ route('template-pesan.destroy', $row->id) }}" method="POST" class="d-inline form-delete">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button>
              </form>

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
