@extends('admin.layouts.app')

@section('title', 'WhatsApp Bot')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Status WhatsApp Bot</h5>
    </div>

    <div class="card-body text-center">

        {{-- STATUS TEXT --}}
        <h5 id="wa-status" class="fw-bold text-secondary">
            Menghubungi server WhatsApp...
        </h5>

        {{-- CONNECTED INFO --}}
        <div id="wa-connected" style="display:none;" class="mt-3">
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <strong>WhatsApp Aktif & Siap Digunakan</strong>
            </div>
            <div class="mt-2">
                <button class="btn btn-sm btn-outline-primary" onclick="getConnectionInfo()">
                    <i class="fas fa-info-circle"></i> Lihat Info Koneksi
                </button>
            </div>
        </div>

        {{-- QR CODE --}}
        <div id="qr-container" class="mt-4" style="display:none;">
            <div class="alert alert-warning">
                <i class="fas fa-qrcode"></i>
                Scan QR Code di bawah untuk menghubungkan WhatsApp
            </div>
            <img id="qr-image" class="img-fluid" width="280">
            <p class="text-muted mt-2">
                Scan QR menggunakan WhatsApp → Linked Devices
            </p>
            <div class="mt-3">
                <button class="btn btn-sm btn-secondary" onclick="checkStatus()">
                    <i class="fas fa-sync"></i> Refresh QR
                </button>
            </div>
        </div>

        {{-- WAITING STATUS --}}
        <div id="wa-waiting" style="display:none;" class="mt-4">
            <div class="alert alert-info">
                <i class="fas fa-clock"></i>
                <strong>Menunggu WhatsApp siap...</strong>
            </div>
        </div>

    </div>
</div>

