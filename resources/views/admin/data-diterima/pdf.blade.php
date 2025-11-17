<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Formulir Pendaftaran - {{ $pendaftar->dataSiswa->nama_lengkap ?? $pendaftar->username }}</title>
<style>
    @page {
        size: A4;
        margin: 1.7cm 1.7cm;
    }

    body {
        font-family: "Times New Roman", serif;
        font-size: 10.2pt;
        color: #000;
        margin: 0;
        padding: 0;
        text-align: justify;
        background: #fff;
    }

    .container {
        width: 17.2cm;
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
    <div class="header">
      <div class="header-text">
          <h1>SMK WISATA INDONESIA</h1>
          <h2>Jl. Raya Lenteng Agung I, Jakarta Selatan – Telp. (021) xxxx xxxx</h2>
      </div>
  </div>

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
            <td><b>Tinggi Badan (cm)</b></td><td>{{ $pendaftar->dataSiswa->tinggi_badan ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Alamat Siswa</b></td>
            <td colspan="3">{{ $pendaftar->dataSiswa->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>RT / RW</b></td><td>{{ $pendaftar->dataSiswa->rt ?? '-' }}/{{ $pendaftar->dataSiswa->rw ?? '-' }}</td>
            <td><b>Berat Badan (kg)</b></td><td>{{ $pendaftar->dataSiswa->berat_badan ?? '-' }}</td>
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

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }}
    </div>
</div>
</body>
</html>
