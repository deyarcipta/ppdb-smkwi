@php
    $pengaturan = $pengaturan ?? \App\Models\PengaturanAplikasi::first();
    $tipeAplikasi = $pengaturan->tipe_aplikasi ?? 'SPMB';
    $namaSekolah = $pengaturan->nama_sekolah ?? 'SMKS YASPIA';
    
    $tahunAjaranPeserta = $dataSiswa?->tahunAjaran?->nama 
        ?? $dataSiswa?->gelombang?->tahunAjaran?->nama 
        ?? $pengaturan?->tahunAjaranAktif?->nama 
        ?? date('Y') . '/' . (date('Y') + 1);

    // Identitas Siswa
    $namaSiswa = $dataSiswa?->nama_lengkap ?? null;
    $nisnSiswa = $dataSiswa?->nisn ?? null;
    $alamatSiswa = $dataSiswa?->alamat ?? null;
    $rtSiswa = $dataSiswa?->rt ?? null;
    $rwSiswa = $dataSiswa?->rw ?? null;
    $kelSiswa = $dataSiswa?->desa ?? null;
    $kecSiswa = $dataSiswa?->kecamatan ?? null;
    $kotaSiswa = $dataSiswa?->kota ?? null;

    // Pilihan Keahlian / Jurusan
    $jurusanSiswa = $dataSiswa?->jurusan?->nama_jurusan ?? null;
    $pilihanKeahlian = $jurusanSiswa ?: 'Perhotelan/Kuliner';

    // Nama Orang Tua
    $namaOrtu = $dataSiswa?->nama_ibu ?? $dataSiswa?->nama_ayah ?? $dataSiswa?->nama_wali ?? null;

    // Lokasi & Tanggal Surat
    $kotaSurat = $pengaturan?->kota ?? 'Jakarta';
    $tanggalSurat = now()->translatedFormat('d F Y');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Pernyataan Sehat - {{ $namaSiswa ?? 'SPMB' }}</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 22mm 24mm 22mm 24mm;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11.5pt;
            color: #000;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background: #fff;
        }

        .title-container {
            text-align: center;
            margin-top: 14px;
            margin-bottom: 28px;
        }

        .doc-title {
            font-size: 14.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            padding-bottom: 2px;
        }

        .intro-text {
            margin: 0 0 16px 0;
            font-size: 11.5pt;
        }

        .identity-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
            font-size: 11.5pt;
        }

        .identity-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .identity-table .lbl {
            width: 85px;
        }

        .identity-table .sep {
            width: 20px;
            text-align: center;
        }

        .identity-table .val {
            word-break: break-word;
        }

        .dots-line {
            display: inline-block;
            border-bottom: 1px dotted #333;
            min-height: 16px;
        }

        .statement-title {
            margin: 0 0 12px 0;
            font-size: 11.5pt;
        }

        .statement-p1 {
            text-align: justify;
            line-height: 1.65;
            margin: 0 0 16px 0;
            font-size: 11.5pt;
        }

        .keahlian-box {
            font-weight: bold;
            font-size: 12pt;
            margin: 0 0 22px 0;
        }

        .statement-p2 {
            text-align: justify;
            line-height: 1.65;
            margin: 0 0 36px 0;
            font-size: 11.5pt;
        }

        /* Signatures */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5pt;
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

        .name-line {
            display: inline-block;
            border-bottom: 1px dotted #222;
            min-width: 200px;
            max-width: 260px;
            font-weight: bold;
            padding-bottom: 2px;
        }

        .footnote {
            margin-top: 40px;
            font-size: 9.5pt;
            font-style: italic;
        }
    </style>
</head>
<body>

    <div class="title-container">
        <h1 class="doc-title">SURAT PERNYATAAN SEHAT</h1>
    </div>

    <p class="intro-text">Yang bertanda tangan di bawah ini:</p>

    <table class="identity-table">
        <tr>
            <td class="lbl">Nama</td>
            <td class="sep">:</td>
            <td class="val">
                @if($namaSiswa)
                    <strong>{{ $namaSiswa }}</strong>
                @else
                    ………………………………………………………………………………………..
                @endif
            </td>
        </tr>
        <tr>
            <td class="lbl">NISN</td>
            <td class="sep">:</td>
            <td class="val">
                @if($nisnSiswa)
                    <strong>{{ $nisnSiswa }}</strong>
                @else
                    ………………………………………………………………………………………..
                @endif
            </td>
        </tr>
        <tr>
            <td class="lbl">Alamat</td>
            <td class="sep">:</td>
            <td class="val">
                @if($alamatSiswa)
                    {{ $alamatSiswa }}
                @else
                    ………………………………………………………………………………………..
                @endif
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td class="val">
                RT <span class="dots-line" style="min-width: 35px; text-align: center;">{{ $rtSiswa ?: '…………' }}</span>
                RW <span class="dots-line" style="min-width: 35px; text-align: center;">{{ $rwSiswa ?: '…………' }}</span>
                Kelurahan <span class="dots-line" style="min-width: 160px;">{{ $kelSiswa ?: '……………………………………..' }}</span>
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td class="val">
                Kecamatan <span class="dots-line" style="min-width: 170px;">{{ $kecSiswa ?: '…………………………………………………….' }}</span>
                Kota <span class="dots-line" style="min-width: 120px;">{{ $kotaSiswa ?: '………….' }}</span>
            </td>
        </tr>
    </table>

    <p class="statement-title">Dengan ini menyatakan bahwa:</p>

    <p class="statement-p1">
        Saya dalam kondisi <strong>SEHAT</strong> dan <strong>SIAP</strong> untuk pemenuhan aspek Kesehatan yang dipersyaratkan dalam {{ $tipeAplikasi }} {{ $namaSekolah }} pada pilihan Konsentrasi Keahlian:
    </p>

    <div class="keahlian-box">
        {{ $pilihanKeahlian }} )*
    </div>

    <p class="statement-p2">
        Demikian surat pernyataan ini kami buat dengan sesungguhnya dan penuh tanggungjawab dan apabila dikemudian hari diperoleh Kesimpulan bahwa surat Pernyataan ini tidak benar, maka saya siap menerima sanksi <strong>DIBATALKAN</strong> sebagai Peserta {{ $tipeAplikasi }} meskipun saya dinyatakan <strong>DITERIMA</strong> dalam seleksi {{ $tipeAplikasi }} {{ $namaSekolah }} Tahun Ajaran {{ $tahunAjaranPeserta }}.
    </p>

    <table class="signature-table">
        <tr>
            <td class="signature-col-left">
                Mengetahui,<br>
                Orang Tua/Wali
                <div style="height: 104px;"></div>
                <div class="name-line">
                    {{ $namaOrtu ?: '………………………………………' }}
                </div>
            </td>
            <td class="signature-col-right">
                {{ $kotaSurat }}, {{ $dataSiswa ? $tanggalSurat : '…………………..20………' }}<br>
                Yang membuat pernyataan
                <div>
                    <div class="materai-box">Materai 10.000</div>
                </div>
                <div class="name-line">
                    {{ $namaSiswa ?: '………………..…………………..' }}
                </div>
            </td>
        </tr>
    </table>

    <div class="footnote">
        )* Coret yang tidak perlu
    </div>

</body>
</html>
