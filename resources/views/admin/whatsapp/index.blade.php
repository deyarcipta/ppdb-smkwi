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
            <span class="badge bg-success">
                WhatsApp Aktif & Siap Digunakan
            </span>
        </div>

        {{-- QR CODE --}}
        <div id="qr-container" class="mt-4" style="display:none;">
            <img id="qr-image" class="img-fluid" width="280">
            <p class="text-muted mt-2">
                Scan QR menggunakan WhatsApp → Linked Devices
            </p>
        </div>

    </div>
</div>

{{-- RIWAYAT PESAN --}}
<div class="card mt-4">
    <div class="card-header">
        <h5>📨 Riwayat Pengiriman WhatsApp</h5>
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
const WA_BASE_URL = 'https://ppdb.smkwisataindonesia.sch.id/wa-api';

/* =====================
   CHECK STATUS WA
===================== */
async function checkStatus() {
    try {
        const health = await fetch(`${WA_BASE_URL}/health`);
        const healthData = await health.json();

        if (healthData.connected === true) {
            document.getElementById('wa-status').innerText =
                '✅ WhatsApp sudah terhubung';
            document.getElementById('wa-connected').style.display = 'block';
            document.getElementById('qr-container').style.display = 'none';
            return;
        }

        const qrRes = await fetch(`${WA_BASE_URL}/qr`);
        const qrData = await qrRes.json();

        if (qrData.status === 'qr') {
            document.getElementById('wa-status').innerText =
                '📱 Silakan scan QR WhatsApp';
            document.getElementById('qr-image').src = qrData.qr;
            document.getElementById('qr-container').style.display = 'block';
            document.getElementById('wa-connected').style.display = 'none';
        } else {
            document.getElementById('wa-status').innerText =
                '⏳ Menunggu WhatsApp siap...';
        }

    } catch (e) {
        document.getElementById('wa-status').innerText =
            '❌ WhatsApp server tidak aktif';
        console.error(e);
    }
}

/* =====================
   AUTO REFRESH LOG
===================== */
async function refreshLog() {
    try {
        const res = await fetch("{{ route('whatsapp.index') }}");
        const html = await res.text();

        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        const newBody = doc.querySelector('#wa-log-table tbody');
        const oldBody = document.querySelector('#wa-log-table tbody');

        if (newBody && oldBody) {
            oldBody.innerHTML = newBody.innerHTML;
        }
    } catch (e) {
        console.error('Gagal refresh log', e);
    }
}

setInterval(checkStatus, 3000);
setInterval(refreshLog, 10000);

checkStatus();
</script>
@endpush
