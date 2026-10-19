/**
 * UNIVERSAL PRINT FORMULIR
 * Dipakai oleh ADMIN & SISWA
 * id = id modal, contoh: doPrintFormulir(5, "formulirModal")
 * atau doPrintFormulir("formulirModal5")
 */

window.doPrintFormulir = function (id, modalPrefix = "formulirModal") {
    // 1. Jika di halaman Siswa, arahkan langsung ke route download formulir resmi
    if (window.location.pathname.includes("/siswa/")) {
        window.open("/siswa/download-formulir", "_blank");
        return;
    }

    // 2. Jika di halaman Admin/Panel
    let cleanId = null;
    if (typeof id === "number") {
        cleanId = id;
    } else if (typeof id === "string") {
        const match = id.match(/\d+/);
        if (match) cleanId = match[0];
    } else if (typeof id === "object" && id !== null) {
        if (id.id) {
            const match = String(id.id).match(/\d+/);
            if (match) cleanId = match[0];
        } else if (id.getAttribute) {
            const elId = id.getAttribute("id") || id.getAttribute("data-id");
            if (elId) {
                const match = String(elId).match(/\d+/);
                if (match) cleanId = match[0];
            }
        }
    }

    if (cleanId) {
        window.open(
            "/panel/data-pendaftar/" + cleanId + "/cetak-formulir",
            "_blank",
        );
        return;
    }

    // Fallback jika tidak terdeteksi ID spesifik tapi ada di siswa
    window.open("/siswa/download-formulir", "_blank");
};

