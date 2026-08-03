{{-- resources/views/admin/login.blade.php --}}
<!DOCTYPE html>
<html lang="id" dir="ltr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    @php
      $pengaturan = \App\Models\PengaturanAplikasi::first();
      $logo = $pengaturan->logo ?? 'sneat/img/logowi.png';
      $namaAplikasi = $pengaturan->nama_aplikasi ?? 'PPDB';
      $namaSekolah = $pengaturan->nama_sekolah ?? 'SMK Wisata Indonesia';
    @endphp
    <title>Login Admin | {{ $namaAplikasi }} - {{ $namaSekolah }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset($logo) }}" />

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- Boxicons -->
    <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/iconify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('sneat/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/vendor/css/theme-default.css') }}" />

    <style>
        :root {
            --primary-color: #6366f1;
            --primary-hover: #4f46e5;
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #4338ca 100%);
            --glow-color: rgba(99, 102, 241, 0.4);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0b0f19;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            padding: 1.5rem;
        }

        /* Dynamic Animated Mesh & Orbs Background */
        .bg-mesh {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: 
                radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.25) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(168, 85, 247, 0.22) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(14, 165, 233, 0.15) 0%, transparent 50%),
                #0b0f19;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.55;
            animation: floatOrb 18s infinite ease-in-out alternate;
        }

        .orb-1 {
            width: 420px;
            height: 420px;
            background: #6366f1;
            top: -120px;
            left: -100px;
        }

        .orb-2 {
            width: 480px;
            height: 480px;
            background: #a855f7;
            bottom: -150px;
            right: -100px;
            animation-delay: -6s;
        }

        .orb-3 {
            width: 320px;
            height: 320px;
            background: #0ea5e9;
            top: 40%;
            left: 55%;
            animation-delay: -12s;
        }

        @keyframes floatOrb {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(50px, -50px) scale(1.08); }
            100% { transform: translate(-40px, 30px) scale(0.95); }
        }

        /* Glassmorphism Centered Card */
        .glass-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-radius: 28px;
            padding: 3rem 2.5rem;
            box-shadow: 
                0 30px 60px -12px rgba(0, 0, 0, 0.45),
                0 0 0 1px rgba(255, 255, 255, 0.6) inset,
                0 0 40px rgba(99, 102, 241, 0.15);
            transition: transform 0.3s ease;
        }

        /* Header Branding */
        .brand-wrapper {
            text-align: center;
            margin-bottom: 2rem;
        }

        .badge-secure {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: rgba(99, 102, 241, 0.1);
            color: #4f46e5;
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 50px;
            font-size: 0.775rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            margin-bottom: 1.25rem;
        }

        .logo-container {
            width: 76px;
            height: 76px;
            margin: 0 auto 1.25rem auto;
            border-radius: 22px;
            background: #ffffff;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.25), 0 0 0 1px rgba(226, 232, 240, 0.8);
        }

        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .title-main {
            font-size: 1.65rem;
            font-weight: 800;
            color: #0f172a !important;
            margin: 0 0 6px 0;
            letter-spacing: -0.4px;
        }

        .subtitle-main {
            font-size: 0.875rem;
            color: #64748b;
            margin: 0;
            font-weight: 500;
            line-height: 1.5;
        }

        /* Form Controls */
        .form-group-custom {
            margin-bottom: 1.25rem;
        }

        .form-label-custom {
            display: block;
            font-size: 0.825rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon-left {
            position: absolute;
            left: 14px;
            font-size: 1.25rem;
            color: #94a3b8;
            pointer-events: none;
            transition: color 0.2s ease;
            z-index: 5;
        }

        .form-input-custom {
            width: 100%;
            padding: 13px 16px 13px 44px;
            font-size: 0.925rem;
            font-weight: 500;
            color: #0f172a;
            background-color: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            transition: all 0.25s ease;
        }

        .form-input-custom:focus {
            outline: none;
            background-color: #ffffff;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        }

        .form-input-custom:focus + .input-icon-left,
        .input-wrapper:focus-within .input-icon-left {
            color: #6366f1;
        }

        .btn-toggle-pwd {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s ease;
            z-index: 5;
        }

        .btn-toggle-pwd:hover {
            color: #475569;
            background-color: #f1f5f9;
        }

        /* Checkbox & Options */
        .options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.75rem;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: #475569;
            font-weight: 500;
            cursor: pointer;
            user-select: none;
        }

        .remember-checkbox {
            width: 17px;
            height: 17px;
            accent-color: #6366f1;
            cursor: pointer;
            border-radius: 4px;
        }

        .link-forgot {
            font-size: 0.85rem;
            font-weight: 600;
            color: #6366f1;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .link-forgot:hover {
            color: #4338ca;
            text-decoration: underline;
        }

        /* Submit Button */
        .btn-submit-main {
            width: 100%;
            padding: 14px;
            font-size: 0.95rem;
            font-weight: 700;
            color: #ffffff;
            background: var(--primary-gradient);
            border: none;
            border-radius: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.45);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-submit-main:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px -5px rgba(99, 102, 241, 0.55);
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
        }

        .btn-submit-main:active {
            transform: translateY(0);
        }

        /* Alerts */
        .alert-custom {
            padding: 12px 16px;
            border-radius: 14px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Footer Note */
        .card-footer-note {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.25rem;
            border-top: 1px solid #f1f5f9;
            font-size: 0.8rem;
            color: #94a3b8;
        }
    </style>
</head>

<body>
    <!-- Background Mesh & Orbs -->
    <div class="bg-mesh">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- Centered Glass Card -->
    <div class="glass-card">
        
        <!-- Header -->
        <div class="brand-wrapper">
            <div class="badge-secure">
                <i class="bx bx-shield-quarter"></i> PORTAL ADMIN PPDB
            </div>
            <div class="logo-container">
                <img src="{{ asset($logo) }}" alt="Logo {{ $namaAplikasi }}">
            </div>
            <h1 class="title-main">Selamat Datang 👋</h1>
            <p class="subtitle-main">Masuk untuk mengelola sistem {{ $namaAplikasi }}<br><strong>{{ $namaSekolah }}</strong></p>
        </div>

        {{-- Pesan Error Session --}}
        @if (session('error'))
            <div class="alert-custom">
                <i class="bx bx-error-circle fs-5"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Validasi Error Validation --}}
        @if ($errors->any())
            <div class="alert-custom">
                <i class="bx bx-error-circle fs-5"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Form Login -->
        <form method="POST" action="{{ route('backend.login.submit') }}">
            @csrf

            <!-- Email Field -->
            <div class="form-group-custom">
                <label for="email" class="form-label-custom">Email Administrator</label>
                <div class="input-wrapper">
                    <i class="bx bx-envelope input-icon-left"></i>
                    <input type="email"
                           class="form-input-custom"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="admin@sekolah.sch.id"
                           required 
                           autofocus />
                </div>
            </div>

            <!-- Password Field -->
            <div class="form-group-custom">
                <label for="password" class="form-label-custom">Kata Sandi</label>
                <div class="input-wrapper">
                    <i class="bx bx-lock-alt input-icon-left"></i>
                    <input type="password"
                           id="password"
                           class="form-input-custom"
                           name="password"
                           placeholder="••••••••"
                           required />
                    <button type="button" class="btn-toggle-pwd" id="togglePasswordBtn" title="Tampilkan kata sandi">
                        <i class="bx bx-hide" id="pwdIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Options Row -->
            <div class="options-row">
                <label class="remember-label" for="remember-me">
                    <input class="remember-checkbox" type="checkbox" id="remember-me" name="remember" />
                    <span>Ingat saya</span>
                </label>
                <a href="javascript:void(0);" onclick="alert('Silakan hubungi Superadmin / Tim IT untuk mereset password akun Anda.')" class="link-forgot">Lupa password?</a>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit-main">
                <span>Masuk ke Dashboard</span>
                <i class="bx bx-right-arrow-alt fs-4"></i>
            </button>
        </form>

        <!-- Footer Info -->
        <div class="card-footer-note">
            &copy; {{ date('Y') }} {{ $namaSekolah }} &bull; PPDB Digital System
        </div>

    </div>

    <!-- Toggle Password Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const pwdInput = document.getElementById('password');
            const pwdIcon = document.getElementById('pwdIcon');

            if (toggleBtn && pwdInput) {
                toggleBtn.addEventListener('click', function() {
                    const isPassword = pwdInput.type === 'password';
                    pwdInput.type = isPassword ? 'text' : 'password';
                    
                    if (isPassword) {
                        pwdIcon.className = 'bx bx-show';
                        toggleBtn.setAttribute('title', 'Sembunyikan kata sandi');
                    } else {
                        pwdIcon.className = 'bx bx-hide';
                        toggleBtn.setAttribute('title', 'Tampilkan kata sandi');
                    }
                    pwdInput.focus();
                });
            }
        });
    </script>
</body>
</html>