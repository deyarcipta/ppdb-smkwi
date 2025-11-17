<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Dashboard Siswa')</title>

  <!-- Sneat CSS -->
  <link rel="stylesheet" href="{{ asset('sneat/vendor/css/core.css') }}">
  <link rel="stylesheet" href="{{ asset('sneat/vendor/css/theme-default.css') }}">
  <link rel="stylesheet" href="{{ asset('sneat/css/demo.css') }}">

  <!-- Fonts & Icons -->
  <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/iconify-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/fontawesome.css') }}">
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

    // Toggle sidebar mobile
    document.addEventListener('DOMContentLoaded', function() {
      const menuToggle = document.querySelector('.layout-menu-toggle');
      const layoutOverlay = document.querySelector('.layout-overlay');
      
      if (menuToggle && layoutOverlay) {
        menuToggle.addEventListener('click', function() {
          document.body.classList.toggle('layout-menu-expanded');
        });
        
        layoutOverlay.addEventListener('click', function() {
          document.body.classList.remove('layout-menu-expanded');
        });
      }
    });
  </script>
</body>
</html>