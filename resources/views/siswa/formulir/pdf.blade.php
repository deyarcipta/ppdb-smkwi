<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Formulir Pendaftaran - {{ $pendaftar->dataSiswa->nama_lengkap ?? $pendaftar->username }}</title>
<style>
    @page {
        size: a4 portrait;
        margin: 10mm 12mm 8mm 12mm;
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: "Times New Roman", Times, serif;
        font-size: 9pt;
        color: #000;
        margin: 0;
        padding: 0;
        line-height: 1.28;
        background: #fff;
    }

    .container {
        width: 100%;
        margin: 0 auto;
    }

    /* ==== KOP HEADER ==== */
    .kop-table {
        width: 100%;
        border-collapse: collapse;
        border: none;
        margin-bottom: 3px;
    }

    .kop-table td {
        border: none;
        padding: 0;
        vertical-align: middle;
    }

    .kop-logo {
        width: 62px;
        height: 62px;
        object-fit: contain;
    }

    .kop-title {
        font-size: 14.5pt;
        font-weight: bold;
        margin: 0;
        line-height: 1.15;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .kop-sub {
        font-size: 8.8pt;
        font-weight: normal;
        margin: 2px 0 0 0;
        line-height: 1.25;
        color: #111;
    }

    .divider {
        border-top: 2px solid #000;
        border-bottom: 0.8px solid #000;
        height: 2px;
        margin: 4px 0 7px 0;
    }

    .form-title {
        text-align: center;
        font-size: 12pt;
        font-weight: bold;
        text-transform: uppercase;
        margin: 0;
        line-height: 1.2;
        letter-spacing: 0.5px;
    }

    .form-number {
        text-align: center;
        font-size: 9pt;
        margin: 2px 0 7px 0;
    }

    /* ==== SECTION HEADERS & TABLES ==== */
    .section-title {
        background: #e9ecef;
        border: 1px solid #000;
        text-align: center;
        font-weight: bold;
        font-size: 9.2pt;
        padding: 3px 0;
        margin-top: 7px;
        letter-spacing: 0.3px;
    }

    table.data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0;
        table-layout: fixed;
    }

    table.data-table th, 
    table.data-table td {
        border: 1px solid #000;
        padding: 4px 6px;
        font-size: 8.8pt;
        vertical-align: middle;
        word-wrap: break-word;
        overflow-wrap: break-word;
        line-height: 1.28;
    }

    table.data-table th {
        text-align: center;
        background: #f2f4f7;
        font-weight: bold;
        font-size: 9pt;
        padding: 4px 6px;
    }

    /* KOTAK PAS FOTO 3X4 CM PROPORSIONAL */
    .photo-cell {
        width: 32mm;
        max-width: 32mm;
        text-align: center;
        vertical-align: middle;
        padding: 2px 0;
        background: #ffffff;
    }

    .photo-box-frame {
        width: 29mm;
        height: 39mm;
        margin: 0 auto;
        border: 1px dashed #444;
        background-color: #fafafa;
        display: block;
        box-sizing: border-box;
        text-align: center;
    }

    .photo-box-content {
        padding-top: 11mm;
        font-size: 9pt;
        font-weight: bold;
        color: #444;
        line-height: 1.35;
    }

    .photo-box-content small {
        font-size: 7.5pt;
        font-weight: normal;
        color: #666;
    }

    .photo-img {
        width: 29mm;
        height: 39mm;
        object-fit: cover;
        display: block;
        margin: 0 auto;
        border: 1px solid #333;
    }

    .lbl {
        font-weight: bold;
        color: #000;
    }

    /* Pernyataan & Tanda Tangan */
    .pernyataan-box {
        border: 1px solid #000;
        margin-top: 7px;
        padding: 6px 10px;
        font-size: 8.2pt;
    }

    .pernyataan-box p {
        margin: 0 0 6px 0;
        text-align: justify;
        line-height: 1.35;
    }

    .ttd-table {
        width: 100%;
        border-collapse: collapse;
        border: none;
        margin-top: 2px;
    }

    .ttd-table td {
        border: none;
        padding: 0;
        text-align: center;
        vertical-align: top;
        font-size: 8.5pt;
    }

    .ttd-space {
        height: 48px;
    }

    .ttd-line {
        border-top: 1px solid #000;
        width: 68%;
        margin: 0 auto;
        padding-top: 2px;
        font-size: 8.5pt;
    }

    .footer-bar {
        text-align: right;
        font-size: 7.5pt;
        margin-top: 4px;
        color: #444;
    }
