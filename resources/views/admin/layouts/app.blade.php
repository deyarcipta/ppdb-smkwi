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
  @endphp
  <link rel="icon" href="{{ asset($logo) }}" type="image/png">
  <title>@yield('title', 'Dashboard Admin') - {{ $namaAplikasi }}</title>

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
    /* ======== LAYOUT DASAR ======== */
    html, body {
      height: 100%;
      overflow: hidden; /* Mencegah double scrollbar di body */
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

    /* ======== SIDEBAR RESPONSIF & ELEGAN ======== */
    .layout-menu {
      height: 100vh !important;
      display: flex !important;
      flex-direction: column !important;
      position: sticky !important;
      top: 0 !important;
      z-index: 1000;
      background-color: #fff !important;
    }

    .layout-menu .app-brand {
      flex-shrink: 0 !important;
      height: 64px !important;
    }

    /* Menu Inner responsif: bisa di-scroll dengan mouse wheel/touch, tidak terpotong */
    .menu-inner {
      flex: 1 1 auto !important;
      max-height: calc(100vh - 64px) !important;
      overflow-y: auto !important;
      overflow-x: hidden !important;
      scrollbar-width: thin;
      scrollbar-color: rgba(105, 108, 255, 0.25) transparent;
      padding-bottom: 0.75rem !important;
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

    /* Konten utama bisa discroll jika konten panjang */
    .content-wrapper {
      height: 100%;
      overflow-y: auto;
      overflow-x: hidden;
      scrollbar-width: thin;
      scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
    }

    /* Scrollbar tipis elegan agar simetris & tidak memakan ruang kanan */
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

    /* Hapus overflow dari halaman utama */
    .layout-page {
      height: 100%;
      overflow: hidden;
    }

    /* Footer tetap di bawah */
    footer.footer {
      padding: 0.75rem 0;
      background-color: #fff;
      border-top: 1px solid #e4e6e8;
      text-align: center;
      font-size: 0.9rem;
      color: #6c757d;
      flex-shrink: 0;
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
</head>

<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

      {{-- Sidebar --}}
      @include('admin.partials.sidebar')

      <div class="layout-page">

        <div class="content-wrapper">
          {{-- Navbar --}}
          @include('admin.partials.navbar')

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

    <!-- Overlay untuk mobile sidebar -->
    <div class="layout-overlay layout-menu-toggle"></div>
  </div>

  <!-- Sneat Vendor JS -->
  <script src="{{ asset('sneat/vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('sneat/vendor/libs/popper/popper.js') }}"></script>
  <script src="{{ asset('sneat/vendor/js/bootstrap.js') }}"></script>
  <script src="{{ asset('sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('sneat/vendor/js/menu.js') }}"></script>
  <!-- Override Sneat scrollToActive agar posisi sidebar tidak terdorong ekstrem ke tengah -->
  <script>
    if (window.Helpers) {
      window.Helpers.scrollToActive = function() {
        const layoutMenu = document.querySelector('#layout-menu');
        if (!layoutMenu) return;
        const menuInner = layoutMenu.querySelector('.menu-inner');
        if (!menuInner) return;
        const activeItem = menuInner.querySelector('.menu-item.active');
        if (activeItem) {
          const rect = activeItem.getBoundingClientRect();
          const innerRect = menuInner.getBoundingClientRect();
          // Hanya scroll bila posisi item benar-benar terpotong di luar area pandang sidebar
          if (rect.top < innerRect.top || rect.bottom > innerRect.bottom) {
            activeItem.scrollIntoView({ block: 'nearest', behavior: 'auto' });
          }
        }
      };
    }
  </script>

  <!-- Main Sneat JS -->
  <script src="{{ asset('sneat/js/main.js') }}"></script>

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