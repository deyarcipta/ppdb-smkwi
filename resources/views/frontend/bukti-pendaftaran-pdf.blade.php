<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Bukti Pendaftaran PPDB - {{ $dataSiswa->nama_lengkap ?? $user->username }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1cm 1.2cm 1cm 1.2cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9.5pt;
            color: #333333;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        /* HEADER / KOP SURAT */
        .kop-surat {
            width: 100%;
            border-bottom: 2.5px double #1b5e20;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-logo {
            width: 70px;
            text-align: center;
            vertical-align: middle;
        }

        .kop-logo img {
            max-width: 65px;
            max-height: 65px;
        }

        .kop-text {
            text-align: center;
            vertical-align: middle;
            padding-right: 35px;
        }

        .kop-text h2 {
            font-size: 12pt;
            font-weight: bold;
            color: #1b5e20;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-text h1 {
            font-size: 15pt;
            font-weight: bold;
            color: #000000;
            margin: 2px 0;
        }

        .kop-text p {
            font-size: 8pt;
            color: #555555;
            margin: 1px 0;
        }

        /* TITLE DOCUMENT */
        .title-box {
            text-align: center;
            margin-bottom: 10px;
        }

        .title-box h3 {
            font-size: 12pt;
            font-weight: bold;
            color: #1b5e20;
            margin: 0;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .title-box p {
            font-size: 8.5pt;
            color: #666666;
            margin: 2px 0 0 0;
        }

        /* ACCOUNT BOX */
        .account-box {
            background-color: #e8f5e9;
            border: 1.5px dashed #2e7d32;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 12px;
            width: 100%;
            box-sizing: border-box;
        }

        .account-table {
            width: 100%;
            border-collapse: collapse;
        }

        .account-table td {
            font-size: 9.5pt;
            padding: 2px 0;
        }

        .account-label {
            font-weight: bold;
            color: #1b5e20;
            width: 150px;
        }

        .account-value {
            font-family: 'Courier', monospace;
            font-weight: bold;
            font-size: 10.5pt;
            color: #000000;
        }

        /* SECTION HEADER */
        .section-header {
            background-color: #1b5e20;
            color: #ffffff;
            font-size: 9pt;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
            margin-top: 10px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        /* DATA TABLE */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .data-table td {
            padding: 4px 6px;
            font-size: 9pt;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: top;
        }

        .data-label {
            width: 32%;
            color: #444444;
            font-weight: 600;
        }

        .data-separator {
            width: 3%;
            text-align: center;
            color: #666666;
        }

        .data-value {
            width: 65%;
            color: #000000;
            font-weight: 500;
        }

        /* INSTRUCTION BOX */
        .instruction-box {
            background-color: #fffde7;
            border: 1px solid #fff59d;
            border-left: 4px solid #fbc02d;
            padding: 8px 10px;
            border-radius: 4px;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .instruction-box h4 {
            margin: 0 0 4px 0;
            font-size: 9pt;
            color: #f57f17;
            font-weight: bold;
        }

        .instruction-box ol {
            margin: 0;
            padding-left: 18px;
            font-size: 8pt;
            color: #444444;
        }

        .instruction-box li {
            margin-bottom: 2px;
        }

        /* FOOTER NOTE */
        .footer-note {
            text-align: center;
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px dashed #ccc;
            font-size: 7.5pt;
            color: #666666;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="kop-surat">
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    @php
                        $logoPath = public_path($pengaturan->logo ?? 'sneat/img/logowi.png');
                        if (!file_exists($logoPath)) {
                            $logoPath = public_path('sneat/img/logowi.png');
                        }
                    @endphp
                    @if(file_exists($logoPath))
                        <img src="{{ $logoPath }}" alt="Logo Sekolah">
                    @endif
                </td>
                <td class="kop-text">
                    <h2>PANITIA PENERIMAAN PESERTA DIDIK BARU (PPDB)</h2>
                    <h1>{{ $pengaturan->nama_sekolah ?? 'SMK WISATA INDONESIA' }}</h1>
                    <p>{{ $pengaturan->alamat ?? 'Jl. Raya Desa No. 123, Indonesia' }}</p>
                    <p>Telepon: {{ $pengaturan->telepon ?? '-' }} | Email: {{ $pengaturan->email ?? '-' }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- JUDUL -->
    <div class="title-box">
        <h3>TANDA BUKTI PENDAFTARAN SISWA BARU</h3>
        <p>Tahun Ajaran: {{ $dataSiswa->tahunAjaran->nama ?? date('Y').'/'.(date('Y')+1) }}</p>
    </div>

    <!-- AKUN LOGIN SISWA -->
    <div class="account-box">
        <table class="account-table">
            <tr>
                <td class="account-label">USERNAME LOGIN:</td>
                <td class="account-value">{{ $user->username }}</td>
            </tr>
            <tr>
                <td class="account-label">PASSWORD AKUN:</td>
                <td class="account-value">{{ $user->password_plain ?? 'password123' }}</td>
            </tr>
            <tr>
                <td class="account-label">STATUS AKUN:</td>
                <td style="font-weight: bold; color: #2e7d32;">AKTIF</td>
            </tr>
        </table>
    </div>

    <!-- SECTION DATA CALON SISWA -->
    <div class="section-header">A. Data Calon Siswa</div>
    <table class="data-table">
        <tr>
            <td class="data-label">Nama Lengkap</td>
            <td class="data-separator">:</td>
            <td class="data-value">{{ $dataSiswa->nama_lengkap ?? '-' }}</td>
        </tr>
        <tr>
            <td class="data-label">NISN</td>
            <td class="data-separator">:</td>
            <td class="data-value">{{ $dataSiswa->nisn ?? '-' }}</td>
        </tr>
        <tr>
            <td class="data-label">Jenis Kelamin</td>
            <td class="data-separator">:</td>
            <td class="data-value">
                @php
                    $jk = strtolower(trim($dataSiswa->jenis_kelamin ?? ''));
                    if (in_array($jk, ['l', 'laki-laki', 'laki - laki', 'laki_laki'])) {
                        echo 'Laki-Laki';
                    } elseif (in_array($jk, ['p', 'perempuan'])) {
                        echo 'Perempuan';
                    } else {
                        echo $dataSiswa->jenis_kelamin ?: '-';
                    }
                @endphp
            </td>
        </tr>
        <tr>
            <td class="data-label">Asal Sekolah (SMP/MTs)</td>
            <td class="data-separator">:</td>
            <td class="data-value">{{ $dataSiswa->asal_sekolah ?? '-' }}</td>
        </tr>
        <tr>
            <td class="data-label">Nomor WhatsApp / HP Siswa</td>
            <td class="data-separator">:</td>
            <td class="data-value">{{ $dataSiswa->no_hp ?? '-' }}</td>
        </tr>
        <tr>
            <td class="data-label">Gelombang Pendaftaran</td>
            <td class="data-separator">:</td>
            <td class="data-value">{{ $dataSiswa->gelombangPendaftaran->nama_gelombang ?? 'Gelombang Pendaftaran' }}</td>
        </tr>
        <tr>
            <td class="data-label">Tanggal Pendaftaran</td>
            <td class="data-separator">:</td>
            <td class="data-value">{{ $tanggal ?? date('d F Y') }}</td>
        </tr>
    </table>

    <!-- SECTION DATA ORANG TUA / WALI -->
    <div class="section-header">B. Data Orang Tua / Wali</div>
    <table class="data-table">
        <tr>
            <td class="data-label">No. HP / WA Ayah</td>
            <td class="data-separator">:</td>
            <td class="data-value">{{ $dataSiswa->no_hp_ayah ?? '-' }}</td>
        </tr>
        <tr>
            <td class="data-label">No. HP / WA Ibu</td>
            <td class="data-separator">:</td>
            <td class="data-value">{{ $dataSiswa->no_hp_ibu ?? '-' }}</td>
        </tr>
    </table>

    <!-- PETUNJUK -->
    <div class="instruction-box">
        <h4>PETUNJUK ALUR SELANJUTNYA:</h4>
        <ol>
            <li>Simpan bukti pendaftaran ini dengan baik sebagai bukti resmi registrasi PPDB.</li>
            <li>Gunakan <strong>Username</strong> dan <strong>Password</strong> di atas untuk login ke <strong>Portal Siswa PPDB</strong>.</li>
            <li>Lengkapi data formulir dan unggah pasfoto pada formulir pendaftaran di portal siswa.</li>
            <li>Lakukan pembayaran registrasi/formulir sesuai instruksi pada menu Pembayaran.</li>
            <li>Jika membutuhkan bantuan, hubungi Panitia PPDB di No. Telp: <strong>{{ $pengaturan->telepon ?? $pengaturan->no_hp ?? '-' }}</strong>.</li>
        </ol>
    </div>

    <!-- FOOTER NOTE -->
    <div class="footer-note">
        <em>Dicetak otomatis oleh Sistem PPDB Online pada tanggal: {{ date('d/m/Y H:i:s') }} WIB</em>
    </div>

</body>
</html>
