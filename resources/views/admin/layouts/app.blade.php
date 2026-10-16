<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @php
      $pengaturan = \App\Models\PengaturanAplikasi::first();
      $logo = $pengaturan->logo ?? 'sneat/img/logowi.png';
      $namaAplikasi = $pengaturan->nama_aplikasi ?? 'PPDB SMK WI';
      $tipeAplikasi = $pengaturan->tipe_aplikasi ?? 'PPDB';
      $tahunAjaranAktif = $pengaturan ? $pengaturan->tahunAjaranAktif : \App\Models\TahunAjaran::where('status', 'aktif')->first();
  @endphp
  <link rel="icon" href="{{ asset($logo) }}" type="image/png">
  <title>@yield('title', 'Dashboard Admin') - {{ $namaAplikasi }}{{ $tahunAjaranAktif ? ' (T.A '.$tahunAjaranAktif->nama.')' : '' }}</title>

  <!-- Sneat CSS -->
  <link rel="stylesheet" href="{{ asset('sneat/vendor/css/core.css') }}">
  <link rel="stylesheet" href="{{ asset('sneat/vendor/css/theme-default.css') }}">
  <link rel="stylesheet" href="{{ asset('sneat/css/demo.css') }}">

  <!-- Fonts & Icons -->
  <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/iconify-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/fontawesome.css') }}">
  <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/flag-icons.css') }}">

  <!-- Helpers -->
  <script src="{{ asset('sneat/vendor/js/helpers.js') }}"></script>

  <style>
    /* ======== 1. DESKTOP LAYOUT (Layar >= 1200px) ======== */
    @media (min-width: 1200px) {
      html, body {
        height: 100%;
        overflow: hidden; /* Mencegah double scrollbar di desktop */
      }

      .layout-wrapper {
        height: 100vh;
        overflow: hidden;
      }

      .layout-container {
        height: 100%;
        overflow: hidden;
        display: flex;
      }

      .layout-menu {
        height: 100vh !important;
        display: flex !important;
        flex-direction: column !important;
        position: sticky !important;
        top: 0 !important;
        z-index: 1000;
        background-color: #fff !important;
        border-right: 1px solid #e4e6e8;
        width: 260px !important;
        flex-shrink: 0 !important;
      }

      .layout-page {
        height: 100%;
        overflow: hidden;
        flex: 1 1 auto;
        min-width: 0;
        display: flex !important;
        flex-direction: column !important;
      }

      .content-wrapper {
        flex: 1 1 auto !important;
        min-height: 0 !important;
        height: auto !important;
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
        display: flex !important;
        flex-direction: column !important;
      }

      .content-wrapper > .container-xxl {
        padding-left: 1.625rem !important;
        padding-right: 1.625rem !important;
        padding-inline: 1.625rem !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
      }
    }

    /* ======== 2. MOBILE & TABLET LAYOUT (< 1200px) ======== */
    @media (max-width: 1199.98px) {
      html, body {
        height: auto !important;
        min-height: 100% !important;
      }

      .layout-wrapper {
        height: auto !important;
        min-height: 100vh !important;
        display: block !important;
      }

      .layout-container {
        height: auto !important;
        min-height: 100vh !important;
        display: block !important;
        width: 100% !important;
      }

      /* Sidebar Off-canvas di HP/Tablet */
      .layout-menu {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        bottom: 0 !important;
        height: 100vh !important;
        max-height: 100vh !important;
        width: 260px !important;
        z-index: 1090 !important;
        transform: translate3d(-100%, 0, 0) !important;
        transition: transform 0.3s ease-in-out !important;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15) !important;
        background-color: #fff !important;
      }

      /* Ketika sidebar dibuka di mobile */
      html.layout-menu-expanded .layout-menu,
      body.layout-menu-expanded .layout-menu,
      .layout-menu-expanded .layout-menu {
        transform: translate3d(0, 0, 0) !important;
      }

      /* Overlay Backdrop Gelap: tersembunyi total saat tertutup */
      .layout-overlay {
        display: none !important;
        pointer-events: none !important;
        opacity: 0 !important;
      }

      /* Overlay hanya aktif & menangkap klik saat sidebar terbuka */
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

      /* Konten Utama mengambil 100% Lebar Layar HP */
      .layout-page {
        width: 100% !important;
        max-width: 100% !important;
        min-height: 100vh !important;
        height: auto !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        padding-inline-start: 0 !important;
        padding-inline-end: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        display: block !important;
        flex: 1 1 100% !important;
      }

      .content-wrapper {
        height: auto !important;
        width: 100% !important;
        max-width: 100% !important;
        display: flex !important;
        flex-direction: column !important;
        padding: 0 !important;
        margin: 0 !important;
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
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.07) !important;
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

      .main-content,
      .container-fluid,
      .container-fluid.p-0,
      .container-xxl .container-xxl,
      .container-xxl .container-fluid,
      .container-xxl .main-content {
        padding-left: 0 !important;
        padding-right: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
      }

      .content-wrapper .card,
      .container-xxl .card,
      .main-content .card {
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

      .layout-menu-toggle {
        display: flex !important;
        align-items: center;
        justify-content: center;
        cursor: pointer;
      }

      .table-responsive {
        -webkit-overflow-scrolling: touch;
        width: 100% !important;
        max-width: 100% !important;
        margin-bottom: 0.75rem !important;
      }

      .modal-dialog {
        margin: 0.5rem !important;
        max-width: calc(100% - 1rem) !important;
      }
    }

    /* ======== 3. GAYA UMUM (DESKTOP & MOBILE) ======== */
    .layout-menu .app-brand {
      flex-shrink: 0 !important;
      height: 64px !important;
    }

    /* Menu Inner responsif: pas dengan konten, tidak menyisakan ruang scroll kosong */
    .menu-inner {
      flex: 0 1 auto !important;
      height: auto !important;
      max-height: calc(100vh - 64px) !important;
      overflow-y: auto !important;
      overflow-x: hidden !important;
      scrollbar-width: thin;
      scrollbar-color: rgba(105, 108, 255, 0.25) transparent;
      padding-bottom: 0.5rem !important;
    }

    /* Scrollbar tipis modern & elegan */
    .menu-inner::-webkit-scrollbar {
      width: 4px;
    }
    .menu-inner::-webkit-scrollbar-track {
      background: transparent;
    }
    .menu-inner::-webkit-scrollbar-thumb {
      background: rgba(105, 108, 255, 0.2);
      border-radius: 4px;
    }
    .menu-inner::-webkit-scrollbar-thumb:hover {
      background: rgba(105, 108, 255, 0.45);
    }

    /* ======== PERBAIKAN INDIKATOR UNGU SIDEBAR ======== */
    /* Pastikan setiap menu item memiliki positioning relatif agar indikator ::before tidak melayang */
    .menu-vertical .menu-inner > .menu-item {
      position: relative !important;
    }

    /* Hilangkan garis indikator vertikal ungu di parent dropdown yang sedang terbuka */
    .layout-wrapper:not(.layout-horizontal) .menu-vertical .menu-inner > .menu-item.open::before,
    .layout-wrapper:not(.layout-horizontal) .menu-vertical .menu-inner > .menu-item:has(.menu-sub)::before {
      display: none !important;
    }

    /* Indikator vertikal ungu hanya untuk menu tunggal level 1 (bukan dropdown) yang aktif */
    .layout-wrapper:not(.layout-horizontal) .menu-vertical .menu-inner > .menu-item.active:not(.open)::before {
      content: "" !important;
      position: absolute !important;
      right: 0 !important;
      top: 50% !important;
      transform: translateY(-50%) !important;
      width: 4px !important;
      height: 2.5rem !important;
      background-color: #696cff !important;
      border-radius: 4px 0 0 4px !important;
      z-index: 5 !important;
    }

    /* Styling aktif untuk sub-menu item (seperti Template Pesan, Jurusan, dll) */
    .menu-vertical .menu-sub > .menu-item.active > .menu-link {
      background-color: rgba(105, 108, 255, 0.12) !important;
      color: #696cff !important;
      font-weight: 600 !important;
      border-radius: 0.375rem !important;
    }
    .menu-vertical .menu-sub > .menu-item.active > .menu-link::before {
      background-color: #696cff !important;
      box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.25) !important;
    }

    /* Scrollbar tipis elegan agar simetris & tidak memakan ruang kanan di desktop */
    .content-wrapper::-webkit-scrollbar {
      width: 6px;
    }
    .content-wrapper::-webkit-scrollbar-track {
      background: transparent;
    }
    .content-wrapper::-webkit-scrollbar-thumb {
      background: rgba(0, 0, 0, 0.18);
      border-radius: 10px;
    }
    .content-wrapper::-webkit-scrollbar-thumb:hover {
      background: rgba(0, 0, 0, 0.35);
    }

    /* Pastikan konten di dalam tidak menyebabkan scroll di luar */
    .container-xxl.flex-grow-1.container-p-y {
      min-height: auto;
      overflow: visible;
    }

    /* Responsivitas Tabel di Mobile */
    .table-responsive {
      -webkit-overflow-scrolling: touch;
      margin-bottom: 0.5rem;
    }

    /* ======== GAYA TABEL ADMIN (Perbesar Sedikit Seluruh Tabel Sesuai Gambar) ======== */
    .table:not(.table-borderless) {
      font-size: 0.9375rem;
    }

    .table:not(.table-borderless) thead th,
    .table thead.table-light th,
    .table thead th {
      font-size: 0.85rem !important;
      font-weight: 700 !important;
      background-color: #e9ecef !important;
      color: #495057 !important;
      padding: 0.65rem 0.75rem !important;
      vertical-align: middle !important;
      border-color: #dee2e6 !important;
      border-bottom: 2px solid #dee2e6 !important;
      letter-spacing: 0.3px;
    }

    .table:not(.table-borderless) tbody td,
    .table tbody td {
      font-size: 0.9375rem !important;
      padding: 0.65rem 0.75rem !important;
      vertical-align: middle !important;
      border-color: #dee2e6 !important;
    }

    .table-sm th,
    .table-sm td {
      padding: 0.65rem 0.75rem !important;
      font-size: 0.9375rem !important;
    }

    /* Footer persis seperti SIAWI: selalu di paling bawah dokumen/halaman */
    footer.footer,
    .content-footer {
      padding: 0.85rem 1.5rem !important;
      background-color: #fff !important;
      border-top: 1px solid #e4e6e8 !important;
      text-align: center;
      font-size: 0.85rem;
      color: #6c757d;
      flex-shrink: 0 !important;
      margin-top: auto !important;
      width: 100% !important;
      box-sizing: border-box !important;
    }

    .content-wrapper > .container-xxl,
    .container-xxl.flex-grow-1 {
      flex: 1 0 auto !important;
    }

    /* Konten utama */
    .container-p-y {
      padding-top: 1rem !important;
      padding-bottom: 0.25rem !important;
    }

    .card {
      margin-bottom: 0.75rem !important;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      border-radius: 0.75rem;
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
    .container-xxl .container-fluid,
    .container-xxl .main-content {
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
    .main-content .card,
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

    /* Perbaikan z-index Swal */
    .swal2-container {
      z-index: 9999 !important;
    }

    .swal2-backdrop-show {
      backdrop-filter: blur(3px);
    }

    /* Navbar */
    .layout-navbar {
      position: sticky;
      top: 0;
      z-index: 1040;
      flex-shrink: 0;
    }

    .layout-menu-toggle {
      cursor: pointer;
    }

    /* Pagination */
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

    /* Table spacing */
    .card-body {
      padding-bottom: 0.5rem;
    }

    .border-top {
      border-top: 1px solid #dee2e6 !important;
      margin-top: 1rem;
      padding-top: 1rem;
    }

    /* Logo */
    .app-brand-logo img {
      max-height: 40px;
      width: auto;
    }

    .navbar-brand img {
      max-height: 35px;
      width: auto;
    }

    /* Untuk mobile: pastikan overlay tidak menyebabkan scroll */
    .layout-overlay {
      overflow: hidden !important;
    }
  </style>
  @stack('styles')
</head>

<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

      {{-- Sidebar --}}
      @include('admin.partials.sidebar')

      <div class="layout-page">

        {{-- Navbar --}}
        @include('admin.partials.navbar')

        <div class="content-wrapper">

          <div class="container-xxl flex-grow-1 container-p-y">
            <div class="main-content">
              @yield('content')
            </div>
          </div>

          {{-- Footer --}}
          @include('admin.partials.footer')
        </div>
      </div>

    </div>

    <!-- Overlay untuk mobile sidebar (tanpa class layout-menu-toggle agar tidak terpicu klik tak sengaja) -->
    <div class="layout-overlay"></div>
  </div>

  <!-- Sneat Vendor JS -->
  <script src="{{ asset('sneat/vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('sneat/vendor/libs/popper/popper.js') }}"></script>
  <script src="{{ asset('sneat/vendor/js/bootstrap.js') }}"></script>
  <script src="{{ asset('sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('sneat/vendor/js/menu.js') }}"></script>
  <!-- Override Sneat scrollToActive agar posisi sidebar tidak terdorong ekstrem ke bawah -->
  <script>
    if ('scrollRestoration' in history) {
      history.scrollRestoration = 'manual';
    }

    if (window.Helpers) {
      window.Helpers.scrollToActive = function() {
        // Dinonaktifkan: agar sidebar tidak auto-scroll ke bawah dan tidak meninggalkan ruang kosong
      };
    }
  </script>

  <!-- Main Sneat JS -->
  <script src="{{ asset('sneat/js/main.js') }}"></script>

  <script>
    // Bersihkan PerfectScrollbar dan pertahankan fokus menu aktif di sidebar
    (function() {
      function cleanSidebarScroll() {
        if (window.Helpers && window.Helpers.menuPsScroll) {
          try {
            window.Helpers.menuPsScroll.destroy();
            window.Helpers.menuPsScroll = null;
          } catch(e) {}
        }
        const menuInner = document.querySelector('.menu-inner');
        if (menuInner) {
          menuInner.classList.remove('ps', 'ps--active-y', 'ps--scrolling-y');
          menuInner.querySelectorAll('.ps__rail-x, .ps__rail-y').forEach(el => el.remove());
        }
      }

      function focusActiveSidebarMenu() {
        const menuInner = document.querySelector('.menu-inner');
        if (!menuInner) return;

        // Cari item aktif paling spesifik (misal sub-item yang active)
        const activeItems = menuInner.querySelectorAll('.menu-item.active');
        const targetItem = activeItems.length > 0 ? activeItems[activeItems.length - 1] : null;

        if (targetItem) {
          // Cek posisi terhadap viewport menuInner
          const innerRect = menuInner.getBoundingClientRect();
          const itemRect = targetItem.getBoundingClientRect();

          if (itemRect.top < innerRect.top + 40 || itemRect.bottom > innerRect.bottom - 40) {
            targetItem.scrollIntoView({ block: 'center', behavior: 'auto' });
          }
        } else {
          const savedScroll = sessionStorage.getItem('adminSidebarScroll');
          if (savedScroll !== null) {
            menuInner.scrollTop = parseInt(savedScroll, 10) || 0;
          }
        }
      }

      cleanSidebarScroll();

      document.addEventListener('DOMContentLoaded', function() {
        cleanSidebarScroll();
        const menuInner = document.querySelector('.menu-inner');
        if (menuInner) {
          menuInner.addEventListener('scroll', function() {
            sessionStorage.setItem('adminSidebarScroll', menuInner.scrollTop);
          }, { passive: true });
        }
        setTimeout(focusActiveSidebarMenu, 50);
        setTimeout(focusActiveSidebarMenu, 200);
      });

      // Simpan scroll saat klik link menu di sidebar
      document.addEventListener('click', function(e) {
        const link = e.target.closest('.menu-inner a');
        if (link) {
          const menuInner = document.querySelector('.menu-inner');
          if (menuInner) {
            sessionStorage.setItem('adminSidebarScroll', menuInner.scrollTop);
          }
        }
      });

      window.addEventListener('load', function() {
        cleanSidebarScroll();
        focusActiveSidebarMenu();
      });
    })();
  </script>

  <script>
    // Handler toggle mobile drawer dan overlay mandiri & terisolasi
    (function() {
      function closeMobileMenu() {
        document.documentElement.classList.remove('layout-menu-expanded');
        document.body.classList.remove('layout-menu-expanded');
      }

      function openMobileMenu() {
        document.documentElement.classList.add('layout-menu-expanded');
        document.body.classList.add('layout-menu-expanded');
      }

      // Pastikan saat awal halaman dimuat, menu SELALU tertutup sehingga overlay tidak menutupi layar
      closeMobileMenu();

      document.addEventListener('DOMContentLoaded', function() {
        closeMobileMenu();

        // Handler klik tombol hamburger di navbar
        const navbarToggle = document.querySelector('.layout-navbar .layout-menu-toggle');
        if (navbarToggle) {
          navbarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (document.documentElement.classList.contains('layout-menu-expanded')) {
              closeMobileMenu();
            } else {
              openMobileMenu();
            }
          });
        }

        // Handler klik overlay backdrop (klik area luar untuk menutup sidebar)
        const overlay = document.querySelector('.layout-overlay');
        if (overlay) {
          overlay.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeMobileMenu();
          });
          overlay.addEventListener('touchstart', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeMobileMenu();
          }, { passive: false });
        }

        // Tutup menu saat resize ke layar desktop
        window.addEventListener('resize', function() {
          if (window.innerWidth >= 1200) {
            closeMobileMenu();
          }
        });
      });
    })();
  </script>

  <!-- SweetAlert2 (menggunakan CDN resmi agar versi terbaru selalu terpakai) -->
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
  </script>
</body>
</html>