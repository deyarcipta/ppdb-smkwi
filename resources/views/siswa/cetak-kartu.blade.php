@php
    $pengaturan = $pengaturan ?? \App\Models\PengaturanAplikasi::getSettings();
    $tipeAplikasi = $pengaturan->tipe_aplikasi ?? 'PPDB';
    $namaPanjang = $pengaturan->namaPanjangAplikasi;
    $tahunAjaranPeserta = $dataSiswa->tahunAjaran?->nama 
        ?? $dataSiswa->gelombang?->tahunAjaran?->nama 
        ?? $pengaturan->tahunAjaranAktif?->nama 
        ?? date('Y') . '/' . (date('Y') + 1);

    // Resolusi path logo yang aman tanpa duplikasi /storage/
    $logoUrl = null;
    if (!empty($pengaturan->logo)) {
        $cleanLogo = ltrim(preg_replace('/^storage\//', '', $pengaturan->logo), '/');
        if (file_exists(public_path('storage/' . $cleanLogo))) {
            $logoUrl = asset('storage/' . $cleanLogo);
        } else {
            $logoUrl = asset($pengaturan->logo);
        }
    } else {
        $logoUrl = asset('sneat/img/logowi.png');
    }

    // Resolusi path foto siswa yang aman
    $fotoPath = null;
    if (!empty($dataSiswa->foto_siswa)) {
        $cleanFoto = ltrim(preg_replace('/^storage\//', '', $dataSiswa->foto_siswa), '/');
        if (file_exists(public_path('storage/' . $cleanFoto))) {
            $fotoPath = asset('storage/' . $cleanFoto);
        } elseif (file_exists(public_path($dataSiswa->foto_siswa))) {
            $fotoPath = asset($dataSiswa->foto_siswa);
        } else {
            $fotoPath = asset('storage/' . $cleanFoto);
        }
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Peserta & Ceklis Berkas {{ $tipeAplikasi }} - {{ $dataSiswa->nama_lengkap }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/boxicons.css') }}" />
    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
            color: #222;
            -webkit-font-smoothing: antialiased;
        }

        .print-btn-container {
            max-width: 210mm;
            margin: 0 auto 14px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .btn-back {
            text-decoration: none;
            color: #495057;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 6px;
            background: #ffffff;
            border: 1px solid #ced4da;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-back:hover {
            background-color: #f8f9fa;
            color: #212529;
            border-color: #adb5bd;
        }

        .view-mode-group {
            display: inline-flex;
            align-items: center;
            background: #e9ecef;
            padding: 3px;
            border-radius: 8px;
            gap: 3px;
        }

        .view-mode-btn {
            border: none;
            background: transparent;
            padding: 6px 13px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #495057;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s ease;
        }

        .view-mode-btn.active {
            background: #ffffff;
            color: #696cff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12);
        }

        .btn-print {
            background-color: #696cff;
            color: white;
            border: none;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 3px 8px rgba(105, 108, 255, 0.35);
            transition: all 0.2s ease;
        }

        .btn-print:hover {
            background-color: #595cd9;
            transform: translateY(-1px);
        }

        .mobile-notice {
            max-width: 210mm;
            margin: 0 auto 12px auto;
            background: #e8f0fe;
            border: 1px solid #cce0ff;
            border-radius: 8px;
            padding: 10px 14px;
            display: none;
            align-items: center;
            gap: 10px;
            font-size: 8.5pt;
            color: #1a73e8;
            box-shadow: 0 1px 3px rgba(26, 115, 232, 0.08);
        }

        .mobile-notice i {
            font-size: 20px;
            flex-shrink: 0;
            color: #1a73e8;
        }

        /* Container Viewport & A4 Utama */
        .a4-viewport-container {
            width: 100%;
            max-width: 100%;
            overflow: hidden;
            position: relative;
        }

        .a4-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-bottom: 30px;
            transition: height 0.25s ease;
        }

        .a4-page {
            width: 210mm;
            min-width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            padding: 9mm 11mm;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            gap: 16px;
            transform-origin: top center;
            transition: transform 0.2s cubic-bezier(0.2, 0, 0, 1);
        }

        /* ================= BAGIAN ATAS: KARTU PESERTA ================= */
        .card-participant {
            border: 2px solid #2b2c40;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
            padding: 0;
        }

        .card-header-kop {
            display: flex;
            align-items: center;
            padding: 10px 16px;
            border-bottom: 2px solid #2b2c40;
            background: #fafafa;
            gap: 14px;
        }

        .card-logo {
            width: 58px;
            height: 58px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .card-kop-text {
            flex: 1;
            text-align: left;
        }

        .card-kop-text h2 {
            margin: 0;
            font-size: 15pt;
            font-weight: 700;
            color: #1a1b2f;
            text-transform: uppercase;
            line-height: 1.15;
        }

        .card-kop-text h3 {
            margin: 2px 0 0 0;
            font-size: 9.2pt;
            font-weight: 600;
            color: #696cff;
            line-height: 1.2;
        }

        .card-kop-text p {
            margin: 2px 0 0 0;
            font-size: 8.2pt;
            color: #555;
            line-height: 1.25;
        }

        .card-badge-title {
            background-color: #2b2c40;
            color: #fff;
            text-align: center;
            padding: 5px;
            font-size: 10.5pt;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .card-body-content {
            padding: 10px 14px;
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .card-photo-box {
            width: 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .photo-inner {
            width: 95px;
            height: 126px;
            border: 1.5px solid #333;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fdfdfd;
            overflow: hidden;
        }

        .photo-inner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-empty {
            text-align: center;
            color: #888;
            font-size: 8.5pt;
            font-weight: 600;
            line-height: 1.2;
        }

        .card-data-table-box {
            flex: 1;
            min-width: 0;
        }

        .table-data-student {
            width: 100%;
            border-collapse: collapse;
        }

        .table-data-student td {
            padding: 3px 4px;
            font-size: 8.8pt;
            vertical-align: middle;
        }

        .table-data-student td.lbl {
            font-weight: 600;
            color: #444;
            white-space: nowrap !important;
        }

        .table-data-student td.col {
            width: 8px;
            color: #666;
            text-align: center;
            white-space: nowrap !important;
        }

        .table-data-student td.val {
            color: #111;
            white-space: nowrap !important;
        }

        .val-highlight {
            font-size: 9.6pt;
            color: #696cff !important;
            font-weight: 700;
        }

        /* Account Login Box */
        .account-access-box {
            margin-top: 8px;
            background: #f4f5fa;
            border-left: 3.5px solid #ffab00;
            padding: 5px 10px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 8.4pt;
            white-space: nowrap !important;
        }

        .account-access-box strong {
            color: #333;
        }

        .account-access-box code {
            background: #fff;
            padding: 2px 5px;
            border-radius: 3px;
            border: 1px solid #ddd;
            font-family: monospace;
            font-size: 8.6pt;
            font-weight: bold;
            color: #d63384;
        }

        /* Footer Kartu */
        .card-footer-box {
            padding: 8px 16px;
            border-top: 1px solid #ddd;
            background: #fafafa;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 14px;
        }

        .card-footer-notes {
            flex: 1;
            font-size: 8pt;
            color: #555;
            line-height: 1.35;
        }

        .card-footer-notes strong {
            color: #222;
        }

        .card-footer-ttd {
            width: 180px;
            text-align: center;
            font-size: 8.2pt;
        }

        .card-footer-ttd .ttd-space {
            height: 34px;
        }

        .card-footer-ttd img {
            max-height: 42px;
            max-width: 130px;
            object-fit: contain;
            margin: 0 auto;
            display: block;
        }



        /* ================= BAGIAN BAWAH: CEKLIS DATA PERSYARATAN ================= */
        .checklist-container {
            border: 2px solid #2b2c40;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
            padding: 0;
        }

        .checklist-top-bar {
            background-color: #2b2c40;
            color: #fff;
            padding: 6px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .checklist-top-bar h4 {
            margin: 0;
            font-size: 10pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .checklist-meta {
            font-size: 8.2pt;
            color: #e0e0e0;
        }

        .checklist-student-summary {
            background: #fafafa;
            border-bottom: 1px solid #ddd;
            padding: 5px 14px;
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            font-size: 8.4pt;
        }

        .checklist-student-summary span {
            white-space: nowrap !important;
        }

        .checklist-student-summary span strong {
            color: #2b2c40;
        }

        /* Tabel Checklist */
        .checklist-table {
            width: 100%;
            border-collapse: collapse;
        }

        .checklist-table th,
        .checklist-table td {
            border: 1px solid #444;
            padding: 2.2px 5px;
            font-size: 7.5pt;
            vertical-align: middle;
            line-height: 1.18;
        }

        .checklist-table th {
            background-color: #eef0f4;
            color: #111;
            font-weight: 700;
            text-align: center;
            white-space: nowrap !important;
            padding: 3px 5px;
            font-size: 7.8pt;
        }

        .checklist-table td.col-status {
            white-space: nowrap !important;
            text-align: center;
        }

        .badge-wajib {
            background: #d1e7dd;
            color: #0f5132;
            padding: 1px 4px;
            border-radius: 3px;
            font-weight: 700;
            font-size: 7pt;
            display: inline-block;
            white-space: nowrap !important;
        }

        .badge-opsional {
            background: #e2e3e5;
            color: #41464b;
            padding: 1px 4px;
            border-radius: 3px;
            font-weight: 600;
            font-size: 7pt;
            display: inline-block;
            white-space: nowrap !important;
        }

        .box-check {
            font-family: monospace;
            font-size: 8.5pt;
            font-weight: bold;
            color: #222;
            white-space: nowrap !important;
            display: inline-block;
        }

        /* Footer Checklist: Status & Tanda Tangan */
        .checklist-bottom-box {
            display: flex;
            border-top: 1px solid #444;
            background: #fafafa;
        }

        .status-verif-box {
            flex: 1.2;
            padding: 4px 10px;
            border-right: 1px solid #444;
            font-size: 7.8pt;
        }

        .status-verif-box strong {
            font-size: 8.2pt;
            color: #111;
        }

        .status-options {
            margin-top: 2px;
            display: flex;
            gap: 12px;
            font-size: 7.8pt;
            font-weight: 600;
            white-space: nowrap !important;
        }

        .ttd-box-verif {
            flex: 1;
            padding: 4px 8px;
            text-align: center;
            font-size: 7.5pt;
        }

        .ttd-box-verif:not(:last-child) {
            border-right: 1px solid #444;
        }

        .ttd-line-space {
            height: 24px;
        }

        /* ================= RESPONSIVE RULES (MOBILE & TABLET SCREEN) ================= */
        @media screen and (max-width: 820px) {
            body {
                padding: 8px 6px;
            }

            .print-btn-container {
                position: sticky;
                top: 0;
                z-index: 1000;
                background: rgba(240, 242, 245, 0.96);
                backdrop-filter: blur(10px);
                padding: 8px 10px;
                margin: -8px -6px 10px -6px;
                border-bottom: 1px solid #d9dce3;
                box-shadow: 0 2px 8px rgba(0,0,0,0.06);
                flex-wrap: wrap;
                gap: 8px;
            }

            .btn-print {
                padding: 7px 14px;
                font-size: 12px;
            }

            .btn-back {
                padding: 7px 12px;
                font-size: 12px;
            }

            .mobile-notice {
                display: flex;
                margin: 0 0 10px 0;
                width: 100%;
            }
        }

        @media screen and (max-width: 520px) {
            .print-btn-container {
                justify-content: space-between;
                gap: 6px;
            }

            .btn-back span {
                display: none;
            }

            .btn-back {
                padding: 7px 10px;
            }

            .btn-back i {
                margin: 0;
                font-size: 17px;
            }

            .view-mode-btn {
                padding: 5px 8px;
                font-size: 11px;
            }

            .btn-print {
                padding: 7px 11px;
                font-size: 11.5px;
            }
        }

        /* ================= PRINT RULES ================= */
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .print-btn-container,
            .mobile-notice,
            .view-mode-group {
                display: none !important;
            }

            .a4-viewport-container,
            .a4-wrapper {
                width: 100% !important;
                height: auto !important;
                overflow: visible !important;
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
            }

            .a4-page {
                width: 100% !important;
                min-width: 100% !important;
                min-height: auto !important;
                height: auto !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 auto !important;
                transform: none !important;
                display: flex !important;
                flex-direction: column !important;
                gap: 16px !important;
            }

            @page {
                size: A4 portrait;
                margin: 0.6cm 0.8cm;
            }
        }
    </style>
</head>
<body>

    <!-- Tombol Navigasi & Cetak (Tampil di Layar, Hilang Saat Print) -->
    <div class="print-btn-container">
        <a href="javascript:handleBack()" class="btn-back">
            <i class="bx bx-arrow-back"></i> <span>Kembali</span>
        </a>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ Auth::guard('siswa')->check() ? route('siswa.surat-pernyataan-sehat') : route('data-cetak-kartu.surat-sehat', $dataSiswa->id) }}" target="_blank" class="btn-back" style="background: #eef2ff; color: #4338ca; border-color: #c7d2fe; font-weight: 600;" title="Download Surat Pernyataan Sehat Langsung (PDF)">
                <i class="bx bx-file-blank"></i> <span>Surat Pernyataan Sehat</span>
            </a>
            <div class="view-mode-group">
                <button type="button" class="view-mode-btn active" id="btnFitScreen" onclick="setViewMode('fit')" title="Sesuaikan dokumen pas selebar layar perangkat">
                    <i class="bx bx-fullscreen"></i> Pas Layar
                </button>
                <button type="button" class="view-mode-btn" id="btnActualSize" onclick="setViewMode('actual')" title="Tampilkan ukuran asli A4 100% (bisa digeser)">
                    <i class="bx bx-zoom-in"></i> 100%
                </button>
            </div>
            <button class="btn-print" onclick="window.print()">
                <i class="bx bx-printer"></i> <span>Cetak Dokumen</span>
            </button>
        </div>
    </div>

    <!-- Alert Edukasi / Petunjuk Layar Mobile -->
    <div class="mobile-notice">
        <i class="bx bx-info-circle"></i>
        <div>
            <strong>Pratinjau Lembar A4 (Bagi 2):</strong> Mode <strong>Pas Layar</strong> memudahkan Anda melihat seluruh isi kartu di HP. Gunakan mode <strong>100%</strong> untuk melihat teks lebih besar. Tap <strong>Cetak Dokumen</strong> untuk mencetak atau simpan PDF.
        </div>
    </div>

    <!-- Container Viewport & Lembar A4 -->
    <div class="a4-viewport-container">
        <div class="a4-wrapper" id="a4Wrapper">
            <div class="a4-page" id="a4Page">

        <!-- ================= 1. BAGIAN ATAS: KARTU PESERTA ================= -->
        <div class="card-participant">
            <!-- Kop Surat -->
            <div class="card-header-kop">
                <img src="{{ $logoUrl }}" alt="Logo" class="card-logo">
                <div class="card-kop-text">
                    <h2>{{ strtoupper($pengaturan->nama_sekolah ?? 'SMK WISATA INDONESIA') }}</h2>
                    <h3>PANITIA {{ strtoupper($namaPanjang) }} ({{ $tipeAplikasi }}) TAHUN AJARAN {{ $tahunAjaranPeserta }}</h3>
                    <p>{{ $pengaturan->alamat ?? 'Jl. Raya Lenteng Agung No. 15, Jakarta Selatan' }} &bull; Telp: {{ $pengaturan->telepon ?? '-' }} &bull; Email: {{ $pengaturan->email ?? '-' }}</p>
                </div>
            </div>

            <!-- Title Bar -->
            <div class="card-badge-title">
                KARTU TANDA PESERTA PENDAFTARAN {{ $tipeAplikasi }}
            </div>

            <!-- Body Kartu -->
            <div class="card-body-content">
                <!-- Foto Siswa -->
                <div class="card-photo-box">
                    <div class="photo-inner">
                        @if($fotoPath)
                            <img src="{{ $fotoPath }}" alt="Foto Siswa">
                        @else
                            <div class="photo-empty">
                                <i class="bx bx-user" style="font-size: 26px; display: block; margin-bottom: 2px;"></i>
                                FOTO<br>3X4
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Data Siswa (Semua Label & Nilai 1 Baris Penuh) -->
                <div class="card-data-table-box">
                    <table class="table-data-student">
                        <tr>
                            <td class="lbl" style="width: 105px;">No. Pendaftaran</td>
                            <td class="col">:</td>
                            <td class="val"><strong class="val-highlight">{{ $dataSiswa->no_pendaftaran }}</strong></td>
                            <td class="lbl" style="width: 130px; padding-left: 14px;">Gelombang</td>
                            <td class="col">:</td>
                            <td class="val">{{ $dataSiswa->gelombang->nama_gelombang ?? 'Gelombang 1' }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Nama Lengkap</td>
                            <td class="col">:</td>
                            <td class="val" colspan="4"><strong style="font-size: 9.3pt; display: inline-block; max-width: 440px; overflow: hidden; text-overflow: ellipsis; vertical-align: middle;">{{ $dataSiswa->nama_lengkap }}</strong></td>
                        </tr>
                        <tr>
                            <td class="lbl">NISN</td>
                            <td class="col">:</td>
                            <td class="val">{{ $dataSiswa->nisn ?? '-' }}</td>
                            <td class="lbl" style="width: 130px; padding-left: 14px;">Kompetensi Keahlian</td>
                            <td class="col">:</td>
                            <td class="val"><strong>{{ $dataSiswa->jurusan->nama_jurusan ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="lbl">Jenis Kelamin</td>
                            <td class="col">:</td>
                            <td class="val">{{ $dataSiswa->jenis_kelamin ?? '-' }}</td>
                            <td class="lbl" style="width: 130px; padding-left: 14px;">Tahun Ajaran</td>
                            <td class="col">:</td>
                            <td class="val">{{ $tahunAjaranPeserta }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Asal Sekolah</td>
                            <td class="col">:</td>
                            <td class="val" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                {{ $dataSiswa->asal_sekolah ?? '-' }}
                                @if(!empty($dataSiswa->jenjang_sekolah_asal) || !empty($dataSiswa->status_sekolah_asal) || !empty($dataSiswa->kota_sekolah_asal))
                                    ({{ implode(' - ', array_filter([$dataSiswa->jenjang_sekolah_asal, $dataSiswa->status_sekolah_asal, $dataSiswa->kota_sekolah_asal])) }})
                                @endif
                            </td>
                            <td class="lbl" style="width: 130px; padding-left: 14px;">Tanggal Cetak</td>
                            <td class="col">:</td>
                            <td class="val">{{ date('d/m/Y') }}</td>
                        </tr>
                    </table>

                    <!-- Akses Akun Portal Siswa -->
                    <div class="account-access-box">
                        <span><i class="bx bx-key"></i> <strong>Akses Portal Siswa:</strong></span>
                        <span>Username: <code>{{ empty($pengaturan->kartu_username_contoh) || $pengaturan->kartu_username_contoh === '[Username Anda]' ? $user->username : $pengaturan->kartu_username_contoh . substr($user->username, -3) }}</code></span>
                        <span>Password: <code>{{ !empty($pengaturan->kartu_password_contoh) ? $pengaturan->kartu_password_contoh : str_pad(abs(crc32($user->id . $user->username)) % 900000 + 100000, 6, '0', STR_PAD_LEFT) }}</code></span>
                    </div>
                </div>
            </div>

            <!-- Footer Kartu -->
            <div class="card-footer-box">
                <div class="card-footer-notes">
                    <strong>Ketentuan Peserta:</strong><br>
                    1. Kartu ini wajib dibawa saat verifikasi fisik dan tes seleksi.<br>
                    2. Gunakan akun portal untuk memantau pengumuman resmi kelulusan {{ $tipeAplikasi }}.
                </div>
                <div class="card-footer-ttd">
                    Panitia {{ $tipeAplikasi }},<br>
                    @if(!empty($pengaturan->ttd_stempel) && file_exists(public_path($pengaturan->ttd_stempel)))
                        <img src="{{ asset($pengaturan->ttd_stempel) }}" alt="Ttd & Stempel">
                    @else
                        <div class="ttd-space"></div>
                    @endif
                    <strong>{{ $pengaturan->nama_sekolah ?? 'SMK Wisata Indonesia' }}</strong>
                </div>
            </div>
        </div>

        <!-- ================= 2. BAGIAN BAWAH: CEKLIS DATA PERSYARATAN ================= -->
        <div class="checklist-container">
            <div class="checklist-top-bar">
                <h4>PERSYARATAN ADMINISTRASI & BERKAS YANG HARUS DIPENUHI OLEH PESERTA {{ $tipeAplikasi }}</h4>
                <span class="checklist-meta">{{ strtoupper($pengaturan->nama_sekolah ?? 'SMK YASPIA') }} &bull; TA {{ $tahunAjaranPeserta }}</span>
            </div>

            <!-- Ringkasan Identitas Siswa -->
            <div class="checklist-student-summary">
                <span><strong>No. Pendaftaran:</strong> {{ $dataSiswa->no_pendaftaran }}</span>
                <span><strong>Nama:</strong> {{ $dataSiswa->nama_lengkap }}</span>
                <span><strong>Jurusan:</strong> {{ $dataSiswa->jurusan->nama_jurusan ?? '-' }}</span>
                <span><strong>Asal SMP:</strong> {{ $dataSiswa->asal_sekolah ?? '-' }}</span>
            </div>

            <!-- Tabel Dokumen Persyaratan (Sesuai Ketentuan SPMB SMK Yaspia) -->
            <table class="checklist-table">
                <thead>
                    <tr>
                        <th width="4%">NO.</th>
                        <th width="58%">PERSYARATAN</th>
                        <th width="9%">ADA</th>
                        <th width="11%">TIDAK ADA</th>
                        <th width="18%">KETERANGAN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td align="center">1.</td>
                        <td>Mengisi lengkap formulir pendaftaran online {{ $pengaturan->nama_sekolah ?? 'SMK YASPIA' }} (dibuktikan dengan printout Kartu Peserta Test {{ $tipeAplikasi }})</td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td align="center">2.</td>
                        <td>3 (Tiga) lembar Foto berwarna (Background Merah/Biru) ukuran 3x4 dan 2x3</td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td align="center">3.</td>
                        <td>2 (dua) lembar Fotokopi Kartu Keluarga (KK)</td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td align="center">4.</td>
                        <td>2 (dua) lembar Fotokopi Akta Kelahiran Siswa</td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td align="center">5.</td>
                        <td>2 (dua) lembar Fotokopi Ijazah SMP/MTs legalisir</td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td align="center">6.</td>
                        <td>2 (dua) rangkap Fotokopi raport, semester 1 - 6 dari sekolah asal/sebelumnya</td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td align="center">7.</td>
                        <td>2 (dua) lembar Fotokopi Surat Keterangan Lulus dari sekolah asal</td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td align="center">8.</td>
                        <td>2 (dua) lembar Fotokopi Surat Keterangan Kelakuan Baik dari sekolah asal</td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td align="center">9.</td>
                        <td>2 (dua) lembar Fotokopi Kartu Pelajar/NISN</td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td align="center">10.</td>
                        <td>Surat Keterangan Tidak Buta Warna TERBARU (Kuliner)<br><small style="color: #555;">(dari Dokter/Dokter Mata, Puskesmas, Rumah Sakit, Klinik Kesehatan)</small></td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td align="center">11.</td>
                        <td>Khusus bagi CMB yang memiliki penyakit serius/dalam masa pengobatan Wajib menyertakan Surat Keterangan Sehat (dari Dokter, Puskesmas, Rumah Sakit, Klinik Kesehatan)</td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td align="center"><span class="box-check">[ &nbsp; ]</span></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <!-- Footer Checklist: Kesimpulan & TTD Verifikator -->
            <div class="checklist-bottom-box">
                <div class="status-verif-box">
                    <strong>Kesimpulan Verifikasi Berkas:</strong>
                    <div class="status-options">
                        <label>[ &nbsp;&nbsp; ] LENGKAP</label>
                        <label>[ &nbsp;&nbsp; ] BELUM LENGKAP</label>
                    </div>
                    <small style="color: #666; font-size: 6.8pt;">Batas Kelengkapan: .......................................</small>
                </div>
                <div class="ttd-box-verif">
                    Yang Menyerahkan Berkas,<br>
                    <div class="ttd-line-space"></div>
                    <strong>( ............................................ )</strong><br>
                    <small>Siswa / Orang Tua</small>
                </div>
                <div class="ttd-box-verif">
                    Petugas Verifikator Panitia,<br>
                    <div class="ttd-line-space"></div>
                    <strong>( ............................................ )</strong><br>
                    <small>Tgl: ..... / ..... / 2026</small>
                </div>
            </div>
        </div>

        </div><!-- /#a4Page -->
    </div><!-- /#a4Wrapper -->
</div><!-- /.a4-viewport-container -->

<script>
    // Inisialisasi mode pratinjau: di mobile/tablet default 'fit' (pas layar), di desktop 'actual' (100%)
    let currentMode = window.innerWidth <= 820 ? 'fit' : 'actual';

    function handleBack() {
        if (window.opener || window.history.length <= 1) {
            window.location.href = "{{ route('siswa.dashboard') }}";
        } else {
            window.history.back();
        }
    }

    function setViewMode(mode) {
        currentMode = mode;
        updateViewButtons();
        applyViewMode();
    }

    function updateViewButtons() {
        const btnFit = document.getElementById('btnFitScreen');
        const btnActual = document.getElementById('btnActualSize');
        if (btnFit && btnActual) {
            if (currentMode === 'fit') {
                btnFit.classList.add('active');
                btnActual.classList.remove('active');
            } else {
                btnActual.classList.add('active');
                btnFit.classList.remove('active');
            }
        }
    }

    function applyViewMode() {
        const wrapper = document.getElementById('a4Wrapper');
        const page = document.getElementById('a4Page');
        if (!wrapper || !page) return;

        // Lebar standar lembar A4 (210mm) pada raster layar kira-kira 794px
        const a4WidthPx = 794;
        const screenWidth = window.innerWidth;

        if (currentMode === 'fit' && screenWidth <= 820) {
            const availableWidth = wrapper.clientWidth - 16;
            const scale = Math.min(1, Math.max(0.32, availableWidth / a4WidthPx));

            page.style.transform = `scale(${scale})`;
            page.style.transformOrigin = 'top center';

            const naturalHeight = page.scrollHeight || page.offsetHeight;
            const scaledHeight = naturalHeight * scale;
            wrapper.style.height = `${scaledHeight + 25}px`;
            wrapper.style.overflowX = 'hidden';
        } else {
            page.style.transform = 'none';
            page.style.transformOrigin = 'top center';
            wrapper.style.height = 'auto';
            wrapper.style.overflowX = 'auto';
        }
    }

    window.addEventListener('resize', () => {
        if (window.innerWidth > 820) {
            setViewMode('actual');
        } else {
            applyViewMode();
        }
    });

    window.addEventListener('orientationchange', () => {
        setTimeout(applyViewMode, 200);
    });

    document.addEventListener('DOMContentLoaded', () => {
        if (window.innerWidth <= 820) {
            setViewMode('fit');
        } else {
            setViewMode('actual');
        }
    });
</script>
</body>
</html>