</style>
</head>
<body>
<div class="container">
    @php
        $pengaturan = $pengaturan ?? \App\Models\PengaturanAplikasi::first();
        $tipeAplikasi = $pengaturan->tipe_aplikasi ?? 'PPDB';
        $namaSekolah = $pengaturan->nama_sekolah ?? 'SMK WISATA INDONESIA';
        $alamatSekolah = $pengaturan->alamat ?? 'Jl. Raya Lenteng Agung No. 15, Jakarta Selatan';
        $teleponSekolah = $pengaturan->telepon ?? '-';

        $dataSiswa = $pendaftar->dataSiswa ?? null;
        $tahunAjaran = $dataSiswa?->tahunAjaran?->nama 
            ?? $dataSiswa?->gelombang?->tahunAjaran?->nama 
            ?? $pengaturan?->tahunAjaranAktif?->nama 
            ?? date('Y') . '/' . (date('Y') + 1);

        // Logo
        $logoPath = null;
        if (!empty($pengaturan->logo)) {
            $cleanLogo = ltrim(preg_replace('/^storage\//', '', $pengaturan->logo), '/');
            if (file_exists(public_path('storage/' . $cleanLogo))) {
                $logoPath = public_path('storage/' . $cleanLogo);
            } elseif (file_exists(public_path($pengaturan->logo))) {
                $logoPath = public_path($pengaturan->logo);
            }
        }
        if (!$logoPath && file_exists(public_path('sneat/img/logowi.png'))) {
            $logoPath = public_path('sneat/img/logowi.png');
        }

        // Foto Siswa
        $fotoPath = null;
        if (!empty($dataSiswa?->foto_siswa)) {
            $cleanFoto = ltrim(preg_replace('/^storage\//', '', $dataSiswa->foto_siswa), '/');
            if (file_exists(public_path('storage/' . $cleanFoto))) {
                $fotoPath = public_path('storage/' . $cleanFoto);
            } elseif (file_exists(public_path($dataSiswa->foto_siswa))) {
                $fotoPath = public_path($dataSiswa->foto_siswa);
            }
        }
    @endphp

    <!-- KOP FORMULIR -->
    <table class="kop-table">
        <tr>
            @if($logoPath)
            <td style="width: 65px; text-align: center;">
                <img src="{{ $logoPath }}" class="kop-logo">
            </td>
            @endif
            <td style="padding-left: 10px;">
                <h1 class="kop-title">{{ strtoupper($namaSekolah) }}</h1>
                <p class="kop-sub">{{ $alamatSekolah }} &ndash; Telp. {{ $teleponSekolah }}</p>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <h3 class="form-title">FORMULIR PENDAFTARAN {{ strtoupper($tipeAplikasi) }}</h3>
    <h5 class="form-title">TAHUN AJARAN {{ $tahunAjaran }}</h5>
    <p class="form-number">No. Pendaftaran: <b>{{ $dataSiswa?->no_pendaftaran ?? $pendaftar->username }}</b></p>

    <!-- DATA PRIBADI SISWA & FOTO (MENYATU SEMPURNA TANPA CELAH) -->
    <table class="data-table" style="margin-top: 0; margin-bottom: 0;">
        <colgroup>
            <col style="width: 32mm;">
            <col style="width: 28%;">
            <col style="width: auto;">
        </colgroup>
        <thead>
            <tr>
                <th style="padding: 3.5px 1px; font-size: 8.5pt; text-align: center; white-space: nowrap;">FOTO (3 &times; 4)</th>
                <th colspan="2" style="font-size: 9pt; padding: 3.5px 6px; text-align: center;">DATA PRIBADI CALON SISWA</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td rowspan="6" class="photo-cell" style="width: 32mm; height: 39.5mm; text-align: center; vertical-align: middle; padding: 1mm 0;">
                    @if($fotoPath)
                        <img src="{{ $fotoPath }}" class="photo-img">
                    @else
                        <div class="photo-box-frame">
                            <div class="photo-box-content">
                                PAS FOTO<br>
                                3 &times; 4 cm<br>
                                <small>(Warna)</small>
                            </div>
                        </div>
                    @endif
                </td>
                <td class="lbl" style="padding: 3.8px 6px;">NISN</td>
                <td style="padding: 3.8px 6px;">{{ $dataSiswa?->nisn ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl" style="padding: 3.8px 6px;">Nama Lengkap</td>
                <td style="padding: 3.8px 6px;"><b style="font-size: 9pt;">{{ $dataSiswa?->nama_lengkap ?? $pendaftar->username }}</b></td>
            </tr>
            <tr>
                <td class="lbl" style="padding: 3.8px 6px;">NIK</td>
                <td style="padding: 3.8px 6px;">{{ $dataSiswa?->nik ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl" style="padding: 3.8px 6px;">No. KK</td>
                <td style="padding: 3.8px 6px;">{{ $dataSiswa?->no_kk ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl" style="padding: 3.8px 6px;">Tempat, Tgl Lahir</td>
                <td style="padding: 3.8px 6px;">
                    {{ $dataSiswa?->tempat_lahir ?? '-' }},
                    {{ $dataSiswa?->tanggal_lahir ? \Carbon\Carbon::parse($dataSiswa->tanggal_lahir)->format('d-m-Y') : '-' }}
                </td>
            </tr>
            <tr>
                <td class="lbl" style="padding: 3.8px 6px;">Jenis Kelamin</td>
                <td style="padding: 3.8px 6px;">{{ $dataSiswa?->jenis_kelamin ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- DETAIL DATA SISWA -->
    <table class="data-table" style="border-top: none;">
        <colgroup>
            <col style="width: 22%">
            <col style="width: 28%">
            <col style="width: 22%">
            <col style="width: 28%">
        </colgroup>
        <tr>
            <td class="lbl">Agama</td><td>{{ $dataSiswa?->agama ?? '-' }}</td>
            <td class="lbl">Anak Ke / Dari</td><td>{{ $dataSiswa?->anak_ke ?? '-' }} dari {{ $dataSiswa?->jumlah_saudara ?? '-' }} bersaudara</td>
        </tr>
        <tr>
            <td class="lbl">No. Handphone</td><td>{{ $dataSiswa?->no_hp ?? '-' }}</td>
            <td class="lbl">Asal Sekolah</td><td>{{ $dataSiswa?->asal_sekolah ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Jenjang / Status Asal</td><td>{{ $dataSiswa?->jenjang_sekolah_asal ?? '-' }} / {{ $dataSiswa?->status_sekolah_asal ?? '-' }}</td>
            <td class="lbl">Kecamatan/Desa Asal Sekolah</td><td>{{ $dataSiswa?->kecamatan_desa_sekolah_asal ?? $dataSiswa?->kota_sekolah_asal ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Tinggi / Berat Badan</td><td>{{ $dataSiswa?->tinggi_badan ?? '-' }} cm / {{ $dataSiswa?->berat_badan ?? '-' }} kg</td>
            <td class="lbl">Status / Tinggal Bersama</td><td>{{ $dataSiswa?->status_dalam_keluarga ?? '-' }} / {{ $dataSiswa?->tinggal_bersama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Alamat Lengkap</td>
            <td colspan="3">{{ $dataSiswa?->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">RT / RW</td><td>{{ $dataSiswa?->rt ?? '-' }} / {{ $dataSiswa?->rw ?? '-' }}</td>
            <td class="lbl">Kelurahan / Desa</td><td>{{ $dataSiswa?->desa ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Kecamatan</td><td>{{ $dataSiswa?->kecamatan ?? '-' }}</td>
            <td class="lbl">Kota / Kabupaten</td><td>{{ $dataSiswa?->kota ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Provinsi</td><td>{{ $dataSiswa?->provinsi ?? '-' }} (Kode Pos: {{ $dataSiswa?->kode_pos ?? '-' }})</td>
            <td class="lbl">Jarak / Waktu Tempuh</td><td>{{ $dataSiswa?->jarak_kesekolah ?? '-' }} m / {{ $dataSiswa?->waktu_tempuh ?? '-' }} menit</td>
        </tr>
        <tr>
            <td class="lbl">Pilihan Jurusan</td><td><b>{{ $dataSiswa?->jurusan?->nama_jurusan ?? '-' }}</b></td>
            <td class="lbl">Gelombang / No KIP</td><td>{{ $dataSiswa?->gelombang?->nama_gelombang ?? '-' }} / {{ $dataSiswa?->no_kip ?: '-' }}</td>
        </tr>
    </table>

    <!-- DATA ORANG TUA / WALI -->
    <div class="section-title">DATA ORANG TUA / WALI PESERTA DIDIK</div>
    <table class="data-table">
        <colgroup>
            <col style="width: 22%">
            <col style="width: 26%">
            <col style="width: 26%">
            <col style="width: 26%">
        </colgroup>
        <thead>
            <tr>
                <th>Keterangan</th>
                <th>Data Ayah</th>
                <th>Data Ibu</th>
                <th>Data Wali</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="lbl">Status Keberadaan</td>
                <td>{{ $dataSiswa?->status_ayah ?? 'Masih Hidup' }}</td>
                <td>{{ $dataSiswa?->status_ibu ?? 'Masih Hidup' }}</td>
                <td>-</td>
            </tr>
            <tr>
                <td class="lbl">NIK</td>
                <td>{{ $dataSiswa?->nik_ayah ?? '-' }}</td>
                <td>{{ $dataSiswa?->nik_ibu ?? '-' }}</td>
                <td>{{ $dataSiswa?->nik_wali ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Nama Lengkap</td>
                <td>{{ $dataSiswa?->nama_ayah ?? '-' }}</td>
                <td>{{ $dataSiswa?->nama_ibu ?? '-' }}</td>
                <td>{{ $dataSiswa?->nama_wali ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Tempat, Tgl Lahir</td>
                <td>{{ $dataSiswa?->tempat_lahir_ayah ?? '-' }}, {{ $dataSiswa?->tanggal_lahir_ayah ? \Carbon\Carbon::parse($dataSiswa->tanggal_lahir_ayah)->format('d-m-Y') : '-' }}</td>
                <td>{{ $dataSiswa?->tempat_lahir_ibu ?? '-' }}, {{ $dataSiswa?->tanggal_lahir_ibu ? \Carbon\Carbon::parse($dataSiswa->tanggal_lahir_ibu)->format('d-m-Y') : '-' }}</td>
                <td>{{ $dataSiswa?->tempat_lahir_wali ?? '-' }}, {{ $dataSiswa?->tanggal_lahir_wali ? \Carbon\Carbon::parse($dataSiswa->tanggal_lahir_wali)->format('d-m-Y') : '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Pendidikan Terakhir</td>
                <td>{{ $dataSiswa?->pendidikan_ayah ?? '-' }}</td>
                <td>{{ $dataSiswa?->pendidikan_ibu ?? '-' }}</td>
                <td>{{ $dataSiswa?->pendidikan_wali ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Pekerjaan</td>
                <td>{{ $dataSiswa?->pekerjaan_ayah ?? '-' }}</td>
                <td>{{ $dataSiswa?->pekerjaan_ibu ?? '-' }}</td>
                <td>{{ $dataSiswa?->pekerjaan_wali ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Penghasilan Bulanan</td>
                <td>{{ $dataSiswa?->penghasilan_ayah ?? '-' }}</td>
                <td>{{ $dataSiswa?->penghasilan_ibu ?? '-' }}</td>
                <td>{{ $dataSiswa?->penghasilan_wali ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">No. Handphone</td>
                <td>{{ $dataSiswa?->no_hp_ayah ?? '-' }}</td>
                <td>{{ $dataSiswa?->no_hp_ibu ?? '-' }}</td>
                <td>{{ $dataSiswa?->no_hp_wali ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- PERNYATAAN DAN PERSETUJUAN -->
    <div class="pernyataan-box">
        <p>
            <strong>Pernyataan:</strong> Dengan ini saya menyatakan bahwa seluruh data yang diisi dalam formulir pendaftaran ini adalah benar dan dapat dipertanggungjawabkan sesuai aslinya. Apabila di kemudian hari terbukti ada pemalsuan atau ketidaksesuaian data, saya bersedia menerima sanksi yang ditetapkan oleh panitia {{ $tipeAplikasi }} {{ $namaSekolah }}.
        </p>
        <table class="ttd-table">
            <tr>
                <td style="width: 50%;">
                    Mengetahui,<br>Orang Tua / Wali Siswa
                    <div class="ttd-space"></div>
                    <div class="ttd-line">
                        <strong>( {{ $dataSiswa?->nama_ibu ?: ($dataSiswa?->nama_ayah ?: '........................................') }} )</strong>
                    </div>
                </td>
                <td style="width: 50%;">
                    {{ $pengaturan?->kota ?? 'Jakarta' }}, {{ now()->translatedFormat('d F Y') }}<br>Calon Peserta Didik Baru
                    <div class="ttd-space"></div>
                    <div class="ttd-line">
                        <strong>( {{ $dataSiswa?->nama_lengkap ?? $pendaftar->username }} )</strong>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer-bar">
        Dicetak otomatis dari Sistem {{ $tipeAplikasi }} &bull; Tanggal: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} WIB
    </div>
</div>
</body>
</html>
