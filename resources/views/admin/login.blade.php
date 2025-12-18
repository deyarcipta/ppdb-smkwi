{{-- resources/views/admin/login.blade.php --}}
<!DOCTYPE html>
<html lang="id" class="light-style customizer-hide" dir="ltr"
      data-theme="theme-default"
      data-sneat-path="{{ asset('sneat/') }}/"
      data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    @php
      $pengaturan = \App\Models\PengaturanAplikasi::first();
      $logo = $pengaturan->logo ?? 'sneat/img/logowi.png';
      $namaAplikasi = $pengaturan->nama_aplikasi ?? 'PPDB SMK WI';
    @endphp
    <title>Login Admin - PPDB SMK Wisata Indonesia</title>

    <link rel="icon" type="image/x-icon" href="{{ asset($logo) }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700&display=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/iconify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/flag-icons.css') }}">

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/vendor/css/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('sneat/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('sneat/js/config.js') }}"></script>
    
    <!-- Style untuk toggle password -->
    <style>
    .toggle-password {
        cursor: pointer;
        user-select: none;
    }

    .toggle-password:hover {
        background-color: #f8f9fa;
        border-color: #d9dee3;
    }

    .toggle-password:active {
        background-color: #e9ecef;
    }
    </style>
</head>

<body>
    <!-- Content -->
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">

                <!-- Login Card -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4">
                            <a href="{{ url('/') }}" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">
                                    <img src="{{ asset($logo) }}" alt="Logo" width="30">
                                </span>
                                <span class="app-brand-text demo text-body fw-bold">PPDB Wisata Indonesia</span>
                            </a>
                        </div>
                        <!-- /Logo -->

                        <h4 class="mb-1 text-center">Selamat Datang 👋</h4>
                        <p class="mb-4 text-center">Silakan masuk ke sistem PPDB Anda</p>

                        {{-- Pesan Error --}}
                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        {{-- Validasi Error --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Form Login -->
                        <form method="POST" action="{{ route('backend.login.submit') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email"
                                       class="form-control"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       placeholder="admin@ppdb.sch.id"
                                       required autofocus />
                            </div>

                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password"
                                        id="password"
                                        class="form-control"
                                        name="password"
                                        placeholder="••••••••"
                                        required />
                                    <span class="input-group-text cursor-pointer toggle-password" 
                                        >
                                        <i class="bx bx-low-vision" data-target="password"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember-me" name="remember" />
                                    <label class="form-check-label" for="remember-me">Ingat saya</label>
                                </div>
                                <a href="#" class="text-primary">Lupa password?</a>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary d-grid w-100">
                                    Masuk
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /Login Card -->

            </div>
        </div>
    </div>

    <!-- / Content -->

    <!-- Core JS -->
    <script src="{{ asset('sneat/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('sneat/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('sneat/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('sneat/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('sneat/js/main.js') }}"></script>
    
    <!-- JavaScript untuk toggle password -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk toggle password visibility
        function togglePasswordVisibility(button) {
            const targetId = button.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const iconElement = button.querySelector('i');
            
            if (!passwordInput) {
                console.error('Password input not found with ID:', targetId);
                return;
            }
            
            // Toggle type password/text
            const currentType = passwordInput.getAttribute('type');
            const newType = currentType === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', newType);
            
            console.log('Toggling password input type from', currentType, 'to', newType);
            
            // Toggle icon - coba beberapa kemungkinan icon
            if (newType === 'text') {
                // Password sekarang terlihat
                if (iconElement.classList.contains('bx-low-vision')) {
                    iconElement.classList.remove('bx-low-vision');
                    iconElement.classList.add('bx-show');
                } else if (iconElement.classList.contains('bx-lock')) {
                    iconElement.classList.remove('bx-lock');
                    iconElement.classList.add('bx-lock-open');
                } else if (iconElement.classList.contains('fa-eye-slash')) {
                    iconElement.classList.remove('fa-eye-slash');
                    iconElement.classList.add('fa-eye');
                }
                button.setAttribute('title', 'Sembunyikan password');
            } else {
                // Password sekarang tersembunyi
                if (iconElement.classList.contains('bx-show')) {
                    iconElement.classList.remove('bx-show');
                    iconElement.classList.add('bx-low-vision');
                } else if (iconElement.classList.contains('bx-lock-open')) {
                    iconElement.classList.remove('bx-lock-open');
                    iconElement.classList.add('bx-lock');
                } else if (iconElement.classList.contains('fa-eye')) {
                    iconElement.classList.remove('fa-eye');
                    iconElement.classList.add('fa-eye-slash');
                }
                button.setAttribute('title', 'Tampilkan password');
            }
            
            // Fokus kembali ke input setelah toggle
            setTimeout(() => {
                passwordInput.focus();
            }, 10);
        }
        
        // Event listener untuk semua toggle buttons
        document.querySelectorAll('.toggle-password').forEach(function(button) {
            // Set initial title
            button.setAttribute('title', 'Tampilkan password');
            button.setAttribute('aria-label', 'Tampilkan password');
            button.setAttribute('role', 'button');
            button.setAttribute('tabindex', '0');
            
            // Click event
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                togglePasswordVisibility(this);
            });
            
            // Keyboard support
            button.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ' || e.key === 'Spacebar') {
                    e.preventDefault();
                    togglePasswordVisibility(this);
                }
            });
        });
        
        // Debug: Log untuk memastikan script berjalan
        console.log('Password toggle script loaded');
        console.log('Toggle password buttons found:', document.querySelectorAll('.toggle-password').length);
    });
    </script>
</body>
</html>