/**
 * UNIVERSAL PRINT FORMULIR
 * Dipakai oleh ADMIN & SISWA
 * id = id modal, contoh: doPrintFormulir(5, "formulirModal")
 * atau doPrintFormulir("formulirModal5")
 */

window.doPrintFormulir = function (id, modalPrefix = "formulirModal") {
    const modalId = typeof id === "number" ? `${modalPrefix}${id}` : id;
    const modal = document.getElementById(modalId);

    if (!modal) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Modal tidak ditemukan",
            confirmButtonColor: "#dc3545",
        });
        return;
    }

    // Ambil data dari data attributes
    const namaLengkap = modal.getAttribute("data-nama");
    const noPendaftaran = modal.getAttribute("data-no-pendaftaran");
    const nisn = modal.getAttribute("data-nisn");
    const nik = modal.getAttribute("data-nik");
    const noKk = modal.getAttribute("data-no-kk");
    const tempatLahir = modal.getAttribute("data-tempat-lahir");
    const tanggalLahir = modal.getAttribute("data-tanggal-lahir");
    const jenisKelamin = modal.getAttribute("data-jenis-kelamin");
    const agama = modal.getAttribute("data-agama");
    const noHp = modal.getAttribute("data-no-hp");
    const asalSekolah = modal.getAttribute("data-asal-sekolah");
    const alamat = modal.getAttribute("data-alamat");
    const rt = modal.getAttribute("data-rt");
    const rw = modal.getAttribute("data-rw");
    const desa = modal.getAttribute("data-desa");
    const kecamatan = modal.getAttribute("data-kecamatan");
    const kota = modal.getAttribute("data-kota");
    const provinsi = modal.getAttribute("data-provinsi");
    const kodePos = modal.getAttribute("data-kode-pos");
    const tinggiBadan = modal.getAttribute("data-tinggi-badan");
    const beratBadan = modal.getAttribute("data-berat-badan");
    const anakKe = modal.getAttribute("data-anak-ke");
    const jumlahSaudara = modal.getAttribute("data-jumlah-saudara");
    const statusDalamKeluarga = modal.getAttribute(
        "data-status-dalam-keluarga"
    );
    const tinggalBersama = modal.getAttribute("data-tinggal-bersama");
    const jarakKesekolah = modal.getAttribute("data-jarak-kesekolah");
    const waktuTempuh = modal.getAttribute("data-waktu-tempuh");
    const transportasi = modal.getAttribute("data-transportasi");
    const ukuranBaju = modal.getAttribute("data-ukuran-baju");
    const hobi = modal.getAttribute("data-hobi");
    const citaCita = modal.getAttribute("data-cita-cita");
    const tahunLulus = modal.getAttribute("data-tahun-lulus");
    const noKip = modal.getAttribute("data-no-kip");
    const referensi = modal.getAttribute("data-referensi");
    const ketReferensi = modal.getAttribute("data-ket-referensi");

    // Data Ayah
    const nikAyah = modal.getAttribute("data-nik-ayah");
    const namaAyah = modal.getAttribute("data-nama-ayah");
    const tempatLahirAyah = modal.getAttribute("data-tempat-lahir-ayah");
    const tanggalLahirAyah = modal.getAttribute("data-tanggal-lahir-ayah");
    const pendidikanAyah = modal.getAttribute("data-pendidikan-ayah");
    const pekerjaanAyah = modal.getAttribute("data-pekerjaan-ayah");
    const penghasilanAyah = modal.getAttribute("data-penghasilan-ayah");
    const noHpAyah = modal.getAttribute("data-no-hp-ayah");

    // Data Ibu
    const nikIbu = modal.getAttribute("data-nik-ibu");
    const namaIbu = modal.getAttribute("data-nama-ibu");
    const tempatLahirIbu = modal.getAttribute("data-tempat-lahir-ibu");
    const tanggalLahirIbu = modal.getAttribute("data-tanggal-lahir-ibu");
    const pendidikanIbu = modal.getAttribute("data-pendidikan-ibu");
    const pekerjaanIbu = modal.getAttribute("data-pekerjaan-ibu");
    const penghasilanIbu = modal.getAttribute("data-penghasilan-ibu");
    const noHpIbu = modal.getAttribute("data-no-hp-ibu");

    // Data Wali
    const nikWali = modal.getAttribute("data-nik-wali");
    const namaWali = modal.getAttribute("data-nama-wali");
    const tempatLahirWali = modal.getAttribute("data-tempat-lahir-wali");
    const tanggalLahirWali = modal.getAttribute("data-tanggal-lahir-wali");
    const pendidikanWali = modal.getAttribute("data-pendidikan-wali");
    const pekerjaanWali = modal.getAttribute("data-pekerjaan-wali");
    const penghasilanWali = modal.getAttribute("data-penghasilan-wali");
    const noHpWali = modal.getAttribute("data-no-hp-wali");

    // Data Pendaftaran
    const gelombang = modal.getAttribute("data-gelombang");
    const jurusan = modal.getAttribute("data-jurusan");
    const email = modal.getAttribute("data-email");
    const tanggalDaftar = modal.getAttribute("data-tanggal-daftar");

    // Buat iframe untuk print
    const printFrame = document.createElement("iframe");
    printFrame.style.position = "fixed";
    printFrame.style.right = "0";
    printFrame.style.bottom = "0";
    printFrame.style.width = "0";
    printFrame.style.height = "0";
    printFrame.style.border = "0";
    printFrame.style.visibility = "hidden";
    document.body.appendChild(printFrame);

    // Buat konten print dengan data yang diambil dari data attributes
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Formulir Pendaftaran - ${namaLengkap}</title>
            <style>
                @page {
                    size: A4;
                    margin: 1.1cm 1.5cm;
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

                * {
                    page-break-inside: avoid !important;
                }

                .container {
                    width: 100%;
                    max-width: 17cm; /* Sesuaikan dengan lebar A4 */
                    margin: 0 auto;
                    padding: 0;
                    box-sizing: border-box;
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
                    margin-bottom: -6px;
                }

                .form-number {
                    text-align: center;
                    font-size: 9.5pt;
                    margin-bottom: 6px;
                }

                /* ==== TABLES & CONTENT ==== */
                .section-title {
                    background: #ebcaecff;
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
                    padding: 3px 4px;
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
                    /* width: 113px; */ 
                    /* height: 151px; */ 
                    width: 75px; /* Kurangi ukuran */
                    height: 113px; /* Kurangi ukuran */
                    text-align: center;
                    font-size: 8.5pt;
                    vertical-align: middle;
                    border: 1px solid #000;
                    background: #f9f9f9;
                }

                .footer {
                    text-align: right;
                    font-size: 9pt;
                    margin-top: 4px;
                }

                .spacer {
                    height: 6px;
                }

                .print-info {
                    margin-bottom: 10px;
                }

                @media print {
                    body {
                        font-size: 10.2pt;
                    }
                    .container {
                        width: 17.2cm;
                    }
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
                        <td class="photo-cell" rowspan="4">FOTO<br>2x3</td>
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
                        <td><b>Tinggi Badan (cm)</b></td><td>${tinggiBadan}</td>
                    </tr>
                    <tr>
                        <td><b>Alamat Siswa</b></td>
                        <td colspan="3">${alamat}</td>
                    </tr>
                    <tr>
                        <td><b>RT / RW</b></td><td>${rt}/${rw}</td>
                        <td><b>Berat Badan (kg)</b></td><td>${beratBadan}</td>
                    </tr>
                    <tr>
                        <td><b>Kelurahan</b></td><td>${desa}</td>
                        <td><b>Status Dalam Keluarga</b></td><td>${statusDalamKeluarga}</td>
                    </tr>
                    <tr>
                        <td><b>Kecamatan</b></td><td>${kecamatan}</td>
                        <td><b>Tinggal Bersama</b></td><td>${tinggalBersama}</td>
                    </tr>
                    <tr>
                        <td><b>Kota / Kabupaten</b></td><td>${kota}</td>
                        <td><b>Jarak ke Sekolah (km)</b></td><td>${jarakKesekolah}</td>
                    </tr>
                    <tr>
                        <td><b>Provinsi</b></td><td>${provinsi}</td>
                        <td><b>Waktu Tempuh (menit)</b></td><td>${waktuTempuh}</td>
                    </tr>
                    <tr>
                        <td><b>Kode Pos</b></td><td>${kodePos}</td>
                        <td><b>Transportasi</b></td><td>${transportasi}</td>
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
                </table>

                <!-- PERNYATAAN DAN TANDA TANGAN -->
                <div class="section-title mt-2">PERNYATAAN DAN PERSETUJUAN</div>
                <table class="no-break">
                    <tr>
                        <td style="text-align: justify; padding: 8px;">
                            <p style="margin: 0 0 5px 0;">
                                Dengan mengisi formulir ini, saya menyatakan bahwa semua data yang saya berikan adalah benar dan dapat dipertanggungjawabkan. Saya bersedia mematuhi semua peraturan dan tata tertib yang berlaku di SMK Wisata Indonesia.
                            </p>
                            
                            <!-- TANDA TANGAN -->
                            <div style="margin-top: 20px; position: relative;">
                                <!-- ORANG TUA -->
                                <div style="position: absolute; left: 0; width: 45%; text-align: center;">
                                    <div style="margin-top: 40px;">
                                        <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto; padding-top: 5px;">
                                            <strong>.........................</strong>
                                            <br>
                                            <em>(Orang Tua/Wali)</em>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- SISWA -->
                                <div style="position: absolute; right: 0; width: 45%; text-align: center;">
                                    <div style="margin-top: 40px;">
                                        <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto; padding-top: 5px;">
                                            <strong>${namaLengkap}</strong>
                                            <br>
                                            <em>(Siswa/Peserta Didik)</em>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- CLEARFIX -->
                                <div style="clear: both; height: 75px;"></div>
                            </div>
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

    // Tulis konten ke iframe
    printFrame.contentDocument.write(printContent);
    printFrame.contentDocument.close();

    // Print ketika iframe siap
    printFrame.onload = function () {
        setTimeout(() => {
            printFrame.contentWindow.focus();
            printFrame.contentWindow.print();

            // Hapus iframe setelah print
            setTimeout(() => {
                if (document.body.contains(printFrame)) {
                    document.body.removeChild(printFrame);
                }
            }, 1000);
        }, 500);
    };
};
