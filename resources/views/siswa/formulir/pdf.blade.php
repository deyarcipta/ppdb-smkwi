<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Formulir Pendaftaran - {{ $pendaftar->dataSiswa->nama_lengkap ?? $pendaftar->username }}</title>
<style>
    @page {
        size: 215mm 330mm;
        margin: 1cm 1.5cm;
    }

    body {
        font-family: "Times New Roman", serif;
        font-size: 9.5pt;
        color: #000;
        margin: 0;
        padding: 0;
        line-height: 1.25;
        background: #fff;
    }

    .container {
        width: 100%;
        margin: 0 auto;
    }

    /* ==== HEADER ==== */
    .header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 6px;
    }

    .header-logo {
        width: 70px;
        height: 70px;
        object-fit: contain;
    }

    .header-text {
        flex: 1;
        text-align: left;
    }

    .header-text h1 {
        font-size: 14pt;
        font-weight: bold;
        margin: 0;
    }

    .header-text h2 {
        font-size: 9.5pt;
        margin: 2px 0 0 0;
    }

    .divider {
        border-top: 2px solid #000;
        margin: 6px 0 10px 0;
    }

    h3.form-title {
        text-align: center;
        font-size: 11.5pt;
        font-weight: bold;
        text-decoration: underline;
        margin: 0 0 4px 0;
    }

    .form-number {
        text-align: center;
        font-size: 9.5pt;
        margin-bottom: 6px;
    }

    /* ==== TABLES & CONTENT ==== */
    .section-title {
        background: #e0e0e0;
        border: 1px solid #000;
        text-align: center;
        font-weight: bold;
        font-size: 10pt;
        padding: 3px;
        margin-top: 8px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 4px;
        table-layout: fixed;
    }

    th, td {
        border: 1px solid #000;
        padding: 4px 6px;
        font-size: 9.5pt;
        vertical-align: top;
        word-wrap: break-word;
        overflow-wrap: break-word;
        white-space: normal;
    }

    th {
        text-align: center;
    }

    .photo-cell {
        width: 80px;
        height: 95px;
        text-align: center;
        font-size: 8.5pt;
    }

    .footer {
        text-align: right;
        font-size: 9pt;
        margin-top: 12px;
    }

    .spacer {
        height: 6px;
    }
</style>

