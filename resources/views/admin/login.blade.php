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
    <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/boxicons.css') }}" />

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
                                    <span class="input-group-text cursor-pointer">
                                        <i class="bx bx-hide"></i>
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

                        <p class="text-center">
                            <span>Belum punya akun?</span>
                            <a href="#" class="text-primary">Daftar di sini</a>
                        </p>
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
</body>
</html>
