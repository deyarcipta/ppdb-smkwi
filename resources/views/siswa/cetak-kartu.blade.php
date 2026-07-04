<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Peserta PPDB - {{ $dataSiswa->nama_lengkap }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('sneat/vendor/fonts/boxicons.css') }}" />
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .print-btn-container {
            max-width: 600px;
            margin: 0 auto 20px auto;
            text-align: right;
        }

        .btn-print {
            background-color: #696cff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(105, 108, 255, 0.3);
            transition: all 0.3s ease;
        }

        .btn-print:hover {
            background-color: #5f61e6;
            transform: translateY(-2px);
        }

        /* Kartu Style */
        .card-container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border: 2px solid #696cff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        /* Header WI */
        .card-header {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 2px solid #e4e6e8;
            background: linear-gradient(135deg, #f8f9fa 0%, #eef0f2 100%);
        }

        .logo-img {
            height: 70px;
            width: auto;
            margin-right: 20px;
        }

        .header-text {
            flex-grow: 1;
        }

        .header-text h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #2b2c40;
            text-transform: uppercase;
        }

        .header-text h3 {
            margin: 3px 0 0 0;
            font-size: 12px;
            font-weight: 600;
            color: #697a8d;
        }

        .header-text p {
            margin: 5px 0 0 0;
            font-size: 10px;
            color: #8592a3;
            line-height: 1.4;
        }

        /* Card Body */
        .card-title-bar {
            background-color: #696cff;
            color: white;
            text-align: center;
            padding: 8px;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .card-body {
            padding: 25px;
            display: flex;
            gap: 25px;
        }

        .photo-column {
            width: 130px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .photo-frame {
            width: 130px;
            height: 170px;
            border: 2px dashed #697a8d;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background-color: #f8f9fa;
        }

        .photo-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-placeholder {
            text-align: center;
            color: #a1b0cb;
            font-size: 12px;
        }

        .info-column {
            flex-grow: 1;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 6px 4px;
            font-size: 13px;
            vertical-align: top;
        }

        .info-table td.label {
            font-weight: 600;
            color: #566a7f;
            width: 130px;
        }

        .info-table td.colon {
            width: 10px;
            color: #8592a3;
        }

        .info-table td.value {
            color: #233446;
            font-weight: 500;
        }

        /* Credential Box */
        .credential-box {
            margin-top: 15px;
            background-color: #f3f4f6;
            border-radius: 8px;
            padding: 10px 15px;
            border-left: 4px solid #ffab00;
        }

        .credential-box h4 {
            margin: 0 0 5px 0;
            font-size: 12px;
            color: #8592a3;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .credential-row {
            display: flex;
            gap: 15px;
            margin-top: 5px;
        }

        .credential-row .credential-item {
            font-size: 13px;
            color: #233446;
        }

        .credential-row .credential-item strong {
            color: #566a7f;
        }

        /* Card Footer */
        .card-footer {
            padding: 15px 25px;
            border-top: 1px solid #e4e6e8;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fafafa;
        }

        .note-text {
            font-size: 10px;
            color: #8592a3;
            max-width: 320px;
            line-height: 1.4;
        }

        .signature-box {
            text-align: center;
            font-size: 11px;
            color: #566a7f;
        }

        .signature-box .signature-line {
            height: 40px;
        }

        .signature-box strong {
            color: #233446;
            display: block;
        }

        /* Print Media Styles */
        @media print {
            body {
                background-color: white;
                padding: 0;
            }

            .print-btn-container {
                display: none;
            }

            .card-container {
                box-shadow: none;
                border: 2px solid #000;
                margin-top: 20px;
                border-radius: 12px;
            }

            .card-header {
                background: white;
                border-bottom: 2px solid #000;
            }

            .card-title-bar {
                background-color: #000;
                color: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .credential-box {
                background-color: #f8f9fa !important;
                border-left: 4px solid #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <div class="print-btn-container">
        <button class="btn-print" onclick="window.print()">
            <i class="bx bx-printer"></i> Cetak Kartu
        </button>
    </div>

    <div class="card-container">
        <!-- Header -->
        <div class="card-header">
            @if($pengaturan->logo)
                <img src="{{ asset($pengaturan->logo) }}" alt="Logo" class="logo-img">
            @else
                <img src="{{ asset('sneat/img/logowi.png') }}" alt="Logo" class="logo-img">
            @endif
            <div class="header-text">
                <h2>{{ $pengaturan->nama_sekolah ?? 'SMK Wisata Indonesia' }}</h2>
                <h3>PANITIA PENERIMAAN PESERTA DIDIK BARU (PPDB)</h3>
                <p>{{ $pengaturan->alamat ?? 'Jl. Raya Lenteng Agung No. 15, Jakarta Selatan' }}<br>
                Telp: {{ $pengaturan->telepon ?? '-' }} | Email: {{ $pengaturan->email ?? '-' }}</p>
            </div>
        </div>

        <!-- Title Bar -->
        <div class="card-title-bar">
            Kartu Bukti Pendaftaran PPDB
        </div>

        <!-- Body -->
        <div class="card-body">
            <!-- Photo Column -->
            <div class="photo-column">
                <div class="photo-frame">
                    @if($dataSiswa->foto_siswa)
                        <img src="{{ asset('storage/' . $dataSiswa->foto_siswa) }}" alt="Foto Siswa">
                    @else
                        <div class="photo-placeholder">
                            <i class="bx bx-user" style="font-size: 40px; display: block; margin-bottom: 5px;"></i>
                            FOTO 3X4
                        </div>
                    @endif
                </div>
            </div>

            <!-- Info Column -->
            <div class="info-column">
                <table class="info-table">
                    <tr>
                        <td class="label">No. Pendaftaran</td>
                        <td class="colon">:</td>
                        <td class="value"><strong>{{ $dataSiswa->no_pendaftaran }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Nama Lengkap</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $dataSiswa->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <td class="label">NISN</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $dataSiswa->nisn }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jenis Kelamin</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $dataSiswa->jenis_kelamin }}</td>
                    </tr>
                    <tr>
                        <td class="label">Asal Sekolah</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $dataSiswa->asal_sekolah }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jurusan Pilihan</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $dataSiswa->jurusan->nama_jurusan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Gelombang</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $dataSiswa->gelombang->nama_gelombang ?? 'Gelombang 1' }}</td>
                    </tr>
                </table>

                <!-- Info Akun Login -->
                <div class="credential-box">
                    <h4>Akses Portal Siswa</h4>
                    <div class="credential-row">
                        <div class="credential-item">
                            <strong>Username:</strong> 
                            <code>{{ empty($pengaturan->kartu_username_contoh) || $pengaturan->kartu_username_contoh === '[Username Anda]' ? $user->username : $pengaturan->kartu_username_contoh . substr($user->username, -3) }}</code>
                        </div>
                        <div class="credential-item">
                            <strong>Password:</strong> 
                            <code>{{ $user->password_plain ?? (!empty($pengaturan->kartu_password_contoh) ? $pengaturan->kartu_password_contoh : str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT)) }}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="card-footer">
            <div class="note-text">
                <strong>Catatan:</strong> Simpan kartu bukti pendaftaran ini dengan baik. Gunakan informasi akses akun di atas untuk login dan mengecek pengumuman pada portal PPDB.
            </div>
            <div class="signature-box">
                Panitia PPDB,<br>
                Ttd & Stempel
                <div class="signature-line"></div>
                <strong>{{ $pengaturan->nama_sekolah ?? 'SMK Wisata Indonesia' }}</strong>
            </div>
        </div>
    </div>

    <script>
        // Otomatis memicu dialog print saat halaman selesai dimuat
        window.addEventListener('DOMContentLoaded', (event) => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
