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

      <!-- Pengaturan Notifikasi WhatsApp -->
      <div class="row">
        <div class="col-12">
          <div class="card mb-4 border border-warning">
            <div class="card-header bg-secondary text-white d-flex align-items-center justify-content-between">
              <h6 class="mb-0 text-white"><i class="bx bxl-whatsapp me-1"></i> Pengaturan Notifikasi WhatsApp</h6>
              <span class="badge bg-warning text-dark fs-12"><i class="bx bx-time-five me-1"></i> Masih Dalam Pengembangan</span>
            </div>
            <div class="card-body mt-3">
              <div class="alert alert-warning py-2 mb-3" role="alert">
                <i class="bx bx-info-circle me-1"></i> Fitur notifikasi otomatis via WhatsApp Bot saat ini <strong>Masih Dalam Pengembangan</strong> dan status otomatis di-set <strong>Nonaktif</strong>.
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">Status Notifikasi WhatsApp <span class="text-danger">*</span></label>
                  <select class="form-select bg-light" disabled>
                    <option value="0" selected>Nonaktif (Matikan semua pengiriman pesan otomatis via WA)</option>
                  </select>
                  <input type="hidden" name="enable_whatsapp" value="0">
                  <small class="text-muted d-block mt-1">Fitur notifikasi WhatsApp dinonaktifkan sementara selama tahap pengembangan.</small>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">Nomor HP Admin (Penerima Notifikasi Pembayaran)</label>
                  <input type="text" class="form-control bg-light" value="-" disabled>
                  <small class="text-muted d-block mt-1">Nomor HP Admin diset <code>-</code> karena integrasi bot WhatsApp belum diaktifkan.</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pengaturan Cetak Kartu -->
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header bg-warning text-white">
              <h6 class="mb-0 text-white"><i class="bx bx-printer"></i> Pengaturan Cetak Kartu</h6>
            </div>
            <div class="card-body mt-3">
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label fw-bold">Fitur Cetak Kartu <span class="text-danger">*</span></label>
                  <select name="enable_cetak_kartu" class="form-select" required>
                    <option value="1" {{ $pengaturan->enable_cetak_kartu ? 'selected' : '' }}>Aktif (Siswa dapat mencetak kartu)</option>
                    <option value="0" {{ !$pengaturan->enable_cetak_kartu ? 'selected' : '' }}>Nonaktif (Fitur cetak disembunyikan)</option>
                  </select>
                  <small class="text-muted d-block mt-1">Aktifkan agar siswa yang statusnya diterima dapat mencetak kartu peserta PPDB.</small>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label fw-bold">Instruksi Username Kartu</label>
                  <input type="text" name="kartu_username_contoh" class="form-control" value="{{ $pengaturan->kartu_username_contoh }}">
                  <small class="text-muted d-block mt-1">Kode awalan kustom. Gabung otomatis dengan 3 digit terakhir. Biarkan kosong untuk username asli. Contoh: <code>K010401310</code></small>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label fw-bold">Instruksi Password Kartu</label>
                  <input type="text" name="kartu_password_contoh" class="form-control" value="{{ $pengaturan->kartu_password_contoh }}">
                  <small class="text-muted d-block mt-1">Password default. Biarkan kosong jika ingin mencetak **Password Acak Otomatis** siswa.</small>
                </div>
                
                <div class="col-md-12 mt-2">
                  <hr>
                  <label class="form-label fw-bold">Upload Ttd & Stempel Panitia</label>
                  <div class="row align-items-center">
                    <div class="col-md-8 mb-2 mb-md-0">
                      <input type="file" name="ttd_stempel" class="form-control" accept="image/*">
                      <small class="text-muted d-block mt-1">Upload 1 file gambar gabungan Tanda Tangan & Stempel Panitia (Format: PNG, JPG, WEBP. Transparan disarankan, Maks 2MB). Gambar ini akan otomatis muncul pada area TTD & Stempel Kartu Peserta.</small>
                    </div>
                    <div class="col-md-4 text-center">
                      @if(!empty($pengaturan->ttd_stempel) && file_exists(public_path($pengaturan->ttd_stempel)))
                        <div class="p-2 border rounded bg-light d-inline-block">
                          <img src="{{ asset($pengaturan->ttd_stempel) }}" alt="Ttd & Stempel" style="max-height: 70px; max-width: 150px; object-fit: contain;">
                          <div class="small text-muted mt-1">Ttd & Stempel Terpasang</div>
                        </div>
                      @else
                        <div class="p-2 border rounded bg-light text-muted small">
                          Belum ada gambar Ttd & Stempel
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pengaturan Tampilan & Warna Website -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-info text-white">
              <h6 class="mb-0 text-white"><i class="bx bx-palette me-1"></i> Tampilan & Warna Tema Website</h6>
            </div>
            <div class="card-body mt-3">
              <div class="row">
                <!-- Upload Hero Background -->
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold">Background Hero Website</label>
                  <input type="file" name="hero_bg" class="form-control" accept="image/*">
                  <small class="text-muted d-block mt-1">Upload gambar latar belakang (background) seksi Hero halaman utama website. (Format: JPG, PNG, WEBP. Maks: 4MB).</small>
                  
                  @if(!empty($pengaturan->hero_bg) && file_exists(public_path($pengaturan->hero_bg)))
                    <div class="mt-2 p-2 border rounded bg-light">
                      <img src="{{ asset($pengaturan->hero_bg) }}" alt="Hero Background" style="max-height: 100px; width: 100%; object-fit: cover;" class="rounded mb-1">
                      <div class="small text-muted"><i class="bx bx-check-circle text-success me-1"></i>Background Hero Kustom Terpasang</div>
                    </div>
                  @else
                    <div class="mt-2 p-2 border rounded bg-light text-muted small">
                      <i class="bx bx-image me-1"></i>Menggunakan Background Hero Default
                    </div>
                  @endif
                </div>

                <!-- Color Pickers -->
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold mb-2">Skema Warna Website</label>
                  
                  <div class="mb-3 d-flex align-items-center gap-3">
                    <input type="color" name="warna_utama" class="form-control form-control-color" value="{{ $pengaturan->warna_utama ?? '#6b21a8' }}" title="Pilih Warna Utama">
                    <div>
                      <strong class="d-block">Warna Utama (Primary Color)</strong>
                      <small class="text-muted">Digunakan untuk judul seksi, marquee info, teks aksen, dan elemen dominan.</small>
                    </div>
                  </div>

                  <div class="mb-3 d-flex align-items-center gap-3">
                    <input type="color" name="warna_header" class="form-control form-control-color" value="{{ $pengaturan->warna_header ?? '#a948ea' }}" title="Pilih Warna Header Navbar">
                    <div>
                      <strong class="d-block">Warna Header / Navbar</strong>
                      <small class="text-muted">Digunakan untuk latar belakang menu navigasi navbar bagian atas.</small>
                    </div>
                  </div>

                  <div class="mb-3 d-flex align-items-center gap-3">
                    <input type="color" name="warna_sekunder" class="form-control form-control-color" value="{{ $pengaturan->warna_sekunder ?? '#16a34a' }}" title="Pilih Warna Sekunder / Tombol">
                    <div>
                      <strong class="d-block">Warna Sekunder / Tombol (Accent Color)</strong>
                      <small class="text-muted">Digunakan untuk tombol utama Pendaftaran PPDB dan elemen sorotan aksi.</small>
                    </div>
                  </div>
                </div>
              </div>
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