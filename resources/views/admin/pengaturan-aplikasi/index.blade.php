@extends('admin.layouts.app')
@section('title', 'Pengaturan Aplikasi')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Pengaturan Aplikasi</h5>
    <form action="{{ route('pengaturan-aplikasi.toggle-maintenance') }}" method="POST" class="d-inline">
      @csrf
      <button type="submit" class="btn btn-{{ $pengaturan->maintenance_mode ? 'warning' : 'secondary' }} btn-sm">
        <i class="bx bx-{{ $pengaturan->maintenance_mode ? 'lock' : 'lock-open' }}"></i>
        {{ $pengaturan->maintenance_mode ? 'Nonaktifkan' : 'Aktifkan' }} Maintenance
      </button>
    </form>
  </div>

  <div class="card-body">
    <form method="POST" action="{{ route('pengaturan-aplikasi.update') }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="row">
        <!-- Informasi Dasar -->
        <div class="col-md-6">
          <div class="card mb-4">
            <div class="card-header bg-primary text-white">
              <h6 class="mb-0"><i class="bx bx-cog"></i> Informasi Dasar</h6>
            </div>
            <div class="card-body">
              <div class="mb-3 mt-3">
                <label>Nama Sekolah</label>
                <input type="text" name="nama_sekolah" class="form-control" value="{{ $pengaturan->nama_sekolah }}" required>
              </div>

              <div class="mb-3">
                <label>Nama Aplikasi</label>
                <input type="text" name="nama_aplikasi" class="form-control" value="{{ $pengaturan->nama_aplikasi }}" required>
              </div>

              <div class="mb-3">
                <label>Logo Aplikasi</label>
                @if($pengaturan->logo)
                  <div class="mb-2">
                    <img src="{{ asset($pengaturan->logo) }}" 
                         alt="Logo" class="img-thumbnail" width="150">
                  </div>
                @endif
                <input type="file" name="logo" class="form-control" accept="image/*">
                <small class="text-muted">Rekomendasi: PNG transparent, max 2MB</small>
              </div>

              <div class="mb-3">
                <label>Favicon</label>
                @if($pengaturan->favicon)
                  <div class="mb-2">
                    <img src="{{ asset($pengaturan->favicon) }}" 
                         alt="Favicon" class="img-thumbnail" width="32">
                  </div>
                @endif
                <input type="file" name="favicon" class="form-control" accept="image/*">
                <small class="text-muted">Format: ICO atau PNG 32x32, max 1MB</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Kontak & Sosial Media -->
        <div class="col-md-6">
          <div class="card mb-4">
            <div class="card-header bg-success text-white">
              <h6 class="mb-0"><i class="bx bx-phone"></i> Kontak & Sosial Media</h6>
            </div>
            <div class="card-body">
              <div class="mb-3 mt-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ $pengaturan->email }}">
              </div>

              <div class="mb-3">
                <label>Telepon</label>
                <input type="text" name="telepon" class="form-control" value="{{ $pengaturan->telepon }}">
              </div>

              <div class="mb-3">
                <label>No Handphone</label>
                <input type="text" name="no_hp" class="form-control" value="{{ $pengaturan->no_hp }}">
              </div>

              <div class="mb-3">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" rows="3">{{ $pengaturan->alamat }}</textarea>
              </div>

              <div class="mb-3">
                <label>Facebook URL</label>
                <input type="url" name="facebook" class="form-control" value="{{ $pengaturan->facebook }}" placeholder="https://facebook.com/username">
              </div>

              <div class="mb-3">
                <label>Instagram URL</label>
                <input type="url" name="instagram" class="form-control" value="{{ $pengaturan->instagram }}" placeholder="https://instagram.com/username">
              </div>

              <div class="mb-3">
                <label>YouTube URL</label>
                <input type="url" name="youtube" class="form-control" value="{{ $pengaturan->youtube }}" placeholder="https://youtube.com/username">
              </div>

              <div class="mb-3">
                <label>Tiktok URL</label>
                <input type="url" name="tiktok" class="form-control" value="{{ $pengaturan->tiktok }}" placeholder="https://tiktok.com/@username">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- SEO & Maintenance -->
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header bg-info text-white">
              <h6 class="mb-0"><i class="bx bx-search-alt"></i> SEO & Maintenance</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3 mt-3">
                    <label>Meta Description</label>
                    <textarea name="meta_description" class="form-control" rows="3" maxlength="160">{{ $pengaturan->meta_description }}</textarea>
                    <small class="text-muted">Deskripsi untuk SEO (max 160 karakter)</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3 mt-3">
                    <label>Meta Keywords</label>
                    <textarea name="meta_keywords" class="form-control" rows="3">{{ $pengaturan->meta_keywords }}</textarea>
                    <small class="text-muted">Kata kunci dipisahkan dengan koma</small>
                  </div>
                </div>
              </div>

              @if($pengaturan->maintenance_mode)
                <div class="mb-3">
                  <label>Pesan Maintenance</label>
                  <textarea name="maintenance_message" class="form-control" rows="3" placeholder="Pesan yang ditampilkan saat maintenance mode aktif">{{ $pengaturan->maintenance_message }}</textarea>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      <!-- Preview & Action -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body text-center">
              <div class="mb-4">
                <h6>Preview Logo & Nama Aplikasi</h6>
                <div class="d-flex align-items-center justify-content-center">
                  @if($pengaturan->logo)
                    <img src="{{ asset($pengaturan->logo) }}" 
                         alt="Logo" class="me-3" height="40">
                  @else
                    <div class="bg-primary rounded me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                      <i class="bx bx-building text-white"></i>
                    </div>
                  @endif
                  <h4 class="mb-0">{{ $pengaturan->nama_aplikasi }}</h4>
                </div>
              </div>

              <div class="d-flex justify-content-center gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="bx bx-save"></i> Simpan Pengaturan
                </button>
                <a href="{{ route('pengaturan-aplikasi.index') }}" class="btn btn-outline-secondary">
                  <i class="bx bx-reset"></i> Reset Form
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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

  // Hitung karakter meta description
  document.querySelector('textarea[name="meta_description"]').addEventListener('input', function() {
    const maxLength = 160;
    const currentLength = this.value.length;
    const counter = this.parentElement.querySelector('.char-counter') || 
                   document.createElement('small');
    
    counter.className = 'text-muted char-counter';
    counter.textContent = `${currentLength}/${maxLength} karakter`;
    
    if (!this.parentElement.querySelector('.char-counter')) {
      this.parentElement.appendChild(counter);
    }

    if (currentLength > maxLength) {
      counter.className = 'text-danger char-counter';
    }
  });
</script>
@endpush