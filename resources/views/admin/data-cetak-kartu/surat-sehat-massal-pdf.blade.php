@php
    $pengaturan = $pengaturan ?? \App\Models\PengaturanAplikasi::first();
    $tipeAplikasi = $pengaturan->tipe_aplikasi ?? 'PPDB';
    $namaSekolah = $pengaturan->nama_sekolah ?? 'SMK Wisata Indonesia';
    $kotaSurat = $pengaturan?->kota ?? 'Jakarta';
    $tanggalSurat = now()->translatedFormat('d F Y');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Pernyataan Sehat Massal - {{ $tipeAplikasi }}</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 22mm 24mm 20mm 24mm;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            color: #000;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background: #fff;
        }

        .title-container {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 26px;
        }

        .doc-title {
            font-size: 13.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            padding-bottom: 2px;
        }

        .intro-text {
            margin: 0 0 14px 0;
            font-size: 11pt;
        }

        .identity-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 11pt;
        }

        .identity-table td {
            padding: 3.5px 0;
            vertical-align: top;
        }

        .identity-table .lbl {
            width: 80px;
        }

        .identity-table .sep {
            width: 20px;
            text-align: center;
        }

        .identity-table .val {
            word-break: break-word;
        }

        .statement-title {
            margin: 0 0 10px 0;
            font-size: 11pt;
        }

        .statement-p1 {
            text-align: justify;
            line-height: 1.55;
            margin: 0 0 14px 0;
            font-size: 11pt;
        }

        .keahlian-box {
            font-weight: bold;
            font-size: 11.5pt;
            margin: 0 0 18px 0;
        }

        .statement-p2 {
            text-align: justify;
            line-height: 1.55;
            margin: 0 0 32px 0;
            font-size: 11pt;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
            margin-top: 10px;
        }

        .signature-table td {
            vertical-align: top;
        }

        .signature-col-left {
            width: 50%;
            text-align: left;
        }

        .signature-col-right {
            width: 50%;
            text-align: left;
            padding-left: 15px;
        }

        .materai-box {
            display: inline-block;
            border: 1px solid #444;
            width: 125px;
            height: 54px;
            text-align: center;
            line-height: 54px;
            font-size: 9.5pt;
            color: #333;
            margin: 14px 0 28px 0;
            background: #fff;
        }

        .ttd-space-normal {
            height: 104px;
        }

        .page-break {
            page-break-after: always;
            break-after: page;
        }
    </style>
</head>
<body>

@foreach($dataSiswaList as $dataSiswa)
    @php
        $user = $dataSiswa->user;
        $namaSiswa = $dataSiswa->nama_lengkap ?? null;
        $nisnSiswa = $dataSiswa->nisn ?? null;

        $alamatLengkap = $dataSiswa->alamat ?? '';
        $rtrw = [];
        if (!empty($dataSiswa->rt)) $rtrw[] = 'RT ' . $dataSiswa->rt;
        if (!empty($dataSiswa->rw)) $rtrw[] = 'RW ' . $dataSiswa->rw;
        if (!empty($rtrw)) $alamatLengkap .= ' (' . implode('/', $rtrw) . ')';
        if (!empty($dataSiswa->desa)) $alamatLengkap .= ', Kel. ' . $dataSiswa->desa;
        if (!empty($dataSiswa->kecamatan)) $alamatLengkap .= ', Kec. ' . $dataSiswa->kecamatan;
        if (!empty($dataSiswa->kota)) $alamatLengkap .= ', ' . $dataSiswa->kota;

        $jurusanSiswa = $dataSiswa->jurusan?->nama_jurusan ?? null;
        $pilihanKeahlian = $jurusanSiswa ?: 'Perhotelan / Kuliner';

        $namaOrtu = $dataSiswa->nama_ibu ?? $dataSiswa->nama_ayah ?? $dataSiswa->nama_wali ?? null;
    @endphp

    <div class="sheet-container">
        <!-- Judul Dokumen -->
        <div class="title-container">
            <h1 class="doc-title">SURAT PERNYATAAN SEHAT</h1>
        </div>

        <!-- Kalimat Pengantar -->
        <p class="intro-text">Saya yang bertanda tangan di bawah ini:</p>

        <!-- Tabel Identitas Calon Siswa -->
        <table class="identity-table">
            <tr>
                <td class="lbl">Nama</td>
                <td class="sep">:</td>
                <td class="val"><strong>{{ $namaSiswa ?: '....................................................................................' }}</strong></td>
            </tr>
            <tr>
                <td class="lbl">NISN</td>
                <td class="sep">:</td>
                <td class="val">{{ $nisnSiswa ?: '....................................................................................' }}</td>
            </tr>
            <tr>
                <td class="lbl">Alamat</td>
                <td class="sep">:</td>
                <td class="val">{{ $alamatLengkap ?: '....................................................................................' }}</td>
            </tr>
        </table>

        <!-- Paragraf Pernyataan -->
        <p class="statement-title">Menyatakan bahwa:</p>

        <p class="statement-p1">
            Saya dalam keadaan sehat jasmani dan rohani, tidak memiliki riwayat penyakit menular, 
            tato, tindik, serta tidak buta warna. Pernyataan ini saya buat dengan sebenarnya untuk 
            memenuhi salah satu persyaratan pendaftaran seleksi masuk {{ $namaSekolah }} pada 
            Program Keahlian:
        </p>

        <!-- Pilihan Program Keahlian -->
        <div class="keahlian-box">
            Pilihan Keahlian : {{ $pilihanKeahlian }}
        </div>

        <p class="statement-p2">
            Demikian surat pernyataan ini saya buat dengan penuh kesadaran dan tanpa paksaan 
            dari pihak manapun, untuk dapat dipergunakan sebagaimana mestinya.
        </p>

        <!-- Bagian Tanda Tangan -->
        <table class="signature-table">
            <tr>
                <td class="signature-col-left">
                    Mengetahui,<br>
                    Orang Tua / Wali Siswa
                    <div class="ttd-space-normal"></div>
                    ( <strong>{{ $namaOrtu ?: '...................................................' }}</strong> )
                </td>
                <td class="signature-col-right">
                    {{ $kotaSurat }}, {{ $tanggalSurat }}<br>
                    Yang Membuat Pernyataan,<br>
                    <div class="materai-box">
                        Materai 10.000
                    </div><br>
                    ( <strong>{{ $namaSiswa ?: '...................................................' }}</strong> )
                </td>
            </tr>
        </table>
    </div>

    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach

</body>
</html>
