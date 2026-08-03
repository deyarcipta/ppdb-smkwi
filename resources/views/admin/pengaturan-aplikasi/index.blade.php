@extends('admin.layouts.app')
@section('title', 'Pengaturan Aplikasi')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
    <div>
      <h5 class="mb-1"><i class="bx bx-cog me-2 text-primary"></i>Pengaturan Aplikasi</h5>
      <small class="text-muted">Konfigurasi identitas sekolah, logo, WhatsApp, dan fitur sistem PPDB.</small>
    </div>
    <form action="{{ route('pengaturan-aplikasi.toggle-maintenance') }}" method="POST" class="d-inline">
      @csrf
      <button type="submit" class="btn btn-{{ $pengaturan->maintenance_mode ? 'warning' : 'outline-secondary' }} btn-sm text-nowrap">
        <i class="bx bx-{{ $pengaturan->maintenance_mode ? 'lock' : 'lock-open' }} me-1"></i>
        {{ $pengaturan->maintenance_mode ? 'Nonaktifkan' : 'Aktifkan' }} Mode Maintenance
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
          <div class="card mb-4 border border-success">
            <div class="card-header bg-success text-white d-flex align-items-center justify-content-between">
              <h6 class="mb-0 text-white"><i class="bx bxl-whatsapp me-1"></i> Pengaturan Notifikasi WhatsApp</h6>
              <span class="badge bg-white text-success fw-bold"><i class="bx bx-check-circle me-1"></i> Status Pengiriman</span>
            </div>
            <div class="card-body mt-3">
              <div class="row">
                <div class="col-md-12 mb-3">
                  <label class="form-label fw-bold">Status Notifikasi WhatsApp Sistem <span class="text-danger">*</span></label>
                  <select name="wa_status" class="form-select" required>
                    <option value="0" {{ ($pengaturan->wa_status ?? 0) == 0 ? 'selected' : '' }}>Nonaktif (Matikan semua pengiriman WhatsApp otomatis)</option>
                    <option value="1" {{ ($pengaturan->wa_status ?? 0) == 1 ? 'selected' : '' }}>Aktif (Kirim notifikasi otomatis pendaftaran, verifikasi, dan pembayaran)</option>
                  </select>
                  <small class="text-muted d-block mt-1">
                    Aktifkan untuk mengizinkan sistem mengirim pesan WhatsApp otomatis. Untuk konfigurasi URL API, API Key, dan Sesi WhatsApp, silakan buka menu <a href="{{ route('whatsapp.index') }}" class="fw-bold text-primary"><i class="bx bxl-whatsapp me-1"></i>WhatsApp Bot</a>.
                  </small>
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

