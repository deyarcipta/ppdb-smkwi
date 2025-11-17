@extends('admin.layouts.app')
@section('title', 'Info Pembayaran')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Info Pembayaran</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addInfoPembayaranModal">
      <i class="bx bx-plus"></i> Tambah
    </button>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light text-center">
          <tr>
            <th>No</th>
            <th>Nama Bank</th>
            <th>Nomor Rekening</th>
            <th>Atas Nama</th>
            <th>Keterangan</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($data as $row)
          <tr class="text-center">
            <td>{{ $loop->iteration }}</td>
            <td>{{ $row->nama_bank }}</td>
            <td>{{ $row->nomor_rekening }}</td>
            <td>{{ $row->atas_nama }}</td>
            <td>{{ $row->keterangan ?? '-' }}</td>
            <td>
              @if($row->status)
                <span class="badge bg-success">Aktif</span>
              @else
                <span class="badge bg-secondary">Nonaktif</span>
              @endif
            </td>
            <td>
              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editInfoPembayaranModal{{ $row->id }}">
                <i class="bx bx-edit"></i>
              </button>

              <form action="{{ route('info-pembayaran.destroy', $row->id) }}" method="POST" class="d-inline form-delete">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button>
              </form>

              @if($row->status)
                <a href="{{ route('info-pembayaran.nonaktifkan', $row->id) }}" class="btn btn-secondary btn-sm" title="Nonaktifkan">
                  <i class="bx bx-block"></i>
                </a>
              @else
                <a href="{{ route('info-pembayaran.aktifkan', $row->id) }}" class="btn btn-success btn-sm" title="Aktifkan">
                  <i class="bx bx-check"></i>
                </a>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center text-muted">Belum ada data info pembayaran.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <!-- Pagination Component -->
    <x-pagination :paginator="$data" />
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addInfoPembayaranModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('info-pembayaran.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Info Pembayaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Nama Bank</label>
          <input type="text" name="nama_bank" class="form-control" placeholder="Contoh: BANK MANDIRI" required>
        </div>
        <div class="mb-3">
          <label>Nomor Rekening</label>
          <input type="text" name="nomor_rekening" class="form-control" placeholder="Contoh: 123-456-789-000" required>
        </div>
        <div class="mb-3">
          <label>Atas Nama</label>
          <input type="text" name="atas_nama" class="form-control" placeholder="Contoh: SEKOLAH ABC" required>
        </div>
        <div class="mb-3">
          <label>Keterangan</label>
          <textarea name="keterangan" class="form-control" placeholder="Contoh: Nama Siswa - No. Pendaftaran" rows="3"></textarea>
          <small class="text-muted">*Opsional</small>
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
<div class="modal fade" id="editInfoPembayaranModal{{ $row->id }}" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('info-pembayaran.update', $row->id) }}">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Info Pembayaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Nama Bank</label>
          <input type="text" name="nama_bank" value="{{ $row->nama_bank }}" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Nomor Rekening</label>
          <input type="text" name="nomor_rekening" value="{{ $row->nomor_rekening }}" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Atas Nama</label>
          <input type="text" name="atas_nama" value="{{ $row->atas_nama }}" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Keterangan</label>
          <textarea name="keterangan" class="form-control" rows="3">{{ $row->keterangan }}</textarea>
          <small class="text-muted">*Opsional</small>
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