@extends('admin.layouts.app')
@section('title', 'Pengumuman')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Pengumuman</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPengumumanModal">
      <i class="bx bx-plus"></i> Tambah
    </button>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light text-center">
          <tr>
            <th>No</th>
            <th>Gambar</th>
            <th>Judul</th>
            <th>Isi</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($data as $row)
          <tr class="text-center">
            <td>{{ $loop->iteration }}</td>
            <td>
              @if($row->gambar)
                <img src="{{ Storage::url('public/pengumuman/' . $row->gambar) }}" 
                     alt="{{ $row->judul }}" 
                     class="rounded" 
                     width="60" 
                     height="60" 
                     style="object-fit: cover;">
              @else
                <div class="bg-secondary rounded d-inline-flex align-items-center justify-content-center" 
                     style="width: 60px; height: 60px;">
                  <i class="bx bx-image text-white"></i>
                </div>
              @endif
            </td>
            <td class="text-start">{{ $row->judul }}</td>
            <td class="text-start">
              <div class="text-truncate" style="max-width: 200px;" title="{{ strip_tags($row->isi) }}">
                {{ Str::limit(strip_tags($row->isi), 50) }}
              </div>
            </td>
            <td>{{ $row->tanggal->format('d/m/Y') }}</td>
            <td>
              @if($row->status)
                <span class="badge bg-success">Aktif</span>
              @else
                <span class="badge bg-secondary">Nonaktif</span>
              @endif
            </td>
            <td>
              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editPengumumanModal{{ $row->id }}">
                <i class="bx bx-edit"></i>
              </button>

              <form action="{{ route('pengumuman.destroy', $row->id) }}" method="POST" class="d-inline form-delete">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button>
              </form>

              @if($row->status)
                <a href="{{ route('pengumuman.nonaktifkan', $row->id) }}" class="btn btn-secondary btn-sm" title="Nonaktifkan">
                  <i class="bx bx-block"></i>
                </a>
              @else
                <a href="{{ route('pengumuman.aktifkan', $row->id) }}" class="btn btn-success btn-sm" title="Aktifkan">
                  <i class="bx bx-check"></i>
                </a>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center text-muted">Belum ada data pengumuman.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <!-- Pagination Component -->
    <x-pagination :paginator="$data" />
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addPengumumanModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('pengumuman.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Pengumuman</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Judul Pengumuman</label>
          <input type="text" name="judul" class="form-control" placeholder="Contoh: Pendaftaran Gelombang 1 Dibuka" required>
        </div>
        <div class="mb-3">
            <label>Isi Pengumuman</label>
            <textarea id="editor" name="isi" class="form-control" rows="6" placeholder="Masukkan isi pengumuman..."></textarea>
            <small class="text-muted">Gunakan format teks biasa atau HTML sederhana</small>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label>Tanggal</label>
              <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label>Gambar</label>
              <input type="file" name="gambar" class="form-control" accept="image/*">
              <small class="text-muted">Format: jpeg, png, jpg, gif (max: 2MB)</small>
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
<div class="modal fade" id="editPengumumanModal{{ $row->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('pengumuman.update', $row->id) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Pengumuman</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Judul Pengumuman</label>
          <input type="text" name="judul" value="{{ $row->judul }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Isi Pengumuman</label>
            <textarea id="editor{{ $row->id }}" name="isi" class="form-control" rows="6">{{ old('isi', $row->isi) }}</textarea>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label>Tanggal</label>
              <input type="date" name="tanggal" value="{{ $row->tanggal->format('Y-m-d') }}" class="form-control" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label>Gambar</label>
              @if($row->gambar)
                <div class="mb-2">
                  <img src="{{ Storage::url('public/pengumuman/' . $row->gambar) }}" 
                       alt="{{ $row->judul }}" 
                       class="rounded" 
                       width="80" 
                       height="80" 
                       style="object-fit: cover;">
                </div>
              @endif
              <input type="file" name="gambar" class="form-control" accept="image/*">
              <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar</small>
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
<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
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

  ClassicEditor
    .create(document.querySelector('#editor'), {
        toolbar: [
            'heading', '|',
            'bold', 'italic', 'underline', 'link', 'bulletedList', 'numberedList', '|',
            'insertTable', 'imageUpload', 'blockQuote', 'mediaEmbed', '|',
            'undo', 'redo'
        ]
    })
    .catch(error => {
        console.error(error);
    });

// CKEditor untuk masing-masing modal edit
@foreach ($data as $row)
$('#editPengumumanModal{{ $row->id }}').on('shown.bs.modal', function () {
    if (!$(this).data('ckeditor-initialized')) {
        ClassicEditor
            .create(document.querySelector('#editor{{ $row->id }}'), {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'underline', 'link', 'bulletedList', 'numberedList', '|',
                    'insertTable', 'imageUpload', 'blockQuote', 'mediaEmbed', '|',
                    'undo', 'redo'
                ]
            })
            .then(editor => {
                $(this).data('ckeditorInstance', editor); // Simpan instance editor
            })
            .catch(error => {
                console.error(error);
            });

        $(this).data('ckeditor-initialized', true); // Tandai sudah diinisialisasi
    }
});
@endforeach

</script>
@endpush