@extends('admin.layouts.app')
@section('title', 'Gelombang Pendaftaran')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Daftar Gelombang Pendaftaran</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addGelombangModal">
      <i class="bx bx-plus"></i> Tambah
    </button>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light text-center">
          <tr>
            <th>No</th>
            <th>Nama Gelombang</th>
            <th>Tahun Ajaran</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Selesai</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($data as $row)
          <tr class="text-center">
            <td>{{ $loop->iteration }}</td>
            <td>{{ $row->nama_gelombang }}</td>
            <td>{{ $row->tahunAjaran->nama ?? '-' }}</td>
            <td>{{ $row->tanggal_mulai ? \Carbon\Carbon::parse($row->tanggal_mulai)->format('d M Y') : '-' }}</td>
            <td>{{ $row->tanggal_selesai ? \Carbon\Carbon::parse($row->tanggal_selesai)->format('d M Y') : '-' }}</td>
            <td>
              @if($row->status == 'aktif')
                <span class="badge bg-success">Aktif</span>
              @else
                <span class="badge bg-secondary">Nonaktif</span>
              @endif
            </td>
            <td>
              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editGelombangModal{{ $row->id }}">
                <i class="bx bx-edit"></i>
              </button>

              <form action="{{ route('gelombang.destroy', $row->id) }}" method="POST" class="d-inline form-delete">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button>
              </form>

              @if($row->status == 'aktif')
                <a href="{{ route('gelombang.nonaktifkan', $row->id) }}" class="btn btn-secondary btn-sm" title="Nonaktifkan">
                  <i class="bx bx-block"></i>
                </a>
              @else
                <a href="{{ route('gelombang.aktifkan', $row->id) }}" class="btn btn-success btn-sm" title="Aktifkan">
                  <i class="bx bx-check"></i>
                </a>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center text-muted">Belum ada data gelombang</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
      <!-- Pagination Component -->
      <x-pagination :paginator="$data" />
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addGelombangModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('gelombang.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Gelombang Pendaftaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Nama Gelombang</label>
          <input type="text" name="nama_gelombang" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Tahun Ajaran</label>
          <select name="tahun_ajaran_id" class="form-select" required>
            <option value="">-- Pilih Tahun Ajaran --</option>
            @foreach($tahunAjaran as $ta)
              <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label>Tanggal Mulai</label>
          <input type="date" name="tanggal_mulai" class="form-control">
        </div>
        <div class="mb-3">
          <label>Tanggal Selesai</label>
          <input type="date" name="tanggal_selesai" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit (Diletakkan di luar tabel agar tidak transparan) -->
@foreach ($data as $row)
<div class="modal fade" id="editGelombangModal{{ $row->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('gelombang.update', $row->id) }}">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Gelombang Pendaftaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Nama Gelombang</label>
          <input type="text" name="nama_gelombang" value="{{ $row->nama_gelombang }}" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Tahun Ajaran</label>
          <select name="tahun_ajaran_id" class="form-select" required>
            <option value="">-- Pilih Tahun Ajaran --</option>
            @foreach($tahunAjaran as $ta)
              <option value="{{ $ta->id }}" {{ $row->tahun_ajaran_id == $ta->id ? 'selected' : '' }}>
                {{ $ta->nama }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label>Tanggal Mulai</label>
          <input type="date" name="tanggal_mulai" value="{{ $row->tanggal_mulai }}" class="form-control">
        </div>
        <div class="mb-3">
          <label>Tanggal Selesai</label>
          <input type="date" name="tanggal_selesai" value="{{ $row->tanggal_selesai }}" class="form-control">
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