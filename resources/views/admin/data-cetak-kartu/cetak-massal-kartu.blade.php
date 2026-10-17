@php
    $pengaturan = $pengaturan ?? \App\Models\PengaturanAplikasi::getSettings();
    $tipeAplikasi = $pengaturan->tipe_aplikasi ?? 'PPDB';
    $namaPanjang = $pengaturan->namaPanjangAplikasi;

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
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Massal Kartu Peserta & Ceklis Berkas {{ $tipeAplikasi }} ({{ $dataSiswaList->count() }} Siswa)</title>
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
            margin: 0 auto 16px auto;
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

        .btn-print {
            background-color: #ffab00;
            color: #fff;
            border: none;
            padding: 9px 20px;
            font-size: 13.5px;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            box-shadow: 0 3px 8px rgba(255, 171, 0, 0.4);
            transition: all 0.2s ease;
        }

        .btn-print:hover {
            background-color: #e09600;
            transform: translateY(-1px);
        }

        .count-badge {
            background: #e7e7ff;
            color: #696cff;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* Container Viewport & A4 Utama */
        .a4-viewport-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 25px;
        }

        .a4-page {
            width: 210mm;
            min-height: 297mm;
            background: #ffffff;
            padding: 10mm 12mm 8mm 12mm;
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            gap: 10px;
            box-sizing: border-box;
            position: relative;
        }

        /* ================= 1. BAGIAN ATAS: KARTU PESERTA ================= */
        .card-participant {
            border: 2px solid #2b2c40;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
            display: flex;
            flex-direction: column;
        }

        .card-header-kop {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border-bottom: 2px solid #2b2c40;
            gap: 12px;
            background: #ffffff;
        }

        .card-logo {
            width: 48px;
            height: 48px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .card-kop-text {
            flex-grow: 1;
            text-align: center;
        }

        .card-kop-text h2 {
            margin: 0;
            font-size: 12pt;
            font-weight: 700;
            color: #111;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }

        .card-kop-text h3 {
            margin: 2px 0 0 0;
            font-size: 8.5pt;
            font-weight: 600;
            color: #444;
            line-height: 1.2;
        }

        .card-kop-text p {
            margin: 2px 0 0 0;
            font-size: 7.2pt;
            color: #666;
            line-height: 1.2;
        }

        .card-badge-title {
            background-color: #2b2c40;
            color: #ffffff;
            text-align: center;
            font-size: 9.5pt;
            font-weight: 700;
            padding: 4px 0;
            letter-spacing: 0.8px;
        }

        .card-body-content {
            padding: 8px 12px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .card-photo-box {
            width: 2.8cm;
            flex-shrink: 0;
        }

        .photo-inner {
            width: 2.8cm;
            height: 3.7cm;
            border: 1px dashed #888;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fdfdfd;
            overflow: hidden;
        }

        .photo-inner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-empty {
            text-align: center;
            font-size: 8pt;
            color: #888;
            font-weight: 600;
            line-height: 1.3;
        }

        .card-data-table-box {
            flex-grow: 1;
        }

        .table-data-student {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.4pt;
        }

        .table-data-student td {
            padding: 2.2px 0;
            vertical-align: middle;
            line-height: 1.25;
        }

        .table-data-student td.lbl {
            color: #495057;
            font-weight: 500;
            white-space: nowrap !important;
        }

        .table-data-student td.col {
            width: 12px;
            text-align: center;
            white-space: nowrap !important;
        }

        .table-data-student td.val {
            color: #111;
            white-space: nowrap !important;
        }

        .val-highlight {
            color: #696cff;
            font-weight: 700;
            font-size: 9.5pt;
        }

        .account-access-box {
            margin-top: 5px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 7.8pt;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .account-access-box span {
            white-space: nowrap !important;
        }

        .account-access-box code {
            font-family: monospace;
            background: #fff;
            padding: 1px 4px;
            border: 1px solid #ced4da;
            border-radius: 3px;
            color: #2b2c40;
            font-weight: 700;
            font-size: 8.2pt;
        }

        .card-footer-box {
            border-top: 1px solid #2b2c40;
            padding: 6px 12px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            background-color: #ffffff;
        }

        .card-footer-notes {
            font-size: 7.2pt;
            color: #444;
            line-height: 1.3;
            max-width: 60%;
        }

        .card-footer-ttd {
            text-align: center;
            font-size: 8pt;
        }

        .card-footer-ttd .ttd-space {
            height: 32px;
        }

        .card-footer-ttd img {
            max-height: 40px;
            max-width: 120px;
            object-fit: contain;
            margin: 0 auto;
            display: block;
        }

        /* ================= 2. BAGIAN BAWAH: CEKLIS PERSYARATAN ================= */
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
            padding: 5px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .checklist-top-bar h4 {
            margin: 0;
            font-size: 9.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .checklist-meta {
            font-size: 8pt;
            color: #e0e0e0;
        }

        .checklist-student-summary {
            background: #fafafa;
            border-bottom: 1px solid #ddd;
            padding: 4px 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            font-size: 8.2pt;
        }

        .checklist-student-summary span {
            white-space: nowrap !important;
        }

        .checklist-student-summary span strong {
            color: #2b2c40;
        }

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

        /* ================= PRINT RULES ================= */
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .print-btn-container {
                display: none !important;
            }

            .a4-viewport-container {
                gap: 0 !important;
            }

            .a4-page {
                width: 100% !important;
                min-width: 100% !important;
                min-height: 297mm !important;
                height: 297mm !important;
                box-shadow: none !important;
                padding: 6mm 10mm 4mm 10mm !important;
                margin: 0 !important;
                page-break-after: always !important;
                break-after: page !important;
            }

            .a4-page:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Tombol Navigasi & Cetak Massal -->
    <div class="print-btn-container">
        <a href="{{ route('data-cetak-kartu.index') }}" class="btn-back">
            <i class="bx bx-arrow-back"></i> <span>Kembali ke Data Cetak Kartu</span>
        </a>

        <div style="display: flex; align-items: center; gap: 10px;">
            <span class="count-badge">
                <i class="bx bx-check-double"></i> Total: {{ $dataSiswaList->count() }} Kartu Peserta
            </span>
            <button class="btn-print" onclick="window.print()">
                <i class="bx bx-printer"></i> <span>Cetak Semua ({{ $dataSiswaList->count() }} Lembar A4)</span>
            </button>
        </div>
    </div>

    <!-- Container Viewport Kartu Massal -->
    <div class="a4-viewport-container">
        @foreach($dataSiswaList as $dataSiswa)
            @php
                $user = $dataSiswa->user;
                $tahunAjaranPeserta = $dataSiswa->tahunAjaran?->nama 
                    ?? $dataSiswa->gelombang?->tahunAjaran?->nama 
                    ?? $pengaturan->tahunAjaranAktif?->nama 
                    ?? date('Y') . '/' . (date('Y') + 1);

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

            <div class="a4-page">
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

                        <!-- Data Siswa -->
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
                                    </td>
                                    <td class="lbl" style="width: 130px; padding-left: 14px;">Tanggal Cetak</td>
                                    <td class="col">:</td>
                                    <td class="val">{{ date('d/m/Y') }}</td>
                                </tr>
                            </table>

                            <!-- Akses Akun Portal Siswa -->
                            <div class="account-access-box">
                                <span><i class="bx bx-key"></i> <strong>Akses Portal Siswa:</strong></span>
                                <span>Username: <code>{{ empty($pengaturan->kartu_username_contoh) || $pengaturan->kartu_username_contoh === '[Username Anda]' ? ($user?->username ?? '-') : $pengaturan->kartu_username_contoh . substr($user?->username ?? '000', -3) }}</code></span>
                                <span>Password: <code>{{ !empty($pengaturan->kartu_password_contoh) ? $pengaturan->kartu_password_contoh : str_pad(abs(crc32(($user?->id ?? 0) . ($user?->username ?? ''))) % 900000 + 100000, 6, '0', STR_PAD_LEFT) }}</code></span>
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

                <!-- ================= 2. BAGIAN BAWAH: CEKLIS PERSYARATAN ================= -->
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

                    <!-- Tabel Dokumen Persyaratan -->
                    <table class="checklist-table">
                        <thead>
                            <tr>
                                <th width="4%">NO.</th>
                                <th width="58%">PERSYARATAN</th>
                                <th width="9%">ADA</th>
                                <th width="11%">TIDAK ADA</th>
                                <th width="18%">STATUS KETENTUAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $syaratItems = [
                                    ['nama' => '1 Lembar Formulir Pendaftaran (diisi lengkap & ditandatangani)', 'wajib' => true, 'ada' => true],
                                    ['nama' => '1 Lembar Surat Pernyataan Sehat (Asli bermaterai Rp 10.000)', 'wajib' => true, 'ada' => false],
                                    ['nama' => '1 Lembar Surat Pernyataan Tata Tertib (Bermaterai Rp 10.000)', 'wajib' => true, 'ada' => false],
                                    ['nama' => '1 Lembar Fotocopy Akte Kelahiran', 'wajib' => true, 'ada' => !empty($dataSiswa->akta_kelahiran)],
                                    ['nama' => '1 Lembar Fotocopy Kartu Keluarga (KK)', 'wajib' => true, 'ada' => !empty($dataSiswa->kartu_keluarga)],
                                    ['nama' => '1 Lembar Fotocopy KTP Orang Tua (Ayah & Ibu)', 'wajib' => true, 'ada' => !empty($dataSiswa->ktp_ayah) || !empty($dataSiswa->ktp_ibu)],
                                    ['nama' => '2 Lembar Fotocopy Ijazah SMP/MTs (Legalisir Asli)', 'wajib' => true, 'ada' => !empty($dataSiswa->ijazah)],
                                    ['nama' => '2 Lembar Fotocopy SKHUN / Surat Keterangan Lulus (SKL)', 'wajib' => true, 'ada' => !empty($dataSiswa->skhun) || !empty($dataSiswa->skl)],
                                    ['nama' => 'Pas Foto 3x4 (3 lembar) & 2x3 (2 lembar) Berseragam SMP Background Merah', 'wajib' => true, 'ada' => !empty($dataSiswa->foto_siswa)],
                                    ['nama' => '1 Lembar Fotocopy Kartu KIP / PIP / KIS / KJP (Jika Memiliki)', 'wajib' => false, 'ada' => !empty($dataSiswa->no_kip)],
                                    ['nama' => '1 Lembar Surat Keterangan Kelakuan Baik (SKKB) dari Asal Sekolah', 'wajib' => false, 'ada' => false],
                                    ['nama' => '1 Lembar Fotocopy Sertifikat Prestasi Akademik / Non-Akademik', 'wajib' => false, 'ada' => false],
                                ];
                            @endphp

                            @foreach($syaratItems as $idx => $item)
                                <tr>
                                    <td class="col-status">{{ $idx + 1 }}</td>
                                    <td>{{ $item['nama'] }}</td>
                                    <td class="col-status">
                                        <span class="box-check">{{ $item['ada'] ? '[ V ]' : '[   ]' }}</span>
                                    </td>
                                    <td class="col-status">
                                        <span class="box-check">{{ !$item['ada'] ? '[ V ]' : '[   ]' }}</span>
                                    </td>
                                    <td class="col-status">
                                        @if($item['wajib'])
                                            <span class="badge-wajib">Wajib</span>
                                        @else
                                            <span class="badge-opsional">Opsional</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Footer Ceklis: Status Verifikasi & Kolom Tanda Tangan Panitia -->
                    <div class="checklist-bottom-box">
                        <div class="status-verif-box">
                            <strong>HASIL VERIFIKASI BERKAS:</strong>
                            <div class="status-options">
                                <span>[   ] LENGKAP</span>
                                <span>[   ] BELUM LENGKAP</span>
                                <span>[   ] DITOLAK</span>
                            </div>
                            <div style="font-size: 7.2pt; color: #555; margin-top: 2px;">
                                Catatan Panitia: ............................................................................
                            </div>
                        </div>

                        <div class="ttd-box-verif">
                            Tanda Tangan Pendaftar / Ortu,<br>
                            <div class="ttd-line-space"></div>
                            ( <strong>{{ $dataSiswa->nama_lengkap }}</strong> )
                        </div>

                        <div class="ttd-box-verif">
                            Petugas Verifikator Berkas,<br>
                            <div class="ttd-line-space"></div>
                            ( ............................................ )
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</body>
</html>