{{-- CONNECTION INFO MODAL --}}
<div class="modal fade" id="connectionInfoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Info Koneksi WhatsApp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="connection-details" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- RIWAYAT PESAN --}}
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>📨 Riwayat Pengiriman WhatsApp</h5>
        <button class="btn btn-sm btn-primary" onclick="refreshLog()">
            <i class="fas fa-sync"></i> Refresh
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
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $log->nomor_tujuan }}</td>
                        <td>{{ $log->jenis_pesan }}</td>
                        <td class="text-center">
                            @if ($log->status === 'sent')
                                <span class="badge bg-success">Berhasil</span>
                            @else
                                <span class="badge bg-danger">Gagal</span>
                            @endif
                        </td>
                        <td>{{ $log->sent_at?->format('d-m-Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Belum ada riwayat pengiriman
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// const WA_BASE_URL = 'http://localhost:3000';
const WA_BASE_URL = 'https://ppdb.smkwisataindonesia.sch.id/wa-api';

// Status elements
const statusEl = document.getElementById('wa-status');
const connectedEl = document.getElementById('wa-connected');
const qrContainer = document.getElementById('qr-container');
const waitingEl = document.getElementById('wa-waiting');

/* =====================
   CHECK STATUS WA (FIXED)
===================== */
async function checkStatus() {
    try {
        // 1. Check health endpoint terlebih dahulu
        const healthRes = await fetch(`${WA_BASE_URL}/health`);
        const healthData = await healthRes.json();
        
        console.log('Health Data:', healthData);
        
        // 2. Check apakah connected berdasarkan health endpoint
        if (healthData.connected === true) {
            // WhatsApp sudah terhubung
            statusEl.innerHTML = '<span class="text-success">✅ WhatsApp sudah terhubung</span>';
            connectedEl.style.display = 'block';
            qrContainer.style.display = 'none';
            waitingEl.style.display = 'none';
            return;
        }
        
        // 3. Jika tidak connected, check QR
        const qrRes = await fetch(`${WA_BASE_URL}/qr`);
        const qrData = await qrRes.json();
        
        console.log('QR Data:', qrData);
        
        if (qrData.status === 'qr') {
            // Ada QR code untuk di-scan
            statusEl.innerHTML = '<span class="text-warning">📱 Silakan scan QR WhatsApp</span>';
            document.getElementById('qr-image').src = qrData.qr;
            qrContainer.style.display = 'block';
            connectedEl.style.display = 'none';
            waitingEl.style.display = 'none';
        } 
        else if (qrData.status === 'waiting' || qrData.status === 'connected') {
            // Menunggu atau sudah connected (tapi mungkin belum ready)
            if (healthData.isInitializing) {
                statusEl.innerHTML = '<span class="text-info">🔄 Sedang menginisialisasi WhatsApp...</span>';
            } else {
                statusEl.innerHTML = '<span class="text-info">⏳ Menunggu WhatsApp siap...</span>';
            }
            waitingEl.style.display = 'block';
            qrContainer.style.display = 'none';
            connectedEl.style.display = 'none';
        }
        else {
            // Status tidak dikenali
            statusEl.innerHTML = '<span class="text-danger">❌ Status tidak dikenali</span>';
        }
        
    } catch (error) {
        console.error('Error checking status:', error);
        statusEl.innerHTML = '<span class="text-danger">❌ WhatsApp server tidak aktif</span>';
        connectedEl.style.display = 'none';
        qrContainer.style.display = 'none';
        waitingEl.style.display = 'none';
    }
}

/* =====================
   GET CONNECTION INFO
===================== */
async function getConnectionInfo() {
    try {
        const modal = new bootstrap.Modal(document.getElementById('connectionInfoModal'));
        const detailsEl = document.getElementById('connection-details');
        
        // Tampilkan loading
        detailsEl.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        `;
        
        modal.show();
        
        // Ambil data health
        const res = await fetch(`${WA_BASE_URL}/health`);
        const data = await res.json();
        
        // Format data untuk ditampilkan
        detailsEl.innerHTML = `
            <div class="text-start">
                <p><strong>Status:</strong> 
                    <span class="badge ${data.connected ? 'bg-success' : 'bg-danger'}">
                        ${data.connected ? 'Connected' : 'Disconnected'}
                    </span>
                </p>
                <p><strong>Reconnect Attempts:</strong> ${data.reconnectAttempts}</p>
                <p><strong>Initializing:</strong> ${data.isInitializing ? 'Yes' : 'No'}</p>
                <p><strong>Last Check:</strong> ${new Date(data.timestamp).toLocaleString()}</p>
                <hr>
                <p class="text-muted small">
                    <i class="fas fa-info-circle"></i>
                    WhatsApp Bot v1.0 | Port: ${WA_BASE_URL.split(':').pop()}
                </p>
            </div>
        `;
        
    } catch (error) {
        document.getElementById('connection-details').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                Gagal mengambil info koneksi: ${error.message}
            </div>
        `;
    }
}

/* =====================
   AUTO REFRESH LOG
===================== */
async function refreshLog() {
    try {
        const tbody = document.querySelector('#wa-log-table tbody');
        const button = event?.target || document.querySelector('button[onclick="refreshLog()"]');
        
        if (button) {
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
            button.disabled = true;
        }
        
        const res = await fetch("{{ route('whatsapp.index') }}");
        const html = await res.text();

        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        const newBody = doc.querySelector('#wa-log-table tbody');
        
        if (newBody && tbody) {
            tbody.innerHTML = newBody.innerHTML;
            
            // Tampilkan notifikasi sukses
            showToast('success', 'Log berhasil di-refresh');
        }
        
        if (button) {
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            }, 1000);
        }
        
    } catch (e) {
        console.error('Gagal refresh log', e);
        showToast('error', 'Gagal refresh log');
    }
}

/* =====================
   TOAST NOTIFICATION
===================== */
function showToast(type, message) {
    // Cek apakah toast container sudah ada
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
    toast.setAttribute('role', 'alert');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Inisialisasi dan tampilkan toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Hapus toast setelah hilang
    toast.addEventListener('hidden.bs.toast', function () {
        toast.remove();
    });
}

/* =====================
   AUTO REFRESH
===================== */
setInterval(checkStatus, 5000); // Check status setiap 5 detik
setInterval(refreshLog, 15000); // Refresh log setiap 15 detik

// Inisialisasi pertama kali
checkStatus();
</script>

<style>
.toast-container {
    z-index: 9999;
}
.badge {
    font-size: 0.8em;
}
</style>
@endpush