const configEl = document.getElementById("appConfig");
let logoRaw = configEl?.dataset?.logoSekolah || "/sneat/img/logowi.png";
if (logoRaw) {
    logoRaw = logoRaw.replace(/\/storage\/storage\//g, "/storage/");
    logoRaw = logoRaw.replace(/^storage\/storage\//g, "storage/");
}
let finalLogoUrl = logoRaw;
if (
    logoRaw &&
    !logoRaw.startsWith("http://") &&
    !logoRaw.startsWith("https://") &&
    !logoRaw.startsWith("data:") &&
    !logoRaw.startsWith("/")
) {
    finalLogoUrl = "/" + logoRaw;
}

const APP_CONFIG = {
    namasekolah: configEl?.dataset?.namaSekolah || "SMK WISATA INDONESIA",
    namaaplikasi: configEl?.dataset?.namaAplikasi || "PPDB",
    logosekolah: finalLogoUrl,
    alamatsekolah:
        configEl?.dataset?.alamatSekolah ||
        "Jl. Raya Lenteng Agung No. 15, Jakarta Selatan",
    teleponsekolah: configEl?.dataset?.teleponSekolah || "(021) 7888-1234",
    emailsekolah:
        configEl?.dataset?.emailSekolah || "info@smkwisataindonesia.sch.id",
};

// Ambil data dari data attributes
const namaLengkap = modal.getAttribute("data-nama") || "-";
const noPendaftaran = modal.getAttribute("data-no-pendaftaran") || "-";
const fotoSiswa = modal.getAttribute("data-foto-siswa") || "";

// Parse URL foto siswa dengan aman (mencegah duplikasi /storage//storage/)
let finalFotoUrl = "";
if (
    fotoSiswa &&
    fotoSiswa !== "-" &&
    fotoSiswa !== "null" &&
    fotoSiswa !== "undefined" &&
    String(fotoSiswa).trim() !== ""
) {
    const trimmed = String(fotoSiswa).trim();
    if (
        trimmed.startsWith("http://") ||
        trimmed.startsWith("https://") ||
        trimmed.startsWith("data:") ||
        trimmed.startsWith("blob:")
    ) {
        finalFotoUrl = trimmed;
    } else if (trimmed.startsWith("/storage/")) {
        finalFotoUrl = trimmed;
    } else if (trimmed.startsWith("storage/")) {
        finalFotoUrl = "/" + trimmed;
    } else if (trimmed.startsWith("/")) {
        finalFotoUrl = trimmed;
    } else {
        finalFotoUrl = "/storage/" + trimmed;
    }
}
const nisn = modal.getAttribute("data-nisn") || "-";
const nik = modal.getAttribute("data-nik") || "-";
const noKk = modal.getAttribute("data-no-kk") || "-";
const tempatLahir = modal.getAttribute("data-tempat-lahir") || "-";
const tanggalLahir = modal.getAttribute("data-tanggal-lahir") || "-";
const jenisKelamin = modal.getAttribute("data-jenis-kelamin") || "-";
const agama = modal.getAttribute("data-agama") || "-";
const noHp = modal.getAttribute("data-no-hp") || "-";
const asalSekolah = modal.getAttribute("data-asal-sekolah") || "-";
const jenjangSekolah = modal.getAttribute("data-jenjang-sekolah") || "-";
const statusSekolah = modal.getAttribute("data-status-sekolah") || "-";
const kotaSekolah = modal.getAttribute("data-kecamatan-desa-sekolah") || modal.getAttribute("data-kota-sekolah") || "-";
const alamat = modal.getAttribute("data-alamat") || "-";
const rt = modal.getAttribute("data-rt") || "-";
const rw = modal.getAttribute("data-rw") || "-";
const desa = modal.getAttribute("data-desa") || "-";
const kecamatan = modal.getAttribute("data-kecamatan") || "-";
const kota = modal.getAttribute("data-kota") || "-";
const provinsi = modal.getAttribute("data-provinsi") || "-";
const kodePos = modal.getAttribute("data-kode-pos") || "-";
const tinggiBadan = modal.getAttribute("data-tinggi-badan") || "-";
const beratBadan = modal.getAttribute("data-berat-badan") || "-";
const anakKe = modal.getAttribute("data-anak-ke") || "-";
const jumlahSaudara = modal.getAttribute("data-jumlah-saudara") || "-";
const statusDalamKeluarga =
    modal.getAttribute("data-status-dalam-keluarga") || "-";
const tinggalBersama = modal.getAttribute("data-tinggal-bersama") || "-";
const jarakKesekolah = modal.getAttribute("data-jarak-kesekolah") || "-";
const waktuTempuh = modal.getAttribute("data-waktu-tempuh") || "-";
const transportasi = modal.getAttribute("data-transportasi") || "-";
const ukuranBaju = modal.getAttribute("data-ukuran-baju") || "-";
const hobi = modal.getAttribute("data-hobi") || "-";
const citaCita = modal.getAttribute("data-cita-cita") || "-";
const tahunLulus = modal.getAttribute("data-tahun-lulus") || "-";
const noKip = modal.getAttribute("data-no-kip") || "-";
const referensi = modal.getAttribute("data-referensi") || "-";
const ketReferensi = modal.getAttribute("data-ket-referensi") || "-";

// Data Ayah
const statusAyah = modal.getAttribute("data-status-ayah") || "Masih Hidup";
const nikAyah = modal.getAttribute("data-nik-ayah") || "-";
const namaAyah = modal.getAttribute("data-nama-ayah") || "-";
const tempatLahirAyah = modal.getAttribute("data-tempat-lahir-ayah") || "-";
const tanggalLahirAyah = modal.getAttribute("data-tanggal-lahir-ayah") || "-";
const pendidikanAyah = modal.getAttribute("data-pendidikan-ayah") || "-";
const pekerjaanAyah = modal.getAttribute("data-pekerjaan-ayah") || "-";
const penghasilanAyah = modal.getAttribute("data-penghasilan-ayah") || "-";
const noHpAyah = modal.getAttribute("data-no-hp-ayah") || "-";

// Data Ibu
const statusIbu = modal.getAttribute("data-status-ibu") || "Masih Hidup";
const nikIbu = modal.getAttribute("data-nik-ibu") || "-";
const namaIbu = modal.getAttribute("data-nama-ibu") || "-";
const tempatLahirIbu = modal.getAttribute("data-tempat-lahir-ibu") || "-";
const tanggalLahirIbu = modal.getAttribute("data-tanggal-lahir-ibu") || "-";
const pendidikanIbu = modal.getAttribute("data-pendidikan-ibu") || "-";
const pekerjaanIbu = modal.getAttribute("data-pekerjaan-ibu") || "-";
const penghasilanIbu = modal.getAttribute("data-penghasilan-ibu") || "-";
const noHpIbu = modal.getAttribute("data-no-hp-ibu") || "-";

// Data Wali
const nikWali = modal.getAttribute("data-nik-wali") || "-";
const namaWali = modal.getAttribute("data-nama-wali") || "-";
const tempatLahirWali = modal.getAttribute("data-tempat-lahir-wali") || "-";
const tanggalLahirWali = modal.getAttribute("data-tanggal-lahir-wali") || "-";
const pendidikanWali = modal.getAttribute("data-pendidikan-wali") || "-";
const pekerjaanWali = modal.getAttribute("data-pekerjaan-wali") || "-";
const penghasilanWali = modal.getAttribute("data-penghasilan-wali") || "-";
const noHpWali = modal.getAttribute("data-no-hp-wali") || "-";

// Data Pendaftaran
const gelombang = modal.getAttribute("data-gelombang") || "-";
const jurusan = modal.getAttribute("data-jurusan") || "-";
const email = modal.getAttribute("data-email") || "-";
const tanggalDaftar = modal.getAttribute("data-tanggal-daftar") || "-";

// Buat konten print dengan data yang diambil dari data attributes
const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Formulir Pendaftaran - ${namaLengkap}</title>
            <style>
                @page {
                    size: 215mm 330mm; /* Kertas F4 / Folio */
                    margin: 1cm 1.5cm;
                }

                body {
                    font-family: "Times New Roman", serif;
                    font-size: 10pt !important;
                    color: #000;
                    padding: 0 !important;
                    margin: 0 !important;
                    text-align: justify;
                    background: #fff;
                }

                .modal-body,
                .modal-content,
                .modal-dialog,
                .print-container {
                    padding: 0 !important;
                    margin: 0 !important;
                    border: none !important;
                    box-shadow: none !important;
                    width: 100% !important;
                    max-width: 100% !important;
                }

                .container {
                    width: 100%;
                    max-width: 17.5cm;
                    margin: 0 auto;
                    padding: 0;
                    box-sizing: border-box;
                }

                /* ==== HEADER ==== */
                .header {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    margin-bottom: 2px;
                }

                .header-logo {
                    width: 52px;
                    height: 52px;
                    object-fit: contain;
                }

                .header-text {
                    flex: 1;
                    text-align: left;
                }

                .header-text h1 {
                    font-size: 13pt;
                    font-weight: bold;
                    margin: 0;
                    line-height: 1.1;
                }

                .header-text h2 {
                    font-size: 8pt;
                    font-weight: normal;
                    margin: 1px 0 0 0;
                    line-height: 1.15;
                }

                .divider {
                    border-top: 1.5px solid #000;
                    border-bottom: 0.5px solid #000;
                    height: 2px;
                    margin: 2px 0 3px 0;
                }

                h3.form-title {
                    text-align: center;
                    font-size: 10.5pt;
                    font-weight: bold;
                    text-decoration: underline;
                    margin: 0;
                    line-height: 1.1;
                }

                .form-number {
                    text-align: center;
                    font-size: 8pt;
                    margin: 1px 0 3px 0;
                }

                /* ==== TABLES & CONTENT ==== */
                .section-title {
                    background: #e9ecef;
                    border: 1px solid #000;
                    text-align: center;
                    font-weight: bold;
                    font-size: 8.5pt;
                    padding: 1.5px;
                    margin-top: 3px;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 2px;
                    table-layout: fixed;
                }

                th, td {
                    border: 1px solid #000;
                    padding: 2.2px 4px;
                    font-size: 7.8pt;
                    vertical-align: top;
                    word-wrap: break-word;
                    overflow-wrap: break-word;
                    white-space: normal;
                    line-height: 1.15;
                }

                th {
                    text-align: center;
                    background: #f2f2f2;
                    font-weight: bold;
                }

                .photo-cell {
                    width: 68px;
                    height: 82px;
                    text-align: center;
                    font-size: 7.5pt;
                    vertical-align: middle;
                    border: 1px solid #000;
                    background: #fafafa;
                }

                .footer {
                    text-align: right;
                    font-size: 7pt;
                    margin-top: 2px;
                    color: #444;
                }

                .spacer {
                    height: 3px;
                }

                .print-info {
                    margin-bottom: 3px;
                }

                @media print {
                    @page {
                        size: a4 portrait;
                        margin: 5mm 8mm 4mm 8mm;
                    }
                    body {
                        font-size: 7.8pt !important;
                    }
                    .container {
                        width: 100% !important;
                        max-width: 100% !important;
                    }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    ${
                        APP_CONFIG.logosekolah
                            ? `<img src="${APP_CONFIG.logosekolah}" alt="Logo Sekolah" class="header-logo" onerror="this.onerror=null;this.src='/sneat/img/logowi.png';">`
                            : ""
                    }
                    <div class="header-text">
                        <h1>${APP_CONFIG.namasekolah}</h1>
                        <h2>${APP_CONFIG.alamatsekolah} – Telp. ${
                            APP_CONFIG.teleponsekolah
                        }</h2>
                    </div>
                </div>

                <div class="divider"></div>

                <h3 class="form-title">FORMULIR PENDAFTARAN</h3>
                <p class="form-number">No. Pendaftaran : <b>${noPendaftaran}</b></p>

                <!-- INFO PENDAFTARAN -->
                <div class="print-info">
                    <table>
                        <tr>
                            <td width="50%"><b>Gelombang:</b> ${gelombang}</td>
                            <td width="50%"><b>Jurusan:</b> ${jurusan}</td>
                        </tr>
                        <tr>
                            <td><b>Tanggal Daftar:</b> ${tanggalDaftar}</td>
                            <td><b>Email:</b> ${email}</td>
                        </tr>
                    </table>
                </div>

                <!-- DATA PRIBADI SISWA -->
                <table>
                    <tr>
                        <th width="100" class="section-title">FOTO SISWA</th>
                        <th colspan="2" class="section-title">DATA PRIBADI SISWA</th>
                    </tr>
                    <tr>
                        <td class="photo-cell" rowspan="4">
                            ${
                                finalFotoUrl
                                    ? `<img src="${finalFotoUrl}" 
                                    style="width:65px;height:85px;object-fit:cover;border:1px solid #000;display:block;margin:0 auto;" onerror="this.onerror=null;this.parentElement.innerHTML='FOTO<br>2x3';">`
                                    : `FOTO<br>2x3`
                            }
                        </td>
                        <td width="35%"><b>NISN</b></td>
                        <td>${nisn}</td>
                    </tr>
                    <tr>
                        <td><b>Nama Lengkap</b></td>
                        <td>${namaLengkap}</td>
                    </tr>
                    <tr>
                        <td><b>Tempat, Tgl Lahir</b></td>
                        <td>${tempatLahir}, ${tanggalLahir}</td>
                    </tr>
                    <tr>
                        <td><b>Jenis Kelamin</b></td>
                        <td>${jenisKelamin}</td>
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
                        <td><b>No NIK</b></td><td>${nik}</td>
                        <td><b>No Kartu Keluarga</b></td><td>${noKk}</td>
                    </tr>
                    <tr>
                        <td><b>Agama</b></td><td>${agama}</td>
                        <td><b>Anak Ke</b></td><td>${anakKe}</td>
                    </tr>
                    <tr>
                        <td><b>No Handphone</b></td><td>${noHp}</td>
                        <td><b>Saudara</b></td><td>${jumlahSaudara}</td>
                    </tr>
                    <tr>
                        <td><b>Asal Sekolah</b></td><td>${asalSekolah}</td>
                        <td><b>Jenjang Sekolah</b></td><td>${jenjangSekolah}</td>
                    </tr>
                    <tr>
                        <td><b>Status Sekolah</b></td><td>${statusSekolah}</td>
                        <td><b>Kecamatan/Desa Asal Sekolah</b></td><td>${kotaSekolah}</td>
                    </tr>
                    <tr>
                        <td><b>Tinggi Badan (cm)</b></td><td>${tinggiBadan}</td>
                        <td><b>Berat Badan (kg)</b></td><td>${beratBadan}</td>
                    </tr>
                    <tr>
                        <td><b>Alamat Siswa</b></td>
                        <td colspan="3">${alamat}</td>
                    </tr>
                    <tr>
                        <td><b>RT / RW</b></td><td>${rt}/${rw}</td>
                        <td><b>Kelurahan</b></td><td>${desa}</td>
                    </tr>
                    <tr>
                        <td><b>Kecamatan</b></td><td>${kecamatan}</td>
                        <td><b>Kota / Kabupaten</b></td><td>${kota}</td>
                    </tr>
                    <tr>
                        <td><b>Provinsi</b></td><td>${provinsi}</td>
                        <td><b>Kode Pos</b></td><td>${kodePos}</td>
                    </tr>
                    <tr>
                        <td><b>Status Dalam Keluarga</b></td><td>${statusDalamKeluarga}</td>
                        <td><b>Tinggal Bersama</b></td><td>${tinggalBersama}</td>
                    </tr>
                    <tr>
                        <td><b>Jarak ke Sekolah (km)</b></td><td>${jarakKesekolah}</td>
                        <td><b>Waktu Tempuh (menit)</b></td><td>${waktuTempuh}</td>
                    </tr>
                    <tr>
                        <td><b>Transportasi</b></td><td>${transportasi}</td>
                        <td><b>Tahun Lulus</b></td><td>${tahunLulus}</td>
                    </tr>
                </table>

                <!-- INFORMASI TAMBAHAN -->
                <table>
                    <tr>
                        <td><b>Ukuran Baju:</b> ${ukuranBaju}</td>
                        <td><b>Hobi:</b> ${hobi}</td>
                        <td><b>Cita-cita:</b> ${citaCita}</td>
                    </tr>
                    <tr>
                        <td><b>No KIP:</b> ${noKip}</td>
                        <td><b>Referensi:</b> ${referensi}</td>
                            ${
                                ketReferensi && ketReferensi !== "-"
                                    ? `
                            <td><b>Ket. Referensi:</b> ${ketReferensi}</td>
                        `
                                    : ""
                            }
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
                        <td><b>Status Keberadaan</b></td>
                        <td>${statusAyah}</td>
                        <td>${statusIbu}</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td><b>NIK</b></td>
                        <td>${nikAyah}</td>
                        <td>${nikIbu}</td>
                        <td>${nikWali}</td>
                    </tr>
                    <tr>
                        <td><b>Nama Lengkap</b></td>
                        <td>${namaAyah}</td>
                        <td>${namaIbu}</td>
                        <td>${namaWali}</td>
                    </tr>
                    <tr>
                        <td><b>Tempat, Tgl Lahir</b></td>
                        <td>${tempatLahirAyah}, ${tanggalLahirAyah}</td>
                        <td>${tempatLahirIbu}, ${tanggalLahirIbu}</td>
                        <td>${tempatLahirWali}, ${tanggalLahirWali}</td>
                    </tr>
                    <tr>
                        <td><b>Pendidikan</b></td>
                        <td>${pendidikanAyah}</td>
                        <td>${pendidikanIbu}</td>
                        <td>${pendidikanWali}</td>
                    </tr>
                    <tr>
                        <td><b>Pekerjaan</b></td>
                        <td>${pekerjaanAyah}</td>
                        <td>${pekerjaanIbu}</td>
                        <td>${pekerjaanWali}</td>
                    </tr>
                    <tr>
                        <td><b>Penghasilan</b></td>
                        <td>${penghasilanAyah}</td>
                        <td>${penghasilanIbu}</td>
                        <td>${penghasilanWali}</td>
                    </tr>
                    <tr>
                        <td><b>No HP</b></td>
                        <td>${noHpAyah}</td>
                        <td>${noHpIbu}</td>
                        <td>${noHpWali}</td>
                    </tr>
                    </tbody>
                </table>                <!-- PERNYATAAN DAN TANDA TANGAN -->
                <div class="section-title">PERNYATAAN DAN PERSETUJUAN</div>
                <table style="margin-top: 4px;">
                    <tr>
                        <td style="text-align: justify; padding: 6px 8px;">
                            <p style="margin: 0 0 6px 0; font-size: 8.5pt; line-height: 1.3;">
                                Dengan mengisi formulir ini, saya menyatakan bahwa semua data yang saya berikan adalah benar dan dapat dipertanggungjawabkan. Saya bersedia mematuhi semua peraturan dan tata tertib yang berlaku di SMK Wisata Indonesia.
                            </p>
                            
                            <!-- TANDA TANGAN -->
                            <table style="width: 100%; border: none; margin: 4px 0 0 0;">
                                <tr style="border: none;">
                                    <td style="width: 50%; border: none; text-align: center; padding: 0;">
                                        <div style="height: 48px;"></div>
                                        <div style="border-top: 1px solid #000; width: 65%; margin: 0 auto; padding-top: 3px; font-size: 8.5pt;">
                                            <strong>.........................</strong><br>
                                            <em>(Orang Tua/Wali)</em>
                                        </div>
                                    </td>
                                    <td style="width: 50%; border: none; text-align: center; padding: 0;">
                                        <div style="height: 48px;"></div>
                                        <div style="border-top: 1px solid #000; width: 65%; margin: 0 auto; padding-top: 3px; font-size: 8.5pt;">
                                            <strong>${namaLengkap}</strong><br>
                                            <em>(Siswa/Peserta Didik)</em>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <div class="footer">
                    Dicetak pada: ${new Date().toLocaleString("id-ID")}
                </div>
            </div>
        </body>
        </html>
    `;

// Hapus iframe print lama jika ada
let oldFrame = document.getElementById("universalPrintFrame");
if (oldFrame) {
    oldFrame.remove();
}

// Buat iframe baru dengan posisi off-screen dan ukuran nyata (bukan visibility: hidden / 0x0)
const printFrame = document.createElement("iframe");
printFrame.id = "universalPrintFrame";
printFrame.name = "universalPrintFrame";
printFrame.style.position = "fixed";
printFrame.style.left = "-9999px";
printFrame.style.top = "-9999px";
printFrame.style.width = "850px";
printFrame.style.height = "1200px";
printFrame.style.border = "none";
document.body.appendChild(printFrame);

const frameDoc =
    printFrame.contentDocument || printFrame.contentWindow.document;
frameDoc.open();
frameDoc.write(printContent);
frameDoc.close();

// Jalankan dialog print setelah delay agar render CSS, font, dan gambar selesai
let printed = false;
const triggerPrint = function () {
    if (printed) return;
    printed = true;
    try {
        printFrame.contentWindow.focus();
        printFrame.contentWindow.print();
    } catch (err) {
        console.warn("Iframe print gagal, fallback ke window.open:", err);
        const win = window.open("", "_blank");
        if (win) {
            win.document.open();
            win.document.write(printContent);
            win.document.close();
            setTimeout(() => {
                win.focus();
                win.print();
            }, 350);
        }
    }
};

// Tunggu gambar (kop logo & foto siswa) selesai ter-load di dalam frame sebelum print dibuka
const imgs = frameDoc.querySelectorAll("img");
if (imgs.length === 0) {
    setTimeout(triggerPrint, 250);
} else {
    let loaded = 0;
    const total = imgs.length;
    const onImgDone = function () {
        loaded++;
        if (loaded >= total) {
            setTimeout(triggerPrint, 150);
        }
    };

    imgs.forEach((img) => {
        if (img.complete) {
            onImgDone();
        } else {
            img.addEventListener("load", onImgDone);
            img.addEventListener("error", onImgDone);
        }
    });

    // Pengaman jika koneksi atau gambar lambat
    setTimeout(triggerPrint, 700);
}
