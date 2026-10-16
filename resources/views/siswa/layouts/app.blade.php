<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  @php
      $pengaturan = \App\Models\PengaturanAplikasi::first();
      $logo = $pengaturan->logo ?? 'sneat/img/logowi.png';
      $namaAplikasi = $pengaturan->nama_aplikasi ?? 'PPDB SMK WI';
      $tipeAplikasi = $pengaturan->tipe_aplikasi ?? 'PPDB';
      $tahunAjaranAktif = $pengaturan ? $pengaturan->tahunAjaranAktif : \App\Models\TahunAjaran::where('status', 'aktif')->first();
  @endphp
  <link rel="icon" href="{{ asset($logo) }}" type="image/png">
  <title>@yield('title', 'Portal Siswa') - {{ $namaAplikasi }}{{ $tahunAjaranAktif ? ' (T.A '.$tahunAjaranAktif->nama.')' : '' }}</title>

  <!-- Sneat CSS -->
  <link rel="stylesheet" href="{{ asset('sneat/vendor/css/core.css') }}">
  <link rel="stylesheet" href="{{ asset('sneat/vendor/css/theme-default.css') }}">
  <link rel="stylesheet" href="{{ asset('sneat/css/demo.css') }}">

  <!-- Fonts & Icons -->
  <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/iconify-icons.css') }}">
  <!-- Font Awesome 6 CDN -->
  <link rel="stylesheet" href="{{ asset('sneat/fontawesome/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/flag-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/boxicons.css') }}">

  <!-- Helpers -->
  <script src="{{ asset('sneat/vendor/js/helpers.js') }}"></script>

  <style>
    /* ======== PERBAIKAN JARAK DAN TAMPILAN ======== */
    body {
      background-color: #f8f9fa !important;
    }

    .container-p-y {
      padding-top: 1rem !important;
      padding-bottom: 0.5rem !important;
    }

    .content-wrapper {
      min-height: auto !important;
      padding-bottom: 0 !important;
    }

    .layout-page {
      min-height: auto !important;
    }

    .card {
      margin-bottom: 0.75rem !important;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      border-radius: 0.75rem;
    }

    @media (min-width: 1200px) {
      .content-wrapper > .container-xxl {
        padding-left: 1.625rem !important;
        padding-right: 1.625rem !important;
        padding-inline: 1.625rem !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
      }
    }

    /* Pastikan seluruh sub-container di semua halaman dinetralkan agar lebar kartu sama dengan navbar */
    .content-wrapper .container,
    .content-wrapper .container-fluid,
    .content-wrapper .container-sm,
    .content-wrapper .container-md,
    .content-wrapper .container-lg,
    .content-wrapper .container-xl,
    .content-wrapper .main-content,
    .container-xxl .container-xxl,
    .container-xxl .container-fluid {
      padding-left: 0 !important;
      padding-right: 0 !important;
      padding-inline: 0 !important;
      padding-inline-start: 0 !important;
      padding-inline-end: 0 !important;
      margin-left: 0 !important;
      margin-right: 0 !important;
      margin-inline: 0 !important;
      width: 100% !important;
      max-width: 100% !important;
      box-sizing: border-box !important;
    }

    .content-wrapper .card,
    .container-xxl .card,
    .card-full-width {
      margin-left: 0 !important;
      margin-right: 0 !important;
      margin-inline: 0 !important;
      width: 100% !important;
      max-width: 100% !important;
      box-sizing: border-box !important;
    }

    .container-p-y .container-p-y {
      padding-top: 0 !important;
      padding-bottom: 0 !important;
    }

    /* Footer agar rapi di bawah */
    footer.footer {
      padding: 0.5rem 0;
      background-color: #fff;
      border-top: 1px solid #e4e6e8;
      text-align: center;
      font-size: 0.9rem;
      color: #6c757d;
    }

    /* Perbaiki z-index Swal agar selalu di atas elemen Sneat */
    .swal2-container {
      z-index: 9999 !important;
    }

    /* Jika navbar tetap muncul efek abu-abu di belakang swal */
    .swal2-backdrop-show {
      backdrop-filter: blur(3px);
    }

    /* Responsif: pastikan navbar dan toggle tampil di HP */
    .layout-navbar {
      position: sticky;
      top: 0;
      z-index: 1040;
    }

    .layout-menu-toggle {
      cursor: pointer;
    }

    @media (max-width: 1199.98px) {
      .layout-page {
        padding-left: 0 !important;
        padding-right: 0 !important;
        padding-inline-start: 0 !important;
        padding-inline-end: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
      }
      .layout-navbar {
        position: sticky !important;
        top: 0.5rem !important;
        z-index: 1040 !important;
        width: calc(100% - 1.5rem) !important;
        inline-size: calc(100% - 1.5rem) !important;
        max-width: calc(100% - 1.5rem) !important;
        max-inline-size: calc(100% - 1.5rem) !important;
        margin: 0.65rem auto 0.75rem auto !important;
        margin-inline: auto !important;
        border-radius: 0.5rem !important;
        padding: 0.5rem 1rem !important;
        box-sizing: border-box !important;
        background-color: #ffffff !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08) !important;
      }
      .content-wrapper > .container-xxl,
      .container-p-y,
      .container-xxl.container-p-y {
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
        padding-inline: 0.75rem !important;
        padding-top: 0 !important;
        padding-bottom: 1.5rem !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
      }

      .container-fluid,
      .container-fluid.p-0,
      .container-xxl .container-xxl,
      .container-xxl .container-fluid {
        padding-left: 0 !important;
        padding-right: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
      }

      .content-wrapper .card,
      .container-xxl .card {
        margin-left: 0 !important;
        margin-right: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
      }

      .content-wrapper .row {
        margin-left: -0.375rem !important;
        margin-right: -0.375rem !important;
      }
      .content-wrapper .row > [class*="col-"] {
        padding-left: 0.375rem !important;
        padding-right: 0.375rem !important;
      }
      .table-responsive {
        -webkit-overflow-scrolling: touch;
      }
      .modal-dialog {
        margin: 0.5rem !important;
        max-width: calc(100% - 1rem) !important;
      }

      /* Hilangkan total icon dan warna tombol lingkaran di atas sidebar */
      .app-brand .layout-menu-toggle,
      .menu .app-brand .layout-menu-toggle,
      .layout-menu .layout-menu-toggle,
      aside .layout-menu-toggle {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
        width: 0 !important;
        height: 0 !important;
        border: none !important;
        background: transparent !important;
      }

      .layout-overlay {
        display: none !important;
        pointer-events: none !important;
        opacity: 0 !important;
      }

      html.layout-menu-expanded .layout-overlay,
      body.layout-menu-expanded .layout-overlay,
      .layout-menu-expanded .layout-overlay {
        display: block !important;
        pointer-events: auto !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background-color: rgba(0, 0, 0, 0.45) !important;
        z-index: 1085 !important;
        backdrop-filter: blur(2px) !important;
        opacity: 1 !important;
      }
    }

    /* ======== STYLE PAGINATION GLOBAL ======== */
    .pagination-btn {
      border-radius: 6px !important;
      min-width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0;
      font-size: 0.8rem;
      font-weight: 500;
      border: 1px solid #dee2e6;
      transition: all 0.2s ease-in-out;
      text-decoration: none;
    }

    .pagination-btn:hover:not(.disabled) {
      transform: translateY(-1px);
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      text-decoration: none;
    }

    .pagination-btn.active {
      background-color: #0d6efd;
      border-color: #0d6efd;
      color: white;
    }

    .pagination-btn:focus {
      box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    /* Reduce spacing between table and pagination */
    .card-body {
      padding-bottom: 0.5rem;
    }

    .border-top {
      border-top: 1px solid #dee2e6 !important;
      margin-top: 1rem;
      padding-top: 1rem;
    }

    /* Custom style untuk siswa */
    .student-theme {
      --bs-primary: #667eea;
      --bs-primary-rgb: 102, 126, 234;
    }
  </style>
</head>

<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

      {{-- Sidebar Siswa --}}
      @include('siswa.partials.sidebar')

      <div class="layout-page">

        {{-- Navbar Siswa --}}
        @include('siswa.partials.navbar')

        <div class="content-wrapper">
          <div class="container-xxl flex-grow-1 container-p-y">
            @yield('content')
          </div>

          {{-- Footer Siswa --}}
          @include('siswa.partials.footer')
        </div>
      </div>
    </div>

    <!-- Overlay untuk mobile sidebar (tanpa class layout-menu-toggle) -->
    <div class="layout-overlay"></div>
  </div>

  <!-- Sneat Vendor JS -->
  <script src="{{ asset('sneat/vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('sneat/vendor/libs/popper/popper.js') }}"></script>
  <script src="{{ asset('sneat/vendor/js/bootstrap.js') }}"></script>
  <script src="{{ asset('sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('sneat/vendor/js/menu.js') }}"></script>

  <!-- Main Sneat JS -->
  <script src="{{ asset('sneat/js/main.js') }}"></script>

  <!-- SweetAlert2 -->
  <script src="{{ asset('sneat/js/sweetalert2@11.js') }}"></script>

  <!-- Script tambahan dari halaman (jika ada) -->
  @stack('scripts')

  <!-- Notifikasi Global SweetAlert -->
  <script>
    @if(session('success'))
      Swal.fire({
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        icon: 'success',
        confirmButtonText: 'OK'
      });
    @endif

    @if(session('error'))
      Swal.fire({
        title: 'Gagal!',
        text: "{{ session('error') }}",
        icon: 'error',
        confirmButtonText: 'OK'
      });
    @endif

    @if(session('warning'))
      Swal.fire({
        title: 'Peringatan!',
        text: "{{ session('warning') }}",
        icon: 'warning',
        confirmButtonText: 'OK'
      });
    @endif

    // Konfirmasi logout
    function confirmLogout() {
      Swal.fire({
        title: 'Konfirmasi Logout',
        text: 'Apakah Anda yakin ingin logout?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Logout',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById('logout-form').submit();
        }
      });
    }

    // Modal Pengingat & Konfirmasi Kesiapan Tes saat Pertama Kali Cetak Kartu
    function confirmCetakKartu(url, isFirstTime) {
      if (isFirstTime == '1' || isFirstTime === 1 || isFirstTime === true) {
        Swal.fire({
          icon: 'info',
          title: 'Kesiapan Tes & Verifikasi Berkas',
          html: `
            <div class="text-start" style="font-size: 0.95rem; line-height: 1.6;">
              <p class="mb-2">Anda akan mengunduh / mencetak <strong>Kartu Tanda Peserta & Lembar Ceklis Berkas</strong>.</p>
              <div class="alert alert-warning py-2 px-3 my-3 small border-0 text-start" style="background-color: #fff3cd; color: #664d03; border-radius: 6px;">
                <i class='bx bx-info-circle me-1'></i> <strong>Perhatian:</strong> Dokumen fisik ini <u>wajib dicetak dan dibawa</u> saat Anda hadir ke sekolah untuk mengikuti <strong>Tes Wawancara / Seleksi</strong> serta <strong>Verifikasi Berkas Fisik</strong>.
              </div>
              <p class="mb-0 text-muted small">Pastikan Anda siap hadir sesuai jadwal tes sekolah. Lanjutkan untuk mencetak kartu sekarang?</p>
            </div>
          `,
          showCancelButton: true,
          confirmButtonText: '<i class="bx bx-printer me-1"></i> Ya, Saya Siap & Cetak Kartu',
          cancelButtonText: 'Batal',
          confirmButtonColor: '#696cff',
          cancelButtonColor: '#8592a3',
          reverseButtons: true,
          focusConfirm: true,
        }).then((result) => {
          if (result.isConfirmed) {
            // Buka tab baru halaman cetak kartu
            window.open(url, '_blank');

            // Kirim konfirmasi ke server via fetch
            fetch('{{ route("siswa.konfirmasi-cetak-kartu") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              }
            }).then(() => {
              window.location.reload();
            }).catch(() => {
              window.location.reload();
            });
          }
        });
      } else {
        // Jika sudah pernah cetak sebelumnya, langsung buka cetak kartu
        window.open(url, '_blank');
      }
    }

    // Toggle sidebar mobile mandiri & terisolasi
    (function() {
      function closeMobileMenu() {
        document.documentElement.classList.remove('layout-menu-expanded');
        document.body.classList.remove('layout-menu-expanded');
      }

      function openMobileMenu() {
        document.documentElement.classList.add('layout-menu-expanded');
        document.body.classList.add('layout-menu-expanded');
      }

      closeMobileMenu();

      document.addEventListener('DOMContentLoaded', function() {
        closeMobileMenu();

        const menuToggle = document.querySelector('.layout-navbar .layout-menu-toggle');
        const layoutOverlay = document.querySelector('.layout-overlay');

        if (menuToggle) {
          menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (document.documentElement.classList.contains('layout-menu-expanded') || document.body.classList.contains('layout-menu-expanded')) {
              closeMobileMenu();
            } else {
              openMobileMenu();
            }
          });
        }

        if (layoutOverlay) {
          layoutOverlay.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeMobileMenu();
          });
          layoutOverlay.addEventListener('touchstart', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeMobileMenu();
          }, { passive: false });
        }

        window.addEventListener('resize', function() {
          if (window.innerWidth >= 1200) {
            closeMobileMenu();
          }
        });
      });
    })();
  </script>
</body>
</html>