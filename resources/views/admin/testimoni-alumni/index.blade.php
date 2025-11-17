@extends('admin.layouts.app')
@section('title', 'Testimoni Alumni')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Testimoni Alumni</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTestimoniModal">
      <i class="bx bx-plus"></i> Tambah
    </button>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light text-center">
          <tr>
            <th>No</th>
            <th>Foto</th>
            <th>Headline</th>
            <th>Nama Alumni</th>
            <th>Jurusan</th>
            <th>Pekerjaan</th>
            <th>Testimoni</th>
            <th>Urutan</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($data as $row)
          <tr class="text-center">
            <td>{{ $loop->iteration }}</td>
            <td>
              @if($row->foto)
                <img src="{{ Storage::url('public/testimoni-alumni/' . $row->foto) }}" 
                     alt="{{ $row->nama_alumni }}" 
                     class="rounded-circle" 
                     width="50" 
                     height="50" 
                     style="object-fit: cover;">
              @else
                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" 
                     style="width: 50px; height: 50px;">
                  <i class="bx bx-user text-white"></i>
                </div>
              @endif
            </td>
            <td class="text-start">{{ $row->headline }}</td>
            <td class="text-start">{{ $row->nama_alumni }}</td>
            <td>{{ $row->jurusan }}</td>
            <td>{{ $row->pekerjaan ?? '-' }}</td>
            <td class="text-start">
              <div class="text-truncate" style="max-width: 200px;" title="{{ $row->testimoni }}">
                {{ Str::limit($row->testimoni, 50) }}
              </div>
            </td>
            <td>{{ $row->urutan }}</td>
            <td>
              @if($row->status)
                <span class="badge bg-success">Aktif</span>
              @else
                <span class="badge bg-secondary">Nonaktif</span>
              @endif
            </td>
            <td>
              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editTestimoniModal{{ $row->id }}">
                <i class="bx bx-edit"></i>
              </button>

              <form action="{{ route('testimoni-alumni.destroy', $row->id) }}" method="POST" class="d-inline form-delete">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button>
              </form>

              @if($row->status)
                <a href="{{ route('testimoni-alumni.nonaktifkan', $row->id) }}" class="btn btn-secondary btn-sm" title="Nonaktifkan">
                  <i class="bx bx-block"></i>
                </a>
              @else
                <a href="{{ route('testimoni-alumni.aktifkan', $row->id) }}" class="btn btn-success btn-sm" title="Aktifkan">
                  <i class="bx bx-check"></i>
                </a>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="10" class="text-center text-muted">Belum ada data testimoni alumni.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <!-- Pagination Component -->
    <x-pagination :paginator="$data" />
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addTestimoniModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('testimoni-alumni.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Testimoni Alumni</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label>Headline</label>
              <input type="text" name="headline" class="form-control" placeholder="Contoh: Pengalaman Magang yang Berharga" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label>Nama Alumni</label>
              <input type="text" name="nama_alumni" class="form-control" placeholder="Contoh: Ilham Muhammad Alamsyah" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label>Jurusan</label>
              <input type="text" name="jurusan" class="form-control" placeholder="Contoh: Jurusan Perhotelan" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label>Pekerjaan</label>
              <input type="text" name="pekerjaan" class="form-control" placeholder="Contoh: Hotel Manager di Grand Hyatt">
              <small class="text-muted">*Opsional</small>
            </div>
          </div>
        </div>
        <div class="mb-3">
          <label>Testimoni</label>
          <textarea name="testimoni" class="form-control" rows="5" placeholder="Masukkan testimoni alumni..." required></textarea>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label>Foto</label>
              <input type="file" name="foto" class="form-control" accept="image/*">
              <small class="text-muted">Format: jpeg, png, jpg, gif (max: 2MB)</small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label>Urutan</label>
              <input type="number" name="urutan" class="form-control" value="1" min="1" required>
              <small class="text-muted">Urutan tampil (angka kecil akan tampil pertama)</small>
            </div>
          </div>
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
<div class="modal fade" id="editTestimoniModal{{ $row->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('testimoni-alumni.update', $row->id) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Testimoni Alumni</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label>Headline</label>
              <input type="text" name="headline" value="{{ $row->headline }}" class="form-control" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label>Nama Alumni</label>
              <input type="text" name="nama_alumni" value="{{ $row->nama_alumni }}" class="form-control" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label>Jurusan</label>
              <input type="text" name="jurusan" value="{{ $row->jurusan }}" class="form-control" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label>Pekerjaan</label>
              <input type="text" name="pekerjaan" value="{{ $row->pekerjaan }}" class="form-control" placeholder="Contoh: Hotel Manager di Grand Hyatt">
              <small class="text-muted">*Opsional</small>
            </div>
          </div>
        </div>
        <div class="mb-3">
          <label>Testimoni</label>
          <textarea name="testimoni" class="form-control" rows="5" required>{{ $row->testimoni }}</textarea>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label>Foto</label>
              @if($row->foto)
                <div class="mb-2">
                  <img src="{{ Storage::url('public/testimoni-alumni/' . $row->foto) }}" 
                       alt="{{ $row->nama_alumni }}" 
                       class="rounded" 
                       width="80" 
                       height="80" 
                       style="object-fit: cover;">
                </div>
              @endif
              <input type="file" name="foto" class="form-control" accept="image/*">
              <small class="text-muted">Kosongkan jika tidak ingin mengubah foto</small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label>Urutan</label>
              <input type="number" name="urutan" value="{{ $row->urutan }}" class="form-control" min="1" required>
            </div>
          </div>
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