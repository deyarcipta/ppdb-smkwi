{{-- resources/views/siswa/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="id" class="light-style customizer-hide" dir="ltr"
      data-theme="theme-default"
      data-sneat-path="{{ asset('sneat/') }}/"
      data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Login Siswa - PPDB SMK Wizata Indonesia</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('sneat/img/logowi.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700&display=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/iconify-icons.css') }}" />
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/vendor/css/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/vendor/css/pages/page-auth.css') }}" />

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Helpers -->
    <script src="{{ asset('sneat/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('sneat/js/config.js') }}"></script>

    <style>
        .login-siswa {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card-siswa {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: none;
            overflow: hidden;
        }
        .login-header-siswa {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            border-radius: 15px 15px 0 0;
        }
        .school-logo {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            padding: 10px;
            border: 3px solid rgba(255,255,255,0.3);
        }
        .school-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }
        .btn-login-siswa {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 8px;
            color: white;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-login-siswa:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .input-group-text {
            cursor: pointer;
            background-color: #f8f9fa;
            border: 1px solid #d9dee3;
            transition: all 0.3s ease;
        }
        .input-group-text:hover {
            background-color: #e9ecef;
        }
    </style>
</head>

<body class="login-siswa">
    <!-- Content -->
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">

                <!-- Login Card -->
                <div class="card login-card-siswa">
                    <!-- Header dengan gradient -->
                    <div class="login-header-siswa">
                        <div class="school-logo">
                            <img src="{{ asset('sneat/img/logowi.png') }}" alt="Logo SMK Wizata Indonesia">
                        </div>
                        <h4 class="mb-2">PPDB SMK Wizata Indonesia</h4>
                        <p class="mb-0">Area Login Siswa</p>
                    </div>

                    <div class="card-body p-4">
                        <h5 class="mb-3 text-center">Masuk ke Akun Anda</h5>

                        {{-- Pesan Error untuk username/password salah --}}
                        @if ($errors->has('username') && !session('account_inactive'))
                            <div class="alert alert-danger" role="alert">
                                <i class="bx bx-error-circle me-2"></i>
                                {{ $errors->first('username') }}
                            </div>
                        @endif

                        {{-- Validasi Error lainnya --}}
                        @if ($errors->any() && !$errors->has('username') && !session('account_inactive'))
                            <div class="alert alert-danger">
                                <i class="bx bx-error-circle me-2"></i>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Pesan Success --}}
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                <i class="bx bx-check-circle me-2"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Form Login -->
                        <form method="POST" action="{{ route('siswa.login.submit') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="bx bx-user me-2"></i>Username
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="username"
                                       name="username"
                                       value="{{ old('username') }}"
                                       placeholder="Masukkan username Anda"
                                       required autofocus />
                                <div class="form-text">
                                    Gunakan username yang diberikan saat pendaftaran
                                </div>
                            </div>

                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="password">
                                    <i class="bx bx-lock me-2"></i>Password
                                </label>
                                <div class="input-group input-group-merge">
                                    <input type="password"
                                           id="password"
                                           class="form-control"
                                           name="password"
                                           placeholder="Masukkan password Anda"
                                           required />
                                    <span class="input-group-text cursor-pointer" id="togglePassword">
                                        <i class="bx bx-hide"></i>
                                    </span>
                                </div>
                                <div class="form-text">
                                    Password default: <code>password123</code> (jika belum diubah)
                                </div>
                            </div>

                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember-me" name="remember" />
                                    <label class="form-check-label" for="remember-me">Ingat saya</label>
                                </div>
                                <a href="#" class="text-primary small">
                                    <i class="bx bx-help-circle me-1"></i>Lupa password?
                                </a>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-login-siswa">
                                    <i class="bx bx-log-in me-2"></i>Masuk ke Dashboard
                                </button>
                            </div>
                        </form>

                        <!-- Contact Info -->
                        <div class="text-center mt-4">
                            <p class="text-muted mb-2 small">Butuh bantuan?</p>
                            <div class="d-flex justify-content-center">
                                <a href="#" class="text-decoration-none small">
                                    <i class="bx bx-phone me-1"></i>Hubungi Panitia PPDB
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer text-center py-3">
                        <small class="text-muted">
                            &copy; {{ date('Y') }} SMK Wizata Indonesia. All rights reserved.
                        </small>
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

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <script>
    // Toggle visibility password - FIXED VERSION
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        
        if (togglePassword && password) {
            togglePassword.addEventListener('click', function() {
                // Toggle the type attribute
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                // Toggle the icon
                const eyeIcon = this.querySelector('i');
                if (eyeIcon) {
                    eyeIcon.classList.toggle('bx-hide');
                    eyeIcon.classList.toggle('bx-show');
                }
            });
        }

        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // SweetAlert untuk akun tidak aktif
        @if(session('account_inactive'))
            Swal.fire({
                icon: 'warning',
                title: 'Akun Tidak Aktif',
                text: '{{ session("account_inactive") }}',
                confirmButtonColor: '#667eea',
                confirmButtonText: 'Mengerti'
            });
        @endif
    });
</script>
</body>
</html>