<!-- Modal Tambah Sesi WhatsApp -->
<div class="modal fade" id="modalAddWaSession" tabindex="-1" aria-labelledby="modalAddWaSessionLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content text-start">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="modalAddWaSessionLabel">Tambah Sesi WhatsApp Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="form_add_wa_session">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="new_session_label" class="form-label">Label Pengenal Nomor / Sesi</label>
            <input type="text" class="form-control" id="new_session_label" name="label" required 
                   placeholder="Contoh: Nomor Utama PPDB, Nomor Cadangan Admin 1">
            <small class="form-text text-muted">Label ini membantu Anda mengidentifikasi nomor WhatsApp yang terhubung.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" id="btn_submit_add_session">Simpan & Daftarkan Sesi</button>
        </div>
      </form>
    </div>
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
  const metaDesc = document.querySelector('textarea[name="meta_description"]');
  if (metaDesc) {
    metaDesc.addEventListener('input', function() {
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
  }

  // Toggle show/hide password OpenWA API Key
  const toggleApiKeyBtn = document.getElementById('toggleApiKey');
  if (toggleApiKeyBtn) {
    toggleApiKeyBtn.addEventListener('click', function() {
      const input = document.getElementById('wa_api_key_input');
      const icon = document.getElementById('toggleIcon');
      if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bx bx-hide';
      } else {
        input.type = 'password';
        icon.className = 'bx bx-show';
      }
    });
  }

  // OpenWA Multi-Session Polling & AJAX Handlers
  const activePolls = {};

  function checkSessionStatus(sessionId, forceShowLoading = false) {
    const loadingEl = document.getElementById(`loading_${sessionId}`);
    const contentEl = document.getElementById(`content_${sessionId}`);
    const badgeEl = document.getElementById(`badge_${sessionId}`);
    const phoneEl = document.getElementById(`phone_${sessionId}`);
    const stateEl = document.getElementById(`raw_state_${sessionId}`);
    const qrContainer = document.getElementById(`qr_container_${sessionId}`);
    const qrImage = document.getElementById(`qr_image_${sessionId}`);
    const btnStart = document.getElementById(`btn_start_${sessionId}`);

    if (!loadingEl || !contentEl) return;

    if (forceShowLoading) {
      loadingEl.classList.remove('d-none');
      contentEl.classList.add('d-none');
    }

    fetch(`/panel/whatsapp-sessions/${sessionId}/status`)
      .then(response => response.json())
      .then(data => {
        loadingEl.classList.add('d-none');
        contentEl.classList.remove('d-none');

        if (data.success) {
          stateEl.textContent = data.status;
          phoneEl.textContent = data.phone_number || '-';
          badgeEl.className = 'badge p-2 px-3';

          if (data.connected) {
            badgeEl.classList.add('bg-success');
            badgeEl.textContent = 'Terhubung';
            qrContainer.classList.add('d-none');
            btnStart.classList.add('d-none');
            stopPolling(sessionId);
          } else {
            if (data.status === 'NOT_STARTED') {
              badgeEl.classList.add('bg-danger');
              badgeEl.textContent = 'Offline';
              qrContainer.classList.add('d-none');
              btnStart.classList.remove('d-none');
              stopPolling(sessionId);
            } else {
              badgeEl.classList.add('bg-warning', 'text-dark');
              badgeEl.textContent = 'Butuh Scan QR';
              btnStart.classList.add('d-none');

              if (data.qrCode) {
                qrContainer.classList.remove('d-none');
                const qrSrc = data.qrCode.startsWith('data:') ? data.qrCode : 'data:image/png;base64,' + data.qrCode;
                qrImage.src = qrSrc;
              } else {
                qrContainer.classList.add('d-none');
              }
              startPolling(sessionId);
            }
          }
        } else {
          badgeEl.className = 'badge p-2 px-3 bg-danger';
          badgeEl.textContent = 'Error Gateway';
          stateEl.textContent = 'ERROR';
          qrContainer.classList.add('d-none');
          btnStart.classList.add('d-none');
          stopPolling(sessionId);
        }
      })
      .catch(error => {
        loadingEl.classList.add('d-none');
        contentEl.classList.remove('d-none');
        badgeEl.className = 'badge p-2 px-3 bg-danger';
        badgeEl.textContent = 'Server Error';
        stateEl.textContent = 'SERVER_ERROR';
        qrContainer.classList.add('d-none');
        btnStart.classList.add('d-none');
        stopPolling(sessionId);
      });
  }

  function startPolling(sessionId) {
    if (!activePolls[sessionId]) {
      activePolls[sessionId] = setInterval(() => checkSessionStatus(sessionId), 5000);
    }
  }

  function stopPolling(sessionId) {
    if (activePolls[sessionId]) {
      clearInterval(activePolls[sessionId]);
      delete activePolls[sessionId];
    }
  }

  function checkAllSessions() {
    document.querySelectorAll('.session-card-wrapper').forEach(card => {
      const sessionId = card.getAttribute('data-id');
      checkSessionStatus(sessionId);
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    checkAllSessions();
  });

  document.addEventListener('click', function(e) {
    // Cek koneksi manual
    if (e.target.closest('.check-session-btn')) {
      const btn = e.target.closest('.check-session-btn');
      const sessionId = btn.getAttribute('data-id');
      checkSessionStatus(sessionId, true);
    }

    // Mulai Sesi
    if (e.target.closest('.start-session-btn')) {
      const btn = e.target.closest('.start-session-btn');
      const sessionId = btn.getAttribute('data-id');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...';

      fetch(`/panel/whatsapp-sessions/${sessionId}/start`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        Swal.fire({
          icon: data.success ? 'success' : 'error',
          title: data.success ? 'Berhasil' : 'Gagal',
          text: data.message
        });
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-play-circle me-1"></i> Mulai Sesi';
        checkSessionStatus(sessionId, true);
      })
      .catch(error => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server untuk memulai sesi.' });
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-play-circle me-1"></i> Mulai Sesi';
      });
    }

    // Menghapus Sesi
    if (e.target.closest('.delete-session-btn')) {
      const btn = e.target.closest('.delete-session-btn');
      const sessionId = btn.getAttribute('data-id');

      Swal.fire({
        title: 'Hapus Sesi WhatsApp?',
        text: "Sesi ini akan dihapus dari database dan server OpenWA.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/panel/whatsapp-sessions/${sessionId}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Content-Type': 'application/json'
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              stopPolling(sessionId);
              location.reload();
            } else {
              Swal.fire('Gagal', data.message || 'Gagal menghapus sesi.', 'error');
            }
          })
          .catch(error => {
            Swal.fire('Error', 'Gagal menghubungi server.', 'error');
          });
        }
      });
    }
  });

  // Toggle Switch status aktif sesi
  document.addEventListener('change', function(e) {
    if (e.target.classList.contains('toggle-session-switch')) {
      const switchEl = e.target;
      const sessionId = switchEl.getAttribute('data-id');

      fetch(`/panel/whatsapp-sessions/${sessionId}/toggle`, {
        method: 'PUT',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (!data.success) {
          switchEl.checked = !switchEl.checked;
          Swal.fire('Gagal', 'Gagal mengubah status sesi.', 'error');
        }
      })
      .catch(error => {
        switchEl.checked = !switchEl.checked;
        Swal.fire('Error', 'Gagal menghubungi server.', 'error');
      });
    }
  });

  // Submit form tambah sesi baru
  const formAddSession = document.getElementById('form_add_wa_session');
  if (formAddSession) {
    formAddSession.addEventListener('submit', function(e) {
      e.preventDefault();
      const btnSubmit = document.getElementById('btn_submit_add_session');
      btnSubmit.disabled = true;
      btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

      const formData = new FormData(this);

      fetch('/panel/whatsapp-sessions', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = 'Simpan & Daftarkan Sesi';

        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Sesi Berhasil Ditambahkan',
            text: data.message
          }).then(() => {
            location.reload();
          });
        } else {
          Swal.fire('Gagal', data.message || 'Gagal menambahkan sesi baru.', 'error');
        }
      })
      .catch(error => {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = 'Simpan & Daftarkan Sesi';
        Swal.fire('Error', 'Gagal menghubungi server OpenWA.', 'error');
      });
    });
  }
</script>
@endpush