@extends('admin.layouts.app')

@section('title', 'Manajemen Backup & Restore - Admin PPDB')

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
    <div class="card border-0 mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="card-body py-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-white bg-opacity-25" style="width:44px;height:44px;">
                        <i class="bx bx-link text-white fs-4"></i>
                    </div>
                    <div>
                        <p class="mb-0 text-white fw-bold">Foto/Berkas Tidak Muncul Setelah Restore?</p>
                        <small class="text-white opacity-75">Klik tombol ini untuk memperbaiki storage link secara otomatis (symlink atau copy file)</small>
                    </div>
                </div>
                <form action="{{ route('backup-restore.fix-storage') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-light fw-bold px-4" onclick="return confirm('Jalankan perbaikan storage link? Proses ini aman dan tidak menghapus data.')">
                        <i class="bx bx-wrench me-2"></i> Perbaiki Storage Link Sekarang
                    </button>
                </form>
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
                                <button type="button" class="btn btn-warning btn-lg px-4 text-dark fw-bold w-100" onclick="confirmRestoreUpload()">
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
                                            onclick="confirmDeleteBackup('{{ $b['filename'] }}')"
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

<!-- Form Delete Backup Hidden -->
<form id="formDeleteBackup" action="" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
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

    // Confirm restore upload
    function confirmRestoreUpload() {
        var fileInput = document.querySelector('input[name="backup_file"]');
        if (!fileInput.files || fileInput.files.length === 0) {
            alert('Silakan pilih berkas backup (.sql atau .zip) terlebih dahulu!');
            return;
        }

        if (confirm('PERINGATAN: Mengunggah dan melakukan restore akan memperbarui data sistem/database Anda saat ini!\n\nApakah Anda yakin ingin melanjutkan?')) {
            document.getElementById('formUploadRestore').submit();
        }
    }

    // Confirm delete backup
    function confirmDeleteBackup(filename) {
        if (confirm('Apakah Anda yakin ingin menghapus berkas backup "' + filename + '"?')) {
            var form = document.getElementById('formDeleteBackup');
            form.action = "{{ url('w1s4t4/backup-restore') }}/" + encodeURIComponent(filename);
            form.submit();
        }
    }
</script>
@endpush
