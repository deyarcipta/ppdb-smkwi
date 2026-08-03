@extends('admin.layouts.app')

@section('title', 'WhatsApp Gateway & Bot')

@section('content')

{{-- STATUS PENGATURAN WHATSAPP SISTEM --}}
@if(isset($pengaturan) && !$pengaturan->wa_status && !$pengaturan->enable_whatsapp)
<div class="alert alert-warning d-flex align-items-center justify-content-between mb-4 shadow-sm" role="alert">
    <div>
        <i class="bx bx-error-circle me-2 fs-5"></i>
        <strong>Fitur Notifikasi WhatsApp Sistem saat ini NONAKTIF.</strong> Pengiriman pesan otomatis dari sistem dipause.
    </div>
    <a href="{{ route('pengaturan-aplikasi.index') }}" class="btn btn-sm btn-warning fw-bold text-nowrap">
        <i class="bx bx-cog me-1"></i> Buka Pengaturan Aplikasi
    </a>
</div>
@endif

<!-- KONFIGURASI SERVER OPEN-WA API GATEWAY -->
<div class="row">
    <div class="col-12">
        <div class="card mb-4 border border-success">
            <div class="card-header bg-success text-white d-flex align-items-center justify-content-between">
                <h6 class="mb-0 text-white"><i class="bx bxl-whatsapp me-1"></i> Konfigurasi Server Open-WA API Gateway</h6>
                <span class="badge bg-white text-success fw-bold"><i class="bx bx-check-circle me-1"></i> Open-WA Gateway</span>
            </div>
            <div class="card-body mt-3">
                <form action="{{ route('whatsapp.update-settings') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">OpenWA API URL</label>
                            <input type="text" name="wa_api_url" class="form-control" 
                                   value="{{ $pengaturan->wa_api_url ?? env('OPEN_WA_API_URL', 'http://localhost:2785/api') }}" 
                                   placeholder="http://localhost:2785/api" required>
                            <small class="text-muted d-block mt-1">URL Gateway OpenWA REST API (Default: <code>http://localhost:2785/api</code>).</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">OpenWA API Key</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="wa_api_key_input" name="wa_api_key" 
                                       value="{{ $pengaturan->wa_api_key ?? env('OPEN_WA_API_KEY') }}"
                                       placeholder="Masukkan API Key (Opsional)">
                                <button class="btn btn-outline-secondary" type="button" id="toggleApiKey">
                                    <i class="bx bx-show" id="toggleIcon"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">API Key autentikasi server OpenWA (Bearer Token / X-API-Key).</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-save me-1"></i> Simpan Konfigurasi Gateway
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- SESI & NOMOR WHATSAPP GATEWAY (MULTI-SESSION) -->
<div class="row">
    <div class="col-12">
        <div class="card mb-4 border border-info">
            <div class="card-header bg-info text-white d-flex align-items-center justify-content-between">
                <h6 class="mb-0 text-white">
                    <i class="bx bxl-whatsapp me-1"></i> Sesi & Nomor WhatsApp Gateway (Multi-Session)
                </h6>
                <button type="button" class="btn btn-sm btn-light text-info fw-bold" data-bs-toggle="modal" data-bs-target="#modalAddWaSession">
                    <i class="bx bx-plus me-1"></i> Tambah Nomor Baru
                </button>
            </div>
            <div class="card-body mt-3">
                @if(empty($waSessions) || $waSessions->isEmpty())
                    <div class="alert alert-light border text-center py-4 mb-0">
                        <p class="text-muted mb-0">Belum ada nomor WhatsApp yang ditambahkan. Silakan klik tombol <strong>Tambah Nomor Baru</strong> di atas.</p>
                    </div>
                @else
                    <div class="row" id="wa_sessions_container">
                        @foreach($waSessions as $waSession)
                            <div class="col-md-6 mb-4 session-card-wrapper" data-id="{{ $waSession->id }}">
                                <div class="card h-100 border shadow-sm mb-0">
                                    <div class="card-header bg-light d-flex align-items-center justify-content-between py-2">
                                        <strong class="text-secondary">{{ $waSession->label }}</strong>
                                        <div class="form-check form-switch ms-auto me-3">
                                            <input class="form-check-input toggle-session-switch" type="checkbox" 
                                                   id="toggle_switch_{{ $waSession->id }}" data-id="{{ $waSession->id }}"
                                                   {{ $waSession->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label small text-muted" for="toggle_switch_{{ $waSession->id }}">Aktif</label>
                                        </div>
                                        <button type="button" class="btn btn-xs btn-outline-danger delete-session-btn" data-id="{{ $waSession->id }}" title="Hapus Sesi">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                    <div class="card-body text-center p-3">
                                        <div id="loading_{{ $waSession->id }}" class="py-3">
                                            <div class="spinner-border text-info spinner-border-sm" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <span class="ms-2 text-muted small">Memeriksa status...</span>
                                        </div>

                                        <div id="content_{{ $waSession->id }}" class="d-none">
                                            <div class="mb-2">
                                                <span id="badge_{{ $waSession->id }}" class="badge p-2 px-3">Unknown</span>
                                            </div>
                                            <div class="small text-muted mb-2">
                                                <div><strong>Nomor:</strong> <span id="phone_{{ $waSession->id }}">{{ $waSession->phone_number ?? '-' }}</span></div>
                                                <div><strong>Status Sesi:</strong> <code id="raw_state_{{ $waSession->id }}">-</code></div>
                                            </div>

                                            <div id="qr_container_{{ $waSession->id }}" class="my-3 d-none">
                                                <p class="text-warning fw-bold mb-2 small">
                                                    <i class="bx bx-qr me-1"></i> Scan QR Code berikut dengan WhatsApp Anda:
                                                </p>
                                                <div class="bg-white p-2 d-inline-block rounded border shadow-sm">
                                                    <img id="qr_image_{{ $waSession->id }}" src="" alt="WhatsApp QR Code" class="img-fluid" style="width: 180px; height: 180px;">
                                                </div>
                                            </div>

                                            <div class="mt-2">
                                                <button type="button" class="btn btn-xs btn-outline-info check-session-btn me-1" data-id="{{ $waSession->id }}">
                                                    <i class="bx bx-refresh me-1"></i> Cek Koneksi
                                                </button>
                                                <button type="button" class="btn btn-xs btn-primary start-session-btn d-none" id="btn_start_{{ $waSession->id }}" data-id="{{ $waSession->id }}">
                                                    <i class="bx bx-play-circle me-1"></i> Mulai Sesi
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- RIWAYAT PESAN (LOG) --}}
<div class="card mt-2">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
        <div>
            <h5 class="mb-1"><i class="bx bx-message-square-dots me-2 text-primary"></i>Riwayat Pengiriman WhatsApp</h5>
            <small class="text-muted">Log rekap pengiriman pesan notifikasi otomatis WhatsApp ke nomor siswa.</small>
        </div>
        <button class="btn btn-sm btn-outline-primary text-nowrap" onclick="location.reload()">
            <i class="bx bx-refresh me-1"></i>Refresh Log
        </button>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-bordered mb-0" id="wa-log-table">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>#</th>
                        <th>Tujuan</th>
                        <th>Jenis Pesan</th>
                        <th>Status</th>
                        <th>Waktu Kirim</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $log->no_hp ?? $log->nomor_tujuan ?? '-' }}</td>
                        <td>{{ $log->jenis_pesan ?? 'Notifikasi' }}</td>
                        <td class="text-center">
                            @if (($log->status ?? '') === 'sent')
                                <span class="badge bg-success">Berhasil</span>
                            @else
                                <span class="badge bg-danger">Gagal</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $log->created_at ? $log->created_at->format('d-m-Y H:i') : ($log->sent_at ? $log->sent_at->format('d-m-Y H:i') : '-') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Belum ada riwayat pengiriman
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Sesi WhatsApp -->
<div class="modal fade" id="modalAddWaSession" tabindex="-1" aria-labelledby="modalAddWaSessionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content text-start">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalAddWaSessionLabel">Tambah Sesi WhatsApp Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_add_wa_session">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_session_label" class="form-label">Label Pengenal Nomor / Sesi</label>
                        <input type="text" class="form-control" id="new_session_label" name="label" required 
                               placeholder="Contoh: Nomor Utama PPDB, Nomor Cadangan Admin 1">
                        <small class="form-text text-muted">Label ini membantu Anda mengidentifikasi nomor WhatsApp yang terhubung.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn_submit_add_session">Simpan & Daftarkan Sesi</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Notifikasi Swal Flash Message
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 1800,
            showConfirmButton: false
        });
    @endif

    // Helper Dapatkan CSRF Token Dinamis
    const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    // Toggle show/hide password OpenWA API Key
    const toggleApiKeyBtn = document.getElementById('toggleApiKey');
    if (toggleApiKeyBtn) {
        toggleApiKeyBtn.addEventListener('click', function() {
            const input = document.getElementById('wa_api_key_input');
            const icon = document.getElementById('toggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bx bx-hide';
            } else {
                input.type = 'password';
                icon.className = 'bx bx-show';
            }
        });
    }

    // OpenWA Multi-Session Polling & AJAX Handlers
    const activePolls = {};

    function checkSessionStatus(sessionId, forceShowLoading = false) {
        const loadingEl = document.getElementById(`loading_${sessionId}`);
        const contentEl = document.getElementById(`content_${sessionId}`);
        const badgeEl = document.getElementById(`badge_${sessionId}`);
        const phoneEl = document.getElementById(`phone_${sessionId}`);
        const stateEl = document.getElementById(`raw_state_${sessionId}`);
        const qrContainer = document.getElementById(`qr_container_${sessionId}`);
        const qrImage = document.getElementById(`qr_image_${sessionId}`);
        const btnStart = document.getElementById(`btn_start_${sessionId}`);

        if (!loadingEl || !contentEl) return;

        if (forceShowLoading) {
            loadingEl.classList.remove('d-none');
            contentEl.classList.add('d-none');
        }

        fetch(`/panel/whatsapp-sessions/${sessionId}/status`)
            .then(response => response.json())
            .then(data => {
                loadingEl.classList.add('d-none');
                contentEl.classList.remove('d-none');

                if (data.success) {
                    stateEl.textContent = data.status;
                    phoneEl.textContent = data.phone_number || '-';
                    badgeEl.className = 'badge p-2 px-3';

                    if (data.connected) {
                        badgeEl.classList.add('bg-success');
                        badgeEl.textContent = 'Terhubung';
                        qrContainer.classList.add('d-none');
                        btnStart.classList.add('d-none');
                        stopPolling(sessionId);
                    } else {
                        if (data.status === 'NOT_STARTED') {
                            badgeEl.classList.add('bg-danger');
                            badgeEl.textContent = 'Offline';
                            qrContainer.classList.add('d-none');
                            btnStart.classList.remove('d-none');
                            stopPolling(sessionId);
                        } else {
                            badgeEl.classList.add('bg-warning', 'text-dark');
                            badgeEl.textContent = 'Butuh Scan QR';
                            btnStart.classList.add('d-none');

                            if (data.qrCode) {
                                qrContainer.classList.remove('d-none');
                                const qrSrc = data.qrCode.startsWith('data:') ? data.qrCode : 'data:image/png;base64,' + data.qrCode;
                                qrImage.src = qrSrc;
                            } else {
                                qrContainer.classList.add('d-none');
                            }
                            startPolling(sessionId);
                        }
                    }
                } else {
                    badgeEl.className = 'badge p-2 px-3 bg-danger';
                    badgeEl.textContent = 'Error Gateway';
                    stateEl.textContent = 'ERROR';
                    qrContainer.classList.add('d-none');
                    btnStart.classList.add('d-none');
                    stopPolling(sessionId);
                }
            })
            .catch(error => {
                loadingEl.classList.add('d-none');
                contentEl.classList.remove('d-none');
                badgeEl.className = 'badge p-2 px-3 bg-danger';
                badgeEl.textContent = 'Server Error';
                stateEl.textContent = 'SERVER_ERROR';
                qrContainer.classList.add('d-none');
                btnStart.classList.add('d-none');
                stopPolling(sessionId);
            });
    }

    function startPolling(sessionId) {
        if (!activePolls[sessionId]) {
            activePolls[sessionId] = setInterval(() => checkSessionStatus(sessionId), 5000);
        }
    }

    function stopPolling(sessionId) {
        if (activePolls[sessionId]) {
            clearInterval(activePolls[sessionId]);
            delete activePolls[sessionId];
        }
    }

    function checkAllSessions() {
        document.querySelectorAll('.session-card-wrapper').forEach(card => {
            const sessionId = card.getAttribute('data-id');
            checkSessionStatus(sessionId);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        checkAllSessions();
    });

    document.addEventListener('click', function(e) {
        // Cek koneksi manual
        if (e.target.closest('.check-session-btn')) {
            const btn = e.target.closest('.check-session-btn');
            const sessionId = btn.getAttribute('data-id');
            checkSessionStatus(sessionId, true);
        }

        // Mulai Sesi
        if (e.target.closest('.start-session-btn')) {
            const btn = e.target.closest('.start-session-btn');
            const sessionId = btn.getAttribute('data-id');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...';

            fetch(`/panel/whatsapp-sessions/${sessionId}/start`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    icon: data.success ? 'success' : 'error',
                    title: data.success ? 'Berhasil' : 'Gagal',
                    text: data.message
                });
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-play-circle me-1"></i> Mulai Sesi';
                checkSessionStatus(sessionId, true);
            })
            .catch(error => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server untuk memulai sesi.' });
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-play-circle me-1"></i> Mulai Sesi';
            });
        }

        // Menghapus Sesi
        if (e.target.closest('.delete-session-btn')) {
            const btn = e.target.closest('.delete-session-btn');
            const sessionId = btn.getAttribute('data-id');

            Swal.fire({
                title: 'Hapus Sesi WhatsApp?',
                text: "Sesi ini akan dihapus dari database dan server OpenWA.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/panel/whatsapp-sessions/${sessionId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            stopPolling(sessionId);
                            location.reload();
                        } else {
                            Swal.fire('Gagal', data.message || 'Gagal menghapus sesi.', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Gagal menghubungi server.', 'error');
                    });
                }
            });
        }
    });

    // Toggle Switch status aktif sesi
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('toggle-session-switch')) {
            const switchEl = e.target;
            const sessionId = switchEl.getAttribute('data-id');

            fetch(`/panel/whatsapp-sessions/${sessionId}/toggle`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    switchEl.checked = !switchEl.checked;
                    Swal.fire('Gagal', 'Gagal mengubah status sesi.', 'error');
                }
            })
            .catch(error => {
                switchEl.checked = !switchEl.checked;
                Swal.fire('Error', 'Gagal menghubungi server.', 'error');
            });
        }
    });

    // Submit form tambah sesi baru
    const formAddSession = document.getElementById('form_add_wa_session');
    if (formAddSession) {
        formAddSession.addEventListener('submit', function(e) {
            e.preventDefault();
            const btnSubmit = document.getElementById('btn_submit_add_session');
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            const formData = new FormData(this);

            fetch('/panel/whatsapp-sessions', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = 'Simpan & Daftarkan Sesi';

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sesi Berhasil Ditambahkan',
                        text: data.message
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal', data.message || 'Gagal menambahkan sesi baru.', 'error');
                }
            })
            .catch(error => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = 'Simpan & Daftarkan Sesi';
                Swal.fire('Error', 'Gagal menghubungi server OpenWA.', 'error');
            });
        });
    }
</script>
@endpush