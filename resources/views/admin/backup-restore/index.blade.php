@extends('admin.layouts.app')

@section('title', 'Manajemen Backup & Restore - Admin PPDB')

<style>
    /* Styling Modal persis seperti Sneat Template (Tambah Pendaftar Manual) */
    .modal-content {
        border-radius: 0.5rem !important;
        border: none !important;
        box-shadow: 0 0.25rem 1.25rem rgba(161, 172, 184, 0.4) !important;
        background-color: #f8f9fa !important;
        overflow: hidden !important;
    }
    .modal-content form {
        margin: 0 !important;
        padding: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        margin-bottom: 0 !important;
    }
    .modal-body {
        background-color: #ffffff !important;
    }
    .modal-header {
        position: relative !important;
        border-top-left-radius: 0.5rem !important;
        border-top-right-radius: 0.5rem !important;
        padding: 1.25rem 1.5rem !important;
    }
    .modal-header .btn-close {
        position: absolute !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        right: 1rem !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 2rem !important;
        height: 2rem !important;
        background-color: #ffffff !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23566a7f'%3E%3Cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z'/%3e%3C/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: center !important;
        background-size: 0.75em !important;
        border-radius: 0.375rem !important;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.15) !important;
        border: none !important;
        opacity: 1 !important;
        filter: none !important;
        z-index: 1055 !important;
    }
    .modal-header .btn-close::before,
    .modal-header .btn-close::after {
        display: none !important;
        content: none !important;
    }
    .modal-header .btn-close:hover {
        background-color: #f8f9fa !important;
        opacity: 1 !important;
    }
    .modal-footer {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        padding: 1rem 1.5rem !important;
        gap: 0.5rem !important;
        border-top: 1px solid #e9ecef !important;
        background-color: #f8f9fa !important;
        margin-top: auto !important;
        margin-bottom: 0 !important;
        border-bottom-left-radius: 0.5rem !important;
        border-bottom-right-radius: 0.5rem !important;
    }
    .modal-footer > * {
        margin: 0 !important;
    }
