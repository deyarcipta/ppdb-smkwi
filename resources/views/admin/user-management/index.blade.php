@extends('admin.layouts.app')
@section('title', 'Management User')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Management User</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
      <i class="bx bx-plus"></i> Tambah User
    </button>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light text-center">
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th>Tanggal Dibuat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($data as $row)
          <tr class="text-center">
            <td>{{ $loop->iteration }}</td>
            <td class="text-start">
              <div class="d-flex align-items-center">
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                     style="width: 40px; height: 40px;">
                  <i class="bx bx-user text-white"></i>
                </div>
                <div>
                  {{ $row->name }}
                  @if($row->id == auth()->id())
                    <span class="badge bg-info">Anda</span>
                  @endif
                </div>
              </div>
            </td>
            <td class="text-start">{{ $row->email }}</td>
            <td>
              @if($row->role === 'superadmin')
                <span class="badge bg-danger">Super Admin</span>
              @else
                <span class="badge bg-success">Admin</span>
              @endif
            </td>
            <td>{{ $row->created_at->format('d/m/Y H:i') }}</td>
            <td>
              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $row->id }}">
                <i class="bx bx-edit"></i>
              </button>

              @if($row->id != auth()->id())
                <form action="{{ route('user-management.destroy', $row->id) }}" method="POST" class="d-inline form-delete">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button>
                </form>
              @else
                <button class="btn btn-outline-secondary btn-sm" disabled title="Tidak dapat menghapus akun sendiri">
                  <i class="bx bx-trash"></i>
                </button>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="text-center text-muted">Belum ada data user.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <!-- Pagination Component -->
    <x-pagination :paginator="$data" />
  </div>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('user-management.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah User Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Nama Lengkap</label>
          <input type="text" name="name" class="form-control" placeholder="Masukkan nama lengkap" required>
        </div>
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" placeholder="Masukkan email" required>
        </div>
        <div class="mb-3">
          <label>Role</label>
          <select name="role" class="form-select" required>
            <option value="">-- Pilih Role --</option>
            <option value="admin">Admin</option>
            <option value="superadmin">Super Admin</option>
          </select>
        </div>
        <div class="mb-3">
          <label>Password</label>
          <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
        </div>
        <div class="mb-3">
          <label>Konfirmasi Password</label>
          <input type="password" name="password_confirmation" class="form-control" placeholder="Konfirmasi password" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit User -->
@foreach ($data as $row)
<div class="modal fade" id="editUserModal{{ $row->id }}" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('user-management.update', $row->id) }}">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Nama Lengkap</label>
          <input type="text" name="name" value="{{ $row->name }}" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" value="{{ $row->email }}" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Role</label>
          <select name="role" class="form-select" required>
            <option value="admin" {{ $row->role == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="superadmin" {{ $row->role == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
          </select>
        </div>
        <div class="mb-3">
          <label>Password Baru</label>
          <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
          <small class="text-muted">Biarkan kosong jika tidak ingin mengubah password</small>
        </div>
        <div class="mb-3">
          <label>Konfirmasi Password Baru</label>
          <input type="password" name="password_confirmation" class="form-control" placeholder="Konfirmasi password baru">
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
        title: 'Yakin ingin menghapus user?',
        text: 'User yang dihapus tidak dapat dikembalikan!',
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

  @if(session('error'))
    Swal.fire({
      icon: 'error',
      title: 'Error!',
      text: '{{ session('error') }}',
      timer: 2000,
      showConfirmButton: false
    });
  @endif
</script>
@endpush