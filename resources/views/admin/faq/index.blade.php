@extends('admin.layouts.app')
@section('title', 'FAQ')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Pertanyaan Umum (FAQ)</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addFaqModal">
      <i class="bx bx-plus"></i> Tambah
    </button>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light text-center">
          <tr>
            <th>No</th>
            <th>Pertanyaan</th>
            <th>Jawaban</th>
            <th>Urutan</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($data as $row)
          <tr class="text-center">
            <td>{{ $loop->iteration }}</td>
            <td class="text-start">{{ Str::limit($row->pertanyaan, 100) }}</td>
            <td class="text-start">{{ Str::limit($row->jawaban, 100) }}</td>
            <td>{{ $row->urutan }}</td>
            <td>
              @if($row->status)
                <span class="badge bg-success">Aktif</span>
              @else
                <span class="badge bg-secondary">Nonaktif</span>
              @endif
            </td>
            <td>
              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editFaqModal{{ $row->id }}">
                <i class="bx bx-edit"></i>
              </button>

              <form action="{{ route('faq.destroy', $row->id) }}" method="POST" class="d-inline form-delete">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button>
              </form>

              @if($row->status)
                <a href="{{ route('faq.nonaktifkan', $row->id) }}" class="btn btn-secondary btn-sm" title="Nonaktifkan">
                  <i class="bx bx-block"></i>
                </a>
              @else
                <a href="{{ route('faq.aktifkan', $row->id) }}" class="btn btn-success btn-sm" title="Aktifkan">
                  <i class="bx bx-check"></i>
                </a>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="text-center text-muted">Belum ada data FAQ.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <!-- Pagination Component -->
    <x-pagination :paginator="$data" />
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addFaqModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('faq.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah FAQ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Pertanyaan</label>
          <input type="text" name="pertanyaan" class="form-control" placeholder="Contoh: Apakah ada tes masuk untuk mendaftar?" required>
        </div>
        <div class="mb-3">
          <label>Jawaban</label>
          <textarea name="jawaban" class="form-control" rows="5" placeholder="Masukkan jawaban untuk pertanyaan di atas..." required></textarea>
          <small class="text-muted">Gunakan format teks biasa atau HTML sederhana</small>
        </div>
        <div class="mb-3">
          <label>Urutan</label>
          <input type="number" name="urutan" class="form-control" value="1" min="1" required>
          <small class="text-muted">Urutan tampil (angka kecil akan tampil pertama)</small>
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
<div class="modal fade" id="editFaqModal{{ $row->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('faq.update', $row->id) }}">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit FAQ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Pertanyaan</label>
          <input type="text" name="pertanyaan" value="{{ $row->pertanyaan }}" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Jawaban</label>
          <textarea name="jawaban" class="form-control" rows="5" required>{{ $row->jawaban }}</textarea>
        </div>
        <div class="mb-3">
          <label>Urutan</label>
          <input type="number" name="urutan" value="{{ $row->urutan }}" class="form-control" min="1" required>
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