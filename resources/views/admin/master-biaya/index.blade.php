@extends('admin.layouts.app')
@section('title', 'Master Biaya')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Master Biaya</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addBiayaModal">
      <i class="bx bx-plus"></i> Tambah
    </button>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light text-center">
          <tr>
            <th>No</th>
            <th>Gelombang</th>
            <th>Jenis Biaya</th>
            <th>Nama Biaya</th>
            <th>Total Biaya</th>
            <th>Diskon (%)</th>
            <th>Total Setelah Diskon</th>
            <th>Keterangan</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($data as $row)
          <tr class="text-center">
            <td>{{ $loop->iteration }}</td>
            <td>{{ $row->gelombang->nama_gelombang ?? '-' }}</td>
            <td>
              <span class="badge bg-info text-dark">
                {{ ucfirst($row->jenis_biaya) }}
              </span>
            </td>
            <td>{{ $row->nama_biaya }}</td>
            <td>Rp {{ number_format($row->total_biaya, 0, ',', '.') }}</td>
            <td>{{ $row->diskon ?? 0 }}%</td>
            <td>Rp {{ number_format($row->total_biaya - ($row->total_biaya * ($row->diskon / 100)), 0, ',', '.') }}</td>
            <td class="text-start">{{ $row->keterangan ?? '-' }}</td>
            <td>
              @if($row->status)
                <span class="badge bg-success">Aktif</span>
              @else
                <span class="badge bg-secondary">Nonaktif</span>
              @endif
            </td>
            <td>
              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editBiayaModal{{ $row->id }}">
                <i class="bx bx-edit"></i>
              </button>

              <form action="{{ route('master-biaya.destroy', $row->id) }}" method="POST" class="d-inline form-delete">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button>
              </form>

              @if($row->status)
                <a href="{{ route('master-biaya.nonaktifkan', $row->id) }}" class="btn btn-secondary btn-sm" title="Nonaktifkan">
                  <i class="bx bx-block"></i>
                </a>
              @else
                <a href="{{ route('master-biaya.aktifkan', $row->id) }}" class="btn btn-success btn-sm" title="Aktifkan">
                  <i class="bx bx-check"></i>
                </a>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="10" class="text-center text-muted">Belum ada data biaya.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <!-- Pagination Component -->
      <x-pagination :paginator="$data" />
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addBiayaModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('master-biaya.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Master Biaya</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
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
          <label>Jenis Biaya</label>
          <select name="jenis_biaya" class="form-select" required>
            <option value="">-- Pilih Jenis Biaya --</option>
            <option value="formulir">Formulir</option>
            <option value="ppdb">PPDB</option>
          </select>
        </div>

        <div class="mb-3">
          <label>Nama Biaya</label>
          <input type="text" name="nama_biaya" class="form-control" placeholder="Contoh: Biaya PPDB" required>
        </div>

        <div class="mb-3">
          <label>Total Biaya</label>
          <input type="number" name="total_biaya" class="form-control" placeholder="Masukkan total biaya" required>
        </div>

        <div class="mb-3">
          <label>Diskon (%)</label>
          <input type="number" name="diskon" class="form-control" placeholder="Opsional">
        </div>

        <div class="mb-3">
          <label>Keterangan</label>
          <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Biaya mencakup seragam, MPLS, gedung, dll"></textarea>
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
<div class="modal fade" id="editBiayaModal{{ $row->id }}" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('master-biaya.update', $row->id) }}">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Master Biaya</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
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
          <label>Jenis Biaya</label>
          <select name="jenis_biaya" class="form-select" required>
            <option value="formulir" {{ $row->jenis_biaya == 'formulir' ? 'selected' : '' }}>Formulir</option>
            <option value="ppdb" {{ $row->jenis_biaya == 'ppdb' ? 'selected' : '' }}>PPDB</option>
          </select>
        </div>

        <div class="mb-3">
          <label>Nama Biaya</label>
          <input type="text" name="nama_biaya" value="{{ $row->nama_biaya }}" class="form-control" required>
        </div>

        <div class="mb-3">
          <label>Total Biaya</label>
          <input type="number" name="total_biaya" value="{{ $row->total_biaya }}" class="form-control" required>
        </div>

        <div class="mb-3">
          <label>Diskon (%)</label>
          <input type="number" name="diskon" value="{{ $row->diskon }}" class="form-control">
        </div>

        <div class="mb-3">
          <label>Keterangan</label>
          <textarea name="keterangan" class="form-control" rows="3">{{ $row->keterangan }}</textarea>
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