</style>

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold py-1 mb-0">
                <span class="text-muted fw-light">Pengaturan /</span> Backup & Restore Sistem
            </h4>
            <p class="text-muted mb-0 small">Kelola cadangan database, berkas media pendaftaran, serta pemulihan (restore) data sistem secara profesional.</p>
        </div>
    </div>

    <!-- Alert Messaging -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bx bx-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bx bx-error-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Banner Perbaiki Storage Link --}}
    <div class="card border-0 mb-4 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="card-body py-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-white shadow-sm" style="width:44px;height:44px; flex-shrink: 0;">
                        <i class="bx bx-link-external fs-4" style="color: #667eea;"></i>
                    </div>
                    <div>
                        <p class="mb-0 text-white fw-bold">Foto/Berkas Tidak Muncul Setelah Restore?</p>
                        <small class="text-white opacity-75">Klik tombol ini untuk memperbarui storage link secara otomatis (symlink atau copy file)</small>
                    </div>
                </div>
                <button type="button" class="btn btn-light fw-bold px-4 text-dark" data-bs-toggle="modal" data-bs-target="#modalFixStorageLink">
                    <i class="bx bx-wrench me-2"></i> Perbaiki Storage Link Sekarang
                </button>
            </div>
        </div>
    </div>

    <!-- System Status & Create Backup Card -->
    <div class="row mb-4">
        <!-- Informasi Status Sistem -->
        <div class="col-lg-5 mb-3 mb-lg-0">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between py-3">
                    <h6 class="mb-0 text-white"><i class="bx bx-server me-2"></i> Informasi Sistem & Database</h6>
                    <span class="badge bg-white text-primary fw-bold">ONLINE</span>
                </div>
                <div class="card-body mt-3">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted"><i class="bx bx-data me-2"></i>Nama Database</span>
                            <strong class="text-dark">{{ $sysInfo['db_name'] }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted"><i class="bx bx-table me-2"></i>Total Tabel</span>
                            <strong class="text-dark">{{ $sysInfo['tables_count'] }} Tabel</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted"><i class="bx bx-folder me-2"></i>Ukuran Berkas Storage</span>
                            <strong class="text-dark">{{ $sysInfo['storage_size'] }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted"><i class="bx bx-code-alt me-2"></i>Versi PHP</span>
                            <span class="badge bg-label-info">{{ $sysInfo['php_version'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted"><i class="bx bx-archive me-2"></i>Modul ZipArchive</span>
                            @if($sysInfo['zip_enabled'])
                                <span class="badge bg-success"><i class="bx bx-check me-1"></i>Aktif</span>
                            @else
                                <span class="badge bg-danger"><i class="bx bx-x me-1"></i>Tidak Aktif</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Buat Backup Baru -->
        <div class="col-lg-7">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between py-3">
                    <h6 class="mb-0 text-white"><i class="bx bx-plus-circle me-2"></i> Buat Cadangan (Backup Baru)</h6>
                </div>
                <div class="card-body mt-3">
                    <form action="{{ route('backup-restore.create') }}" method="POST" id="formCreateBackup">
                        @csrf
                        <label class="form-label fw-bold mb-2">Pilih Jenis Backup Yang Diinginkan:</label>

                        <div class="row g-3 mb-4">
                            <!-- Database Only -->
                            <div class="col-md-4">
                                <div class="form-check custom-option custom-option-icon p-3 border rounded h-100 text-center">
                                    <label class="form-check-label w-100 cursor-pointer" for="type_db">
                                        <input class="form-check-input d-none" type="radio" name="backup_type" id="type_db" value="database" checked>
                                        <i class="bx bx-data text-primary fs-1 mb-2"></i>
                                        <span class="d-block fw-bold text-dark mb-1">Database Only</span>
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">Export tabel & data database (.sql)</small>
                                    </label>
                                </div>
                            </div>

                            <!-- Storage Files Only -->
                            <div class="col-md-4">
                                <div class="form-check custom-option custom-option-icon p-3 border rounded h-100 text-center">
                                    <label class="form-check-label w-100 cursor-pointer" for="type_files">
                                        <input class="form-check-input d-none" type="radio" name="backup_type" id="type_files" value="files">
                                        <i class="bx bx-folder-open text-info fs-1 mb-2"></i>
                                        <span class="d-block fw-bold text-dark mb-1">Media Storage</span>
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">Archive berkas upload foto & dokumen (.zip)</small>
                                    </label>
                                </div>
                            </div>

                            <!-- Full System -->
                            <div class="col-md-4">
                                <div class="form-check custom-option custom-option-icon p-3 border rounded h-100 text-center">
                                    <label class="form-check-label w-100 cursor-pointer" for="type_full">
                                        <input class="form-check-input d-none" type="radio" name="backup_type" id="type_full" value="full">
                                        <i class="bx bx-archive text-success fs-1 mb-2"></i>
                                        <span class="d-block fw-bold text-dark mb-1">Paket Lengkap</span>
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">Database SQL + Berkas Storage (.zip)</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-2">
                            <span class="text-muted small"><i class="bx bx-info-circle me-1"></i>File backup tersimpan di server & otomatis ter-download ke komputer Anda.</span>
                            <button type="submit" class="btn btn-primary px-4 fw-bold" id="btnSubmitBackup">
                                <i class="bx bx-cloud-download me-2"></i> Buat & Download Backup
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload & Restore Directly -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark d-flex align-items-center justify-content-between py-3">
                    <h6 class="mb-0 text-dark fw-bold"><i class="bx bx-upload me-2"></i> Unggah & Restore File Backup Luar (.sql / .zip)</h6>
                </div>
                <div class="card-body mt-3">
                    <form action="{{ route('backup-restore.restore') }}" method="POST" enctype="multipart/form-data" id="formUploadRestore">
                        @csrf
                        <div class="row align-items-center">
                            <div class="col-md-8 mb-3 mb-md-0">
                                <input type="file" name="backup_file" class="form-control form-control-lg" accept=".sql,.zip" required>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <button type="button" class="btn btn-warning btn-lg px-4 text-dark fw-bold w-100" onclick="triggerRestoreUploadModal()">
                                    <i class="bx bx-refresh me-2"></i> Unggah & Jalankan Restore
                                </button>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">
                            Format: <strong>.sql</strong> atau <strong>.zip</strong> — Maksimal: <strong>500MB</strong>.
                            <span class="text-success fw-semibold ms-2"><i class="bx bx-info-circle"></i> File ZIP Paket Lengkap akan me-restore database <u>dan</u> semua foto/berkas media secara otomatis.</span>
                        </small>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup History Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
            <h5 class="mb-0 fw-bold"><i class="bx bx-history me-2 text-primary"></i> Riwayat File Backup Terimpan</h5>
            <span class="badge bg-label-primary px-3 py-2 fs-6">{{ count($backups) }} File Tersedia</span>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No.</th>
                        <th>Nama File Backup</th>
                        <th>Tipe Backup</th>
                        <th>Ukuran File</th>
                        <th>Tanggal Dibuat</th>
                        <th class="text-center" style="width: 220px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backups as $index => $b)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($b['extension'] === 'sql')
                                        <i class="bx bx-file-blank text-primary fs-3 me-2"></i>
                                    @else
                                        <i class="bx bx-file text-warning fs-3 me-2"></i>
                                    @endif
                                    <div>
                                        <strong class="text-dark d-block">{{ $b['filename'] }}</strong>
                                        <small class="text-muted">{{ $b['extension'] }} file</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $b['badge'] }} px-3 py-2 fs-7">{{ $b['type'] }}</span>
                            </td>
                            <td><strong class="text-secondary">{{ $b['size'] }}</strong></td>
                            <td><small class="text-muted"><i class="bx bx-time-five me-1"></i>{{ $b['created_at'] }}</small></td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <!-- Download Button -->
                                    <a href="{{ route('backup-restore.download', $b['filename']) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Download File">
                                        <i class="bx bx-download me-1"></i> Download
                                    </a>

                                    <!-- Restore Button -->
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-warning" 
                                            onclick="confirmRestoreExisting('{{ $b['filename'] }}')"
                                            title="Restore Data">
                                        <i class="bx bx-refresh me-1"></i> Restore
                                    </button>

                                    <!-- Delete Button -->
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            onclick="triggerDeleteBackupModal('{{ $b['filename'] }}')"
                                            title="Hapus Backup">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bx bx-hdd display-4 d-block mb-3 opacity-50"></i>
                                <h5>Belum ada riwayat file backup</h5>
                                <p class="mb-0 small">Klik tombol <strong>Mulai Proses Backup</strong> di atas untuk membuat cadangan pertama Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Danger Zone Card: Reset Data System & Inisialisasi Tahun Ajaran Baru -->
<div class="card border-danger shadow-sm mb-4">
    <div class="card-header bg-danger text-white d-flex align-items-center justify-content-between py-3">
        <h6 class="mb-0 text-white"><i class="bx bx-trash me-2"></i> Reset Data Keseluruhan / Inisialisasi Tahun Ajaran Baru</h6>
        <span class="badge bg-white text-danger fw-bold">DANGER ZONE</span>
    </div>
    <div class="card-body py-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h6 class="fw-bold text-danger mb-2">Ingin Menggunakan Sistem Dalam Keadaan Fresh Untuk Tahun Ajaran Baru?</h6>
                <p class="text-muted small mb-2">
                    Fitur ini digunakan ketika telah berganti tahun ajaran dan sistem PPDB akan digunakan dari awal kembali. Seluruh data pendaftaran, akun siswa, bukti pembayaran, dan log aktivitas akan dibersihkan.
                </p>
                <div class="d-flex flex-wrap gap-3 text-muted small">
                    <span class="text-danger"><i class="bx bx-x-circle me-1"></i>Dihapus: Data Pendaftar, Akun Siswa, Pembayaran, Log Sistem & Berkas Upload Siswa</span>
                    <span class="text-success"><i class="bx bx-check-circle me-1"></i>Dipertahankan: Akun Admin/Superadmin, Logo & Pengaturan Sekolah, Master Data</span>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <button type="button" class="btn btn-outline-danger fw-bold px-4" data-bs-toggle="modal" data-bs-target="#modalResetDataSystem">
                    <i class="bx bx-error-alt me-2"></i> Reset Data Sistem Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reset Data System -->
<div class="modal fade" id="modalResetDataSystem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <form action="{{ route('backup-restore.reset') }}" method="POST" id="formResetDataSystem">
                @csrf
                <div class="modal-header bg-danger text-white py-3">
                    <h5 class="modal-title fw-bold text-white"><i class="bx bx-shield-quarter me-2"></i> Konfirmasi Reset Data Sistem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="alert alert-danger d-flex align-items-start mb-3" role="alert">
                        <i class="bx bx-error-circle fs-3 me-2 mt-1"></i>
                        <div>
                            <strong>PERINGATAN KERAS! Tindakan Tidak Dapat Dibatalkan!</strong>
                            <p class="mb-0 small">Seluruh data pendaftar, akun pendaftaran siswa, transaksi/bukti pembayaran, serta berkas media pendaftar akan <strong>DIHAPUS PERMANEN</strong>.</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cakupan Pembersihan Data:</label>
                        <select name="reset_scope" class="form-select">
                            <option value="pendaftaran_only" selected>Mode Pendaftaran & Transaksi (Pertahankan Data Master & Pengaturan)</option>
                            <option value="full_reset">Mode Reset Total (Hapus Pendaftaran + Master Jurusan, Kuota, Tahun Ajaran, SMP, Biaya & Statistik)</option>
                        </select>
                        <small class="text-muted">Akun Superadmin/Admin dan Pengaturan Aplikasi utama (Logo/Branding) tetap utuh pada kedua mode agar sistem tidak terkunci.</small>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="auto_backup" id="autoBackupSwitch" checked value="1">
                        <label class="form-check-label fw-bold text-dark" for="autoBackupSwitch">
                            Buat Cadangan Database Otomatis Sebelum Dihapus (Sangat Direkomendasikan)
                        </label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Password Login Superadmin <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <input type="password" name="admin_password" id="inputResetAdminPassword" class="form-control" placeholder="Masukkan password login Anda" required autocomplete="current-password">
                            <span class="input-group-text cursor-pointer" onclick="toggleResetAdminPassword()" style="cursor: pointer;">
                                <i class="bx bx-hide" id="iconResetAdminPassword"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">
                            Ketik Teks Konfirmasi: <code class="user-select-all text-danger fw-bold">RESET DATA SYSTEM</code> <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="confirmation_text" class="form-control" placeholder="RESET DATA SYSTEM" autocomplete="off" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger fw-bold px-4">
                        <i class="bx bx-trash me-1"></i> Ya, Hapus & Reset Data Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Fix Storage Link -->
<div class="modal fade" id="modalFixStorageLink" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('backup-restore.fix-storage') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold text-white"><i class="bx bx-wrench me-2"></i> Konfirmasi Perbaiki Storage Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                        <i class="bx bx-info-circle fs-3 me-2"></i>
                        <div>
                            <strong>Proses Aman!</strong>
                            <p class="mb-0 small">Proses ini akan memperbarui tautan penyimpanan berkas foto/media pendaftar (symlink/copy file). Tidak ada data yang dihapus.</p>
                        </div>
                    </div>
                    <p class="mb-0 text-dark fw-semibold">Jalankan perbaikan storage link sekarang?</p>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">
                        <i class="bx bx-check me-1"></i> Ya, Jalankan Perbaikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirm Restore Existing -->
<div class="modal fade" id="modalRestoreExisting" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('backup-restore.restore') }}" method="POST" id="formRestoreExisting">
                @csrf
                <input type="hidden" name="filename" id="restoreFilenameInput">
                <div class="modal-header bg-warning text-dark py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="bx bx-error me-2"></i> Konfirmasi Restore Data Sistem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="bx bx-error-circle fs-3 me-2"></i>
                        <div>
                            <strong>Peringatan Penting!</strong>
                            <p class="mb-0 small">Proses restore akan memperbarui dan menimpa data database/berkas sistem saat ini sesuai isi file backup yang dipilih.</p>
                        </div>
                    </div>
                    <p class="mb-1 text-muted">File backup yang akan dijalankan:</p>
                    <div class="p-3 bg-light rounded border font-monospace text-break fw-bold text-primary mb-3" id="restoreFilenameDisplay">
                        -
                    </div>
                    <p class="mb-0 small text-danger fw-bold"><i class="bx bx-info-circle me-1"></i>Apakah Anda yakin ingin melanjutkan proses restore ini?</p>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold px-4">
                        <i class="bx bx-check me-1"></i> Ya, Jalankan Restore Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirm Upload & Restore Luar -->
<div class="modal fade" id="modalConfirmUploadRestore" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="bx bx-error me-2"></i> Konfirmasi Restore Berkas Luar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div class="alert alert-warning d-flex align-items-center mb-3" role="alert">
                    <i class="bx bx-error-circle fs-3 me-2"></i>
                    <div>
                        <strong>Peringatan Penting!</strong>
                        <p class="mb-0 small">Mengunggah dan melakukan restore akan memperbarui dan menimpa data sistem/database Anda saat ini sesuai isi berkas backup yang dipilih.</p>
                    </div>
                </div>
                <p class="mb-1 text-muted">Berkas backup yang dipilih:</p>
                <div class="p-3 bg-light rounded border font-monospace text-break fw-bold text-dark mb-3" id="uploadFilenameDisplay">
                    -
                </div>
                <p class="mb-0 small text-danger fw-bold"><i class="bx bx-info-circle me-1"></i>Apakah Anda yakin ingin melanjutkan proses restore ini?</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning text-dark fw-bold px-4" onclick="submitUploadRestoreForm()">
                    <i class="bx bx-check me-1"></i> Ya, Unggah & Restore Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete Backup File -->
<div class="modal fade" id="modalDeleteBackup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formDeleteBackup" action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white py-3">
                    <h5 class="modal-title fw-bold text-white"><i class="bx bx-trash me-2"></i> Konfirmasi Hapus Berkas Backup</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <p class="mb-2 text-dark">Apakah Anda yakin ingin menghapus berkas backup berikut?</p>
                    <div class="p-3 bg-light rounded border font-monospace text-break fw-bold text-danger mb-3" id="deleteFilenameDisplay">
                        -
                    </div>
                    <small class="text-muted"><i class="bx bx-info-circle me-1"></i>Berkas cadangan yang dihapus tidak dapat dikembalikan lagi.</small>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger fw-bold px-4">
                        <i class="bx bx-trash me-1"></i> Ya, Hapus Berkas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Validation Error -->
<div class="modal fade" id="modalValidationError" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title fw-bold text-white"><i class="bx bx-error-circle me-2"></i> Peringatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4 text-center">
                <i class="bx bx-error-circle display-3 text-danger mb-3"></i>
                <h5 class="fw-bold text-dark" id="validationErrorMessage">Silakan pilih berkas terlebih dahulu!</h5>
            </div>
            <div class="modal-footer bg-light justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Style active radio selection cards
    document.querySelectorAll('input[name="backup_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.custom-option').forEach(card => {
                card.classList.remove('border-primary', 'bg-light');
            });
            if(this.checked) {
                this.closest('.custom-option').classList.add('border-primary', 'bg-light');
            }
        });
    });

    // Initial check
    document.querySelector('input[name="backup_type"]:checked')?.closest('.custom-option')?.classList.add('border-primary', 'bg-light');

    // Confirm restore from history
    function confirmRestoreExisting(filename) {
        document.getElementById('restoreFilenameInput').value = filename;
        document.getElementById('restoreFilenameDisplay').innerText = filename;
        var modal = new bootstrap.Modal(document.getElementById('modalRestoreExisting'));
        modal.show();
    }

    // Trigger upload & restore modal
    function triggerRestoreUploadModal() {
        var fileInput = document.querySelector('input[name="backup_file"]');
        if (!fileInput.files || fileInput.files.length === 0) {
            document.getElementById('validationErrorMessage').innerText = 'Silakan pilih berkas backup (.sql atau .zip) terlebih dahulu!';
            var modalErr = new bootstrap.Modal(document.getElementById('modalValidationError'));
            modalErr.show();
            return;
        }

        document.getElementById('uploadFilenameDisplay').innerText = fileInput.files[0].name;
        var modal = new bootstrap.Modal(document.getElementById('modalConfirmUploadRestore'));
        modal.show();
    }

    function submitUploadRestoreForm() {
        document.getElementById('formUploadRestore').submit();
    }

    // Trigger delete backup modal
    function triggerDeleteBackupModal(filename) {
        var form = document.getElementById('formDeleteBackup');
        form.action = "{{ url('panel/backup-restore') }}/" + encodeURIComponent(filename);
        document.getElementById('deleteFilenameDisplay').innerText = filename;
        var modal = new bootstrap.Modal(document.getElementById('modalDeleteBackup'));
        modal.show();
    }

    // Toggle hide/unhide password superadmin
    function toggleResetAdminPassword() {
        var input = document.getElementById('inputResetAdminPassword');
        var icon = document.getElementById('iconResetAdminPassword');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bx-hide');
            icon.classList.add('bx-show');
        } else {
            input.type = 'password';
            icon.classList.remove('bx-show');
            icon.classList.add('bx-hide');
        }
    }
</script>
@endpush
