<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
    /* ======== PERBAIKAN SCROLL SIDEBAR DROPDOWN ======== */
    html, body {
      height: 100%;
      overflow: hidden; /* Mencegah scroll di body */
    }

    .layout-wrapper {
      height: 100vh;
      overflow: hidden; /* Mencegah scroll di wrapper utama */
    }

    .layout-container {
      height: 100%;
      overflow: hidden; /* Mencegah scroll di container */
    }

    /* Sidebar TANPA scrollbar visible */
    .layout-menu {
      height: 100vh !important;
      overflow-y: hidden !important; /* Ubah dari auto menjadi hidden */
      overflow-x: hidden !important;
    }

    /* Menu inner tanpa scrollbar */
    .menu-inner {
      overflow-y: hidden !important;
      overflow-x: hidden !important;
    }

    /* Konten utama bisa discroll jika konten panjang */
    .content-wrapper {
      height: 100%;
      overflow-y: auto;
      overflow-x: hidden;
    }

    /* Hilangkan scrollbar dari browser */
    .layout-menu::-webkit-scrollbar {
      display: none !important;
      width: 0 !important;
      height: 0 !important;
    }

    .layout-menu {
      -ms-overflow-style: none !important;  /* IE and Edge */
      scrollbar-width: none !important;  /* Firefox */
    }

    .menu-inner::-webkit-scrollbar {
      display: none !important;
      width: 0 !important;
      height: 0 !important;
    }

    .menu-inner {
      -ms-overflow-style: none !important;  /* IE and Edge */
      scrollbar-width: none !important;  /* Firefox */
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

    <!-- Overlay untuk mobile sidebar -->
    <div class="layout-overlay layout-menu-toggle"></div>
  </div>

  <!-- Sneat Vendor JS -->
  <script src="{{ asset('sneat/vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('sneat/vendor/libs/popper/popper.js') }}"></script>
  <script src="{{ asset('sneat/vendor/js/bootstrap.js') }}"></script>
  <script src="{{ asset('sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('sneat/vendor/js/menu.js') }}"></script>

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