</head>
<body>
<div class="container">
    @php
        $pengaturan = $pengaturan ?? \App\Models\PengaturanAplikasi::first();
    @endphp
    <table style="width: 100%; border: none; margin-bottom: 4px;">
        <tr style="border: none;">
            @php
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
            @endphp
            @if($logoPath)
            <td style="width: 75px; border: none; vertical-align: middle; padding: 0;">
                <img src="{{ $logoPath }}" style="width: 65px; height: 65px; object-fit: contain;">
            </td>
            @endif
            <td style="border: none; vertical-align: middle; text-align: left; padding-left: 8px;">
                <h1 style="font-size: 14pt; font-weight: bold; margin: 0; line-height: 1.2;">{{ strtoupper($pengaturan->nama_sekolah ?? 'SMK WISATA INDONESIA') }}</h1>
                <h2 style="font-size: 9pt; font-weight: normal; margin: 2px 0 0 0;">{{ $pengaturan->alamat ?? 'Jl. Raya Lenteng Agung I, Jakarta Selatan' }} – Telp. {{ $pengaturan->telepon ?? '-' }}</h2>
            </td>
        </tr>
    </table>

  <div class="divider"></div>

  <h3 class="form-title">FORMULIR PENDAFTARAN</h3>
  <p class="form-number">No. Pendaftaran : <b>{{ $pendaftar->dataSiswa->no_pendaftaran ?? $pendaftar->username }}</b></p>


    <!-- DATA PRIBADI SISWA -->
    <table>
        <tr>
            <th width="100">FOTO SISWA</th>
            <th colspan="2">DATA PRIBADI SISWA</th>
        </tr>
        <tr>
            <td class="photo-cell" rowspan="4">FOTO<br>3x4</td>
            <td width="35%"><b>NISN</b></td>
            <td>{{ $pendaftar->dataSiswa->nisn ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Nama Lengkap</b></td>
            <td>{{ $pendaftar->dataSiswa->nama_lengkap ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Tempat, Tgl Lahir</b></td>
            <td>
                {{ $pendaftar->dataSiswa->tempat_lahir ?? '-' }},
                {{ $pendaftar->dataSiswa->tanggal_lahir ? \Carbon\Carbon::parse($pendaftar->dataSiswa->tanggal_lahir)->format('d-m-Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td><b>Jenis Kelamin</b></td>
            <td>{{ $pendaftar->dataSiswa->jenis_kelamin ?? '-' }}</td>
        </tr>
    </table>

    <div class="spacer"></div>

    <!-- DETAIL SISWA -->
    <table>
        <colgroup>
            <col style="width: 25%">
            <col style="width: 25%">
            <col style="width: 25%">
            <col style="width: 25%">
        </colgroup>

        <tr>
            <td><b>No NIK</b></td><td>{{ $pendaftar->dataSiswa->nik ?? '-' }}</td>
            <td><b>No Kartu Keluarga</b></td><td>{{ $pendaftar->dataSiswa->no_kk ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Agama</b></td><td>{{ $pendaftar->dataSiswa->agama ?? '-' }}</td>
            <td><b>Anak Ke</b></td><td>{{ $pendaftar->dataSiswa->anak_ke ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>No Handphone</b></td><td>{{ $pendaftar->dataSiswa->no_hp ?? '-' }}</td>
            <td><b>Saudara</b></td><td>{{ $pendaftar->dataSiswa->jumlah_saudara ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Asal Sekolah</b></td><td>{{ $pendaftar->dataSiswa->asal_sekolah ?? '-' }}</td>
            <td><b>Jenjang Sekolah</b></td><td>{{ $pendaftar->dataSiswa->jenjang_sekolah_asal ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Status Sekolah</b></td><td>{{ $pendaftar->dataSiswa->status_sekolah_asal ?? '-' }}</td>
            <td><b>Kota Asal Sekolah</b></td><td>{{ $pendaftar->dataSiswa->kota_sekolah_asal ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Tinggi Badan (cm)</b></td><td>{{ $pendaftar->dataSiswa->tinggi_badan ?? '-' }}</td>
            <td><b>Berat Badan (kg)</b></td><td>{{ $pendaftar->dataSiswa->berat_badan ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Alamat Siswa</b></td>
            <td colspan="3">{{ $pendaftar->dataSiswa->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>RT / RW</b></td><td>{{ $pendaftar->dataSiswa->rt ?? '-' }}/{{ $pendaftar->dataSiswa->rw ?? '-' }}</td>
            <td><b>Status Dalam Keluarga</b></td><td>{{ $pendaftar->dataSiswa->status_dalam_keluarga ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Kelurahan</b></td><td>{{ $pendaftar->dataSiswa->desa ?? '-' }}</td>
            <td><b>Status Dalam Keluarga</b></td><td>{{ $pendaftar->dataSiswa->status_dalam_keluarga ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Kecamatan</b></td><td>{{ $pendaftar->dataSiswa->kecamatan ?? '-' }}</td>
            <td><b>Tinggal Bersama</b></td><td>{{ $pendaftar->dataSiswa->tinggal_bersama ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Kota / Kabupaten</b></td><td>{{ $pendaftar->dataSiswa->kota ?? '-' }}</td>
            <td><b>Jarak ke Sekolah (m)</b></td><td>{{ $pendaftar->dataSiswa->jarak_kesekolah ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Provinsi</b></td><td>{{ $pendaftar->dataSiswa->provinsi ?? '-' }}</td>
            <td><b>Waktu Tempuh (menit)</b></td><td>{{ $pendaftar->dataSiswa->waktu_tempuh ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Kode Pos</b></td><td>{{ $pendaftar->dataSiswa->kode_pos ?? '-' }}</td>
            <td><b>No KIP</b></td><td>{{ $pendaftar->dataSiswa->no_kip ?? '-' }}</td>
        </tr>
    </table>

    <!-- ORANG TUA / WALI -->
    <div class="section-title">DATA ORANG TUA / WALI</div>
    <table>
        <thead>
        <tr>
            <th width="20%"></th>
            <th>Data Ayah</th>
            <th>Data Ibu</th>
            <th>Data Wali</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><b>NIK</b></td>
            <td>{{ $pendaftar->dataSiswa->nik_ayah ?? '-' }}</td>
            <td>{{ $pendaftar->dataSiswa->nik_ibu ?? '-' }}</td>
            <td>{{ $pendaftar->dataSiswa->nik_wali ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Nama Lengkap</b></td>
            <td>{{ $pendaftar->dataSiswa->nama_ayah ?? '-' }}</td>
            <td>{{ $pendaftar->dataSiswa->nama_ibu ?? '-' }}</td>
            <td>{{ $pendaftar->dataSiswa->nama_wali ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Tempat, Tgl Lahir</b></td>
            <td>{{ $pendaftar->dataSiswa->tempat_lahir_ayah ?? '-' }}, {{ $pendaftar->dataSiswa->tanggal_lahir_ayah ? \Carbon\Carbon::parse($pendaftar->dataSiswa->tanggal_lahir_ayah)->format('d-m-Y') : '-' }}</td>
            <td>{{ $pendaftar->dataSiswa->tempat_lahir_ibu ?? '-' }}, {{ $pendaftar->dataSiswa->tanggal_lahir_ibu ? \Carbon\Carbon::parse($pendaftar->dataSiswa->tanggal_lahir_ibu)->format('d-m-Y') : '-' }}</td>
            <td>{{ $pendaftar->dataSiswa->tempat_lahir_wali ?? '-' }}, {{ $pendaftar->dataSiswa->tanggal_lahir_wali ? \Carbon\Carbon::parse($pendaftar->dataSiswa->tanggal_lahir_wali)->format('d-m-Y') : '-' }}</td>
        </tr>
        <tr>
            <td><b>Pendidikan</b></td>
            <td>{{ $pendaftar->dataSiswa->pendidikan_ayah ?? '-' }}</td>
            <td>{{ $pendaftar->dataSiswa->pendidikan_ibu ?? '-' }}</td>
            <td>{{ $pendaftar->dataSiswa->pendidikan_wali ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Pekerjaan</b></td>
            <td>{{ $pendaftar->dataSiswa->pekerjaan_ayah ?? '-' }}</td>
            <td>{{ $pendaftar->dataSiswa->pekerjaan_ibu ?? '-' }}</td>
            <td>{{ $pendaftar->dataSiswa->pekerjaan_wali ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Penghasilan</b></td>
            <td>{{ $pendaftar->dataSiswa->penghasilan_ayah ?? '-' }}</td>
            <td>{{ $pendaftar->dataSiswa->penghasilan_ibu ?? '-' }}</td>
            <td>{{ $pendaftar->dataSiswa->penghasilan_wali ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>No HP</b></td>
            <td>{{ $pendaftar->dataSiswa->no_hp_ayah ?? '-' }}</td>
            <td>{{ $pendaftar->dataSiswa->no_hp_ibu ?? '-' }}</td>
            <td>{{ $pendaftar->dataSiswa->no_hp_wali ?? '-' }}</td>
        </tr>
        </tbody>
    </table>

    <div class="section-title" style="margin-top: 3px;">PERNYATAAN DAN PERSETUJUAN</div>
    <table style="margin-top: 2px;">
        <tr>
            <td style="padding: 3px 6px; font-size: 7.8pt; text-align: justify;">
                <p style="margin: 0 0 3px 0; line-height: 1.2;">
                    Dengan mengisi formulir ini, saya menyatakan bahwa semua data yang saya berikan adalah benar dan dapat dipertanggungjawabkan. Saya bersedia mematuhi semua peraturan dan tata tertib yang berlaku di {{ $pengaturan->nama_sekolah ?? 'SMK Wisata Indonesia' }}.
                </p>
                <table style="width: 100%; border: none; margin: 2px 0 0 0;">
                    <tr style="border: none;">
                        <td style="width: 50%; border: none; text-align: center; padding: 0;">
                            <div style="height: 32px;"></div>
                            <div style="border-top: 1px solid #000; width: 65%; margin: 0 auto; padding-top: 2px; font-size: 7.8pt;">
                                <strong>.........................</strong><br>
                                <em>(Orang Tua/Wali)</em>
                            </div>
                        </td>
                        <td style="width: 50%; border: none; text-align: center; padding: 0;">
                            <div style="height: 32px;"></div>
                            <div style="border-top: 1px solid #000; width: 65%; margin: 0 auto; padding-top: 2px; font-size: 7.8pt;">
                                <strong>{{ $pendaftar->dataSiswa->nama_lengkap ?? $pendaftar->username }}</strong><br>
                                <em>(Siswa/Peserta Didik)</em>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }}
    </div>
</div>
</body>
</html>
