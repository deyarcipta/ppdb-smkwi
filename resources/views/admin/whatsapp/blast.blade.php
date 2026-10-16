@extends('admin.layouts.app')

@section('title', 'Broadcast / Blast WhatsApp')

@section('content')
<div class="container-fluid p-0">

  {{-- Status Pengaturan WhatsApp Sistem --}}
  @if(isset($pengaturan) && !$pengaturan->wa_status && !$pengaturan->enable_whatsapp)
  <div class="alert alert-warning d-flex align-items-center justify-content-between mb-4 shadow-sm" role="alert">
      <div>
          <i class="bx bx-error-circle me-2 fs-5"></i>
          <strong>Perhatian: Fitur Notifikasi WhatsApp saat ini NONAKTIF di Pengaturan Aplikasi.</strong> Pesan yang dijadwalkan akan tersimpan di Queue sampai fitur diaktifkan.
      </div>
      <a href="{{ route('pengaturan-aplikasi.index') }}" class="btn btn-sm btn-warning fw-bold text-nowrap">
          <i class="bx bx-cog me-1"></i> Buka Pengaturan
      </a>
  </div>
  @endif

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
      <div class="d-flex align-items-center">
          <i class="bx bx-check-circle me-2 fs-4"></i>
          <div>{{ session('success') }}</div>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif

  @if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
      <div class="d-flex align-items-center">
          <i class="bx bx-x-circle me-2 fs-4"></i>
          <div>{{ session('error') }}</div>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif

  <!-- Header Section -->
  <div class="row mb-4 mx-0">
      <div class="col-12 px-0">
          <div class="card bg-white border-0 shadow-sm">
              <div class="card-body py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                  <div>
                      <h4 class="mb-1 text-primary fw-bold">
                          <i class="bx bxl-whatsapp me-2"></i>Broadcast / Blast WhatsApp Siswa
                      </h4>
                      <p class="text-muted mb-0 small">
                          Kirim informasi pengumuman, pengingat tagihan pembayaran, atau kelulusan secara massal dengan kriteria filter dinamis.
                      </p>
                  </div>
                  <div class="d-flex gap-2">
                      @if(Auth::user()->role === 'superadmin')
                      <a href="{{ route('whatsapp.index') }}" class="btn btn-outline-secondary btn-sm">
                          <i class="bx bx-history me-1"></i> Log & Status Server
                      </a>
                      <a href="{{ route('template-pesan.index') }}" class="btn btn-outline-primary btn-sm">
                          <i class="bx bx-file me-1"></i> Kelola Template
                      </a>
                      @endif
                  </div>
              </div>
          </div>
      </div>
  </div>

  <form id="formBlastWA" action="{{ route('whatsapp.blast.send') }}" method="POST">
      @csrf

      <div class="row g-4">
          <!-- ================= KOLOM KIRI: FILTER & TARGET PENERIMA ================= -->
          <div class="col-lg-5">
              <div class="card border-0 shadow-sm h-100">
                  <div class="card-header bg-light py-3 border-bottom d-flex align-items-center justify-content-between">
                      <h6 class="mb-0 fw-bold text-dark">
                          <i class="bx bx-filter-alt text-primary me-2"></i>1. Kriteria Target Siswa
                      </h6>
                      <span class="badge bg-primary text-white fw-bold px-2 py-1 shadow-sm">
                          <i class="bx bx-check me-1"></i>Filter Dinamis
                      </span>
                  </div>

                  <div class="card-body">
                      <!-- Tahun Ajaran -->
                      <div class="mb-3">
                          <label class="form-label small fw-bold text-muted">Tahun Ajaran</label>
                          <select name="tahun_ajaran_id" id="filter_tahun_ajaran" class="form-select filter-trigger">
                              <option value="">-- Semua Tahun Ajaran --</option>
                              @foreach($tahunAjaranList as $ta)
                                  <option value="{{ $ta->id }}" {{ ($activeTahunAjaran && $activeTahunAjaran->id == $ta->id) ? 'selected' : '' }}>
                                      {{ $ta->nama }} {{ $ta->status == 'aktif' ? '(Aktif)' : '' }}
                                  </option>
                              @endforeach
                          </select>
                      </div>

                      <!-- Gelombang Pendaftaran -->
                      <div class="mb-3">
                          <label class="form-label small fw-bold text-muted">
                              <i class="bx bx-time-five me-1 text-primary"></i>Gelombang Pendaftaran
                          </label>
                          <select name="gelombang_id" id="filter_gelombang" class="form-select filter-trigger">
                              <option value="">-- Semua Gelombang --</option>
                              @foreach($gelombangList as $gel)
                                  <option value="{{ $gel->id }}">
                                      {{ $gel->nama_gelombang }} ({{ $gel->tahunAjaran->nama ?? 'Umum' }})
                                  </option>
                              @endforeach
                          </select>
                          <small class="text-muted fs-11">Pilih misal <strong>Gelombang 1</strong> untuk mengirimkan info hanya ke pendaftar gelombang tersebut.</small>
                      </div>

                      <!-- Jurusan -->
                      <div class="mb-3">
                          <label class="form-label small fw-bold text-muted">
                              <i class="bx bx-book-bookmark me-1 text-primary"></i>Jurusan Pilihan
                          </label>
                          <select name="jurusan_id" id="filter_jurusan" class="form-select filter-trigger">
                              <option value="">-- Semua Jurusan --</option>
                              @foreach($jurusanList as $jur)
                                  <option value="{{ $jur->id }}">{{ $jur->nama_jurusan }} ({{ $jur->kode_jurusan }})</option>
                              @endforeach
                          </select>
                      </div>

                      <!-- Status Pendaftar -->
                      <div class="mb-3">
                          <label class="form-label small fw-bold text-muted">
                              <i class="bx bx-user-check me-1 text-primary"></i>Status Keputusan Siswa
                          </label>
                          <select name="status_pendaftar" id="filter_status_pendaftar" class="form-select filter-trigger">
                              <option value="semua">Semua Status (Diterima / Pending / Ditolak)</option>
                              <option value="pending">Menunggu Keputusan / Seleksi (Pending)</option>
                              <option value="diterima">Siswa DITERIMA</option>
                              <option value="ditolak">Siswa DITOLAK</option>
                          </select>
                      </div>

                      <!-- Status Pembayaran Keuangan -->
                      <div class="mb-3">
                          <label class="form-label small fw-bold text-muted">
                              <i class="bx bx-wallet-alt me-1 text-success"></i>Status Keuangan / Pembayaran
                          </label>
                          <select name="status_pembayaran" id="filter_status_pembayaran" class="form-select border-success filter-trigger">
                              <option value="semua">Semua Siswa (Tanpa batasan bayar)</option>
                              <option value="belum_lunas" class="text-warning fw-bold">⚠️ Belum Lunas (Masih Ada Sisa Tagihan/Cicilan)</option>
                              <option value="belum_bayar" class="text-danger fw-bold">❌ Belum Bayar Sama Sekali (Rp 0)</option>
                              <option value="lunas" class="text-success fw-bold">✅ Lunas (Memenuhi Target Biaya)</option>
                              <option value="menunggu_verifikasi" class="text-info fw-bold">⏳ Menunggu Verifikasi Bukti Transfer</option>
                          </select>
                          <small class="text-muted fs-11">Contoh: Pilih <em>"Belum Lunas"</em> untuk blast pengingat tagihan pembayaran.</small>
                      </div>

                      <!-- Target Nomor HP Penerima -->
                      <div class="mb-3 p-3 bg-light rounded border">
                          <label class="form-label small fw-bold text-dark d-block mb-2">
                              <i class="bx bx-phone-call me-1 text-primary"></i>Kirim Pesan Ke:
                          </label>
                          <div class="d-flex flex-column gap-2">
                              <div class="form-check">
                                  <input class="form-check-input filter-trigger" type="radio" name="target_penerima" id="target_siswa" value="siswa" checked>
                                  <label class="form-check-label small" for="target_siswa">
                                      <strong>Nomor HP Siswa</strong> (No. WA Calon Pendaftar)
                                  </label>
                              </div>
                              <div class="form-check">
                                  <input class="form-check-input filter-trigger" type="radio" name="target_penerima" id="target_ortu" value="orang_tua">
                                  <label class="form-check-label small" for="target_ortu">
                                      <strong>Nomor HP Orang Tua</strong> (Prioritas: Ibu → Ayah → Wali)
                                  </label>
                              </div>
                              <div class="form-check">
                                  <input class="form-check-input filter-trigger" type="radio" name="target_penerima" id="target_semua" value="semua">
                                  <label class="form-check-label small" for="target_semua">
                                      <strong>Keduanya</strong> (Siswa & 1 Orang Tua, Anti-Duplikat)
                                  </label>
                              </div>
                          </div>
                      </div>

                      <!-- Sesi WhatsApp Pengirim (Hanya untuk Superadmin) -->
                      @if(Auth::user()->role === 'superadmin')
                      <div class="mb-3">
                          <label class="form-label small fw-bold text-muted">
                              <i class="bx bx-devices me-1 text-primary"></i>Sesi Pengirim (Multi-Session)
                          </label>
                          <select name="session_id" class="form-select form-select-sm">
                              <option value="auto">Rotasi Otomatis (Load Balancing Antara Sesi Aktif) - Rekomendasi</option>
                              @foreach($waSessions as $sesi)
                                  <option value="{{ $sesi->session_id }}">
                                      {{ $sesi->label }} ({{ $sesi->phone_number ?? 'No. Belum Tercatat' }}) - {{ $sesi->status }}
                                  </option>
                              @endforeach
                          </select>
                      </div>
                      @else
                      <input type="hidden" name="session_id" value="auto">
                      @endif

                      <!-- Kotak Indikator Hasil Filter -->
                      <div class="card bg-primary bg-opacity-10 border border-primary border-opacity-25 mt-4">
                          <div class="card-body p-3">
                              <div class="d-flex align-items-center justify-content-between mb-2">
                                  <div class="fw-bold text-primary">
                                      <i class="bx bx-group me-1"></i>Ringkasan Sasaran
                                  </div>
                                  <span id="spinnerPreview" class="spinner-border spinner-border-sm text-primary d-none" role="status"></span>
                              </div>

                              <div class="row g-2 text-center my-2">
                                  <div class="col-6">
                                      <div class="p-2 bg-white rounded border">
                                          <div class="text-muted fs-11 text-uppercase fw-semibold">Target Siswa</div>
                                          <div class="fs-4 fw-bold text-primary" id="lblTotalTarget">-</div>
                                      </div>
                                  </div>
                                  <div class="col-6">
                                      <div class="p-2 bg-white rounded border">
                                          <div class="text-muted fs-11 text-uppercase fw-semibold">Memiliki No WA</div>
                                          <div class="fs-4 fw-bold text-success" id="lblTotalValid">-</div>
                                      </div>
                                  </div>
                              </div>

                              <button type="button" class="btn btn-outline-primary btn-sm w-100 mt-2" data-bs-toggle="modal" data-bs-target="#modalDaftarPenerima">
                                  <i class="bx bx-list-check me-1"></i>Lihat Rincian Daftar Siswa Penerima
                              </button>
                          </div>
                      </div>
                  </div>
              </div>
          </div>


          <!-- ================= KOLOM KANAN: PESAN & TEMPLATE DINAMIS ================= -->
          <div class="col-lg-7">
              <div class="card border-0 shadow-sm h-100">
                  <div class="card-header bg-light py-3 border-bottom d-flex align-items-center justify-content-between">
                      <h6 class="mb-0 fw-bold text-dark">
                          <i class="bx bx-message-dots text-success me-2"></i>2. Susun Pesan WhatsApp
                      </h6>
                      <span class="badge bg-success text-white fw-bold px-2 py-1 shadow-sm">
                          <i class="bx bx-check me-1"></i>Personalisasi Dinamis
                      </span>
                  </div>

                  <div class="card-body">
                      <!-- Pilih Contoh Template Pesan (Opsional) -->
                      <div class="mb-3">
                          <label class="form-label small fw-bold text-muted d-flex align-items-center justify-content-between">
                              <span><i class="bx bx-file me-1 text-primary"></i>Pilih Template / Contoh Format Pesan</span>
                              <span class="badge bg-secondary text-white fw-semibold px-2 py-1">Opsional</span>
                          </label>
                          <select id="selectQuickTemplate" class="form-select">
                              <option value="custom">✍️ Tulis Pesan Sendiri / Bebas</option>
                              <option value="template_pengumuman_gelombang" selected>📢 Pengumuman Tes / Wawancara Gelombang</option>
                              <option value="template_tagihan_belum_lunas">💰 Pengingat Batas Akhir Pembayaran (Belum Lunas)</option>
                              <option value="template_pemberitahuan_diterima">🎉 Selamat Anda Dinyatakan Diterima</option>
                              <option value="template_berkas_pendaftaran">📑 Pengingat Kelengkapan Formulir / Berkas</option>
                              @if($templatePesanList->isNotEmpty())
                                  <optgroup label="Template Tersimpan di Database">
                                      @foreach($templatePesanList as $tpl)
                                          <option value="db_{{ $tpl->id }}" data-content="{{ $tpl->isi_pesan }}">{{ $tpl->judul }} ({{ $tpl->jenis_pesan }})</option>
                                      @endforeach
                                  </optgroup>
                              @endif
                          </select>
                          <small class="text-muted fs-11">Pilih salah satu contoh di atas untuk memuat draf pesan siap pakai, lalu Anda dapat mengedit teksnya secara leluasa.</small>
                      </div>

                      <!-- Variabel Dinamis (Klik untuk Sisipkan) -->
                      <div class="mb-3">
                          <div class="d-flex align-items-center justify-content-between mb-1">
                              <label class="form-label small fw-bold text-dark mb-0">
                                  <i class="bx bx-code-curly me-1 text-primary"></i>Variabel Personalisasi Otomatis:
                              </label>
                              <small class="text-muted fs-11">Klik tombol tag untuk memasukkan ke pesan</small>
                          </div>
                          <div class="d-flex flex-wrap gap-1 p-2 bg-light rounded border">
                              <button type="button" class="btn btn-xs btn-white border shadow-none btn-insert-tag" data-tag="{nama}" title="Nama Lengkap Siswa">
                                  <code>{nama}</code>
                              </button>
                              <button type="button" class="btn btn-xs btn-white border shadow-none btn-insert-tag" data-tag="{no_pendaftaran}" title="No. Pendaftaran Resmi">
                                  <code>{no_pendaftaran}</code>
                              </button>
                              <button type="button" class="btn btn-xs btn-white border shadow-none btn-insert-tag" data-tag="{gelombang}" title="Gelombang Pendaftaran">
                                  <code>{gelombang}</code>
                              </button>
                              <button type="button" class="btn btn-xs btn-white border shadow-none btn-insert-tag" data-tag="{jurusan}" title="Pilihan Jurusan">
                                  <code>{jurusan}</code>
                              </button>
                              <button type="button" class="btn btn-xs btn-white border shadow-none btn-insert-tag" data-tag="{sisa_tagihan}" title="Sisa Biaya Belum Dibayar">
                                  <code>{sisa_tagihan}</code>
                              </button>
                              <button type="button" class="btn btn-xs btn-white border shadow-none btn-insert-tag" data-tag="{total_biaya}" title="Total Biaya Gelombang">
                                  <code>{total_biaya}</code>
                              </button>
                              <button type="button" class="btn btn-xs btn-white border shadow-none btn-insert-tag" data-tag="{total_bayar}" title="Total Nominal yang Sudah Masuk">
                                  <code>{total_bayar}</code>
                              </button>
                              <button type="button" class="btn btn-xs btn-white border shadow-none btn-insert-tag" data-tag="{status_pembayaran}" title="Status Lunas / Belum Lunas">
                                  <code>{status_pembayaran}</code>
                              </button>
                              <button type="button" class="btn btn-xs btn-white border shadow-none btn-insert-tag" data-tag="{link_login}" title="Link Login Siswa">
                                  <code>{link_login}</code>
                              </button>
                              <button type="button" class="btn btn-xs btn-white border shadow-none btn-insert-tag" data-tag="{nama_sekolah}" title="Nama Sekolah">
                                  <code>{nama_sekolah}</code>
                              </button>
                          </div>
                      </div>

                      <!-- Textarea Pesan -->
                      <div class="mb-3">
                          <label class="form-label small fw-bold text-muted">Isi Pesan WhatsApp</label>
                          <textarea name="pesan" id="txtPesanWA" class="form-control font-monospace" rows="9" placeholder="Tulis pesan pengumuman atau pengingat di sini..." required></textarea>
                          <div class="d-flex justify-content-between align-items-center mt-1">
                              <small class="text-muted fs-11">Tips: Gunakan <code>*tebal*</code>, <code>_miring_</code>, atau <code>~coret~</code> untuk format tulisan WhatsApp.</small>
                              <small class="text-muted fs-11"><span id="charCount">0</span> karakter</small>
                          </div>
                      </div>

                      <!-- Live Chat WhatsApp Preview -->
                      <div class="mb-4">
                          <label class="form-label small fw-bold text-muted d-flex align-items-center">
                              <i class="bx bxl-whatsapp text-success fs-5 me-1"></i> Pratinjau Tampilan di Layar Siswa:
                          </label>
                          <div class="wa-chat-preview p-3 rounded" style="background-color: #E5DDD5; min-height: 120px; border: 1px solid #d3c8be;">
                              <div class="wa-bubble bg-white p-3 rounded-3 shadow-sm position-relative" style="max-width: 90%; border-radius: 7.5px 7.5px 7.5px 0 !important;">
                                  <div id="waPreviewContent" class="small text-dark" style="white-space: pre-wrap; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                                      <em>(Tulis pesan di atas untuk melihat simulasi tampilan di sini...)</em>
                                  </div>
                                  <div class="text-end text-muted mt-1" style="font-size: 10px;">
                                      {{ date('H:i') }} <i class="bx bx-check-double text-primary"></i>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <!-- Tombol Eksekusi Blast -->
                      <div class="border-top pt-3">
                          <button type="button" id="btnTriggerBlast" class="btn btn-success btn-lg w-100 fw-bold shadow-sm py-2">
                              <i class="bx bxl-whatsapp me-2 fs-5 align-middle"></i>
                              Jadwalkan & Kirim Broadcast WhatsApp
                          </button>
                          <small class="text-muted fs-11 text-center d-block mt-2">
                              <i class="bx bx-shield-quarter me-1 text-success"></i>Sistem otomatis menggunakan <strong>Anti-Ban Queue</strong> (delay 12-18 detik per nomor) agar nomor WhatsApp sekolah aman.
                          </small>
                      </div>

                  </div>
              </div>
          </div>
      </div>

  </form>
</div>

<!-- ================= MODAL RINCIAN DAFTAR PENERIMA ================= -->
<div class="modal fade" id="modalDaftarPenerima" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light py-3">
                <h5 class="modal-title fw-bold text-primary">
                    <i class="bx bx-list-check me-2"></i>Daftar Calon Siswa Penerima Pesan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="small">
                        Menampilkan siswa yang cocok dengan kriteria filter aktif saat ini:
                    </div>
                    <div class="badge bg-primary fs-12 px-3 py-2" id="badgeModalTotal">
                        Memuat data...
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 480px;">
                    <table class="table table-bordered table-hover table-striped mb-0 align-middle">
                        <thead class="table-light text-center small fw-bold sticky-top">
                            <tr>
                                <th width="40">#</th>
                                <th width="140">No Daftar</th>
                                <th width="240">Nama Siswa</th>
                                <th width="140">Gelombang</th>
                                <th width="140">No. HP Dituju</th>
                                <th width="130">Total Bayar</th>
                                <th width="130">Sisa Tagihan</th>
                                <th width="120">Status Bayar</th>
                                <th width="100">Status</th>
                            </tr>
                        </thead>
                        <tbody id="tblPreviewReceiversBody">
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <div class="spinner-border spinner-border-sm text-primary me-2"></div> Memuat daftar penerima...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterTriggers = document.querySelectorAll('.filter-trigger');
    const txtPesanWA = document.getElementById('txtPesanWA');
    const waPreviewContent = document.getElementById('waPreviewContent');
    const charCount = document.getElementById('charCount');
    const lblTotalTarget = document.getElementById('lblTotalTarget');
    const lblTotalValid = document.getElementById('lblTotalValid');
    const spinnerPreview = document.getElementById('spinnerPreview');
    const tblPreviewReceiversBody = document.getElementById('tblPreviewReceiversBody');
    const badgeModalTotal = document.getElementById('badgeModalTotal');
    const selectQuickTemplate = document.getElementById('selectQuickTemplate');
    const btnTriggerBlast = document.getElementById('btnTriggerBlast');
    const formBlastWA = document.getElementById('formBlastWA');

    // Preset template teks
    const quickTemplates = {
        template_pengumuman_gelombang: 
`Halo *{nama}*,

📢 *PENGUMUMAN SELEKSI GELOMBANG*
Diberitahukan kepada seluruh calon siswa *{gelombang}* {nama_sekolah}, bahwa pelaksanaan tes peminatan dan wawancara akan diselenggarakan pada:

📅 Hari/Tanggal: Senin, 20 Oktober 2026
⏰ Waktu: Pukul 08.00 WIB s.d Selesai
🏫 Tempat: Kampus {nama_sekolah}
👕 Pakaian: Seragam SMP/MTs asal (rapi & sopan)

Silakan unduh atau cetak Kartu Tanda Peserta di portal siswa:
🔗 {link_login}

Jika ada pertanyaan, silakan hubungi Panitia {tipe_aplikasi}.
Terima kasih!`,

        template_tagihan_belum_lunas:
`Yth. Orang Tua / Calon Siswa *{nama}* ({no_pendaftaran}),

Mengingatkan kembali perihal administrasi pembayaran biaya pendaftaran {tipe_aplikasi} untuk *{gelombang}* di *{nama_sekolah}*.

*Rincian Pembayaran Anda:*
• Pilihan Jurusan: {jurusan}
• Total Biaya: {total_biaya}
• Telah Dibayar: {total_bayar}
• *Sisa Tagihan: {sisa_tagihan}*

Batas akhir pelunasan administrasi adalah sebelum penutupan gelombang ini.
Pembayaran dapat ditransfer ke rekening resmi atau datang langsung ke ruang panitia.

Konfirmasi pembayaran atau upload bukti transfer dapat dilakukan melalui portal:
🔗 {link_login}

Terima kasih atas kerja samanya.
_Panitia {tipe_aplikasi} {nama_sekolah}_`,

        template_pemberitahuan_diterima:
`🎉 *SELAMAT! ANDA DINYATAKAN DITERIMA*

Halo *{nama}* ({no_pendaftaran}),
Selamat! Berdasarkan hasil seleksi berkas dan tes wawancara, Anda dinyatakan:

*RESMI DITERIMA SEBAGAI SISWA BARU*
Jurusan: *{jurusan}*
Gelombang: *{gelombang}*
Sekolah: *{nama_sekolah}*

Langkah selanjutnya adalah melakukan pendaftaran ulang dan melengkapi berkas administrasi melalui portal:
🔗 {link_login}

Selamat bergabung dengan keluarga besar {nama_sekolah}!`,

        template_berkas_pendaftaran:
`Halo *{nama}*,

Panitia {tipe_aplikasi} {nama_sekolah} mengingatkan bahwa berkas formulir pendaftaran Anda untuk nomor *{no_pendaftaran}* belum lengkap.

Silakan segera login ke akun Anda dan lengkapi data formulir serta upload pas foto terbaru:
🔗 {link_login}

Kelengkapan data diperlukan untuk pencetakan Kartu Ujian dan verifikasi berkas. Terima kasih!`
    };

    // Fungsi Update AJAX Preview
    let debounceTimer;
    function fetchPreview() {
        spinnerPreview.classList.remove('d-none');
        
        const params = new URLSearchParams();
        params.append('tahun_ajaran_id', document.getElementById('filter_tahun_ajaran').value);
        params.append('gelombang_id', document.getElementById('filter_gelombang').value);
        params.append('jurusan_id', document.getElementById('filter_jurusan').value);
        params.append('status_pendaftar', document.getElementById('filter_status_pendaftar').value);
        params.append('status_pembayaran', document.getElementById('filter_status_pembayaran').value);

        const targetPenerima = document.querySelector('input[name="target_penerima"]:checked')?.value || 'siswa';
        params.append('target_penerima', targetPenerima);
        params.append('_token', '{{ csrf_token() }}');

        fetch("{{ route('whatsapp.blast.preview') }}", {
            method: 'POST',
            body: params,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            spinnerPreview.classList.add('d-none');
            if (data.success) {
                lblTotalTarget.innerText = data.total_target;
                lblTotalValid.innerText = data.total_valid_phone;
                badgeModalTotal.innerText = `Total Terfilter: ${data.total_target} Siswa (${data.total_valid_phone} Memiliki No WA)`;

                // Render tabel modal preview
                if (data.receivers.length === 0) {
                    tblPreviewReceiversBody.innerHTML = `
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bx bx-info-circle fs-3 d-block mb-1"></i>
                                Tidak ada siswa yang sesuai dengan kombinasi filter saat ini.
                            </td>
                        </tr>
                    `;
                } else {
                    let html = '';
                    data.receivers.forEach((item, idx) => {
                        let badgeBayar = '<span class="badge bg-secondary">Belum Bayar</span>';
                        if (item.status_bayar === 'lunas') {
                            badgeBayar = '<span class="badge bg-success">Lunas</span>';
                        } else if (item.status_bayar === 'belum_lunas') {
                            badgeBayar = '<span class="badge bg-warning text-dark">Belum Lunas</span>';
                        }

                        let badgePendaftar = '<span class="badge bg-secondary">Pending</span>';
                        if (item.status_pendaftar === 'diterima') {
                            badgePendaftar = '<span class="badge bg-success">Diterima</span>';
                        } else if (item.status_pendaftar === 'ditolak') {
                            badgePendaftar = '<span class="badge bg-danger">Ditolak</span>';
                        }

                        let phoneDisplay = item.phones_to_send;
                        if (!item.has_valid_phone) {
                            phoneDisplay = '<span class="text-danger fw-bold"><i class="bx bx-x"></i> Tidak Ada No WA</span>';
                        }

                        html += `
                            <tr>
                                <td class="text-center">${idx + 1}</td>
                                <td class="text-center fw-medium">${item.no_daftar}</td>
                                <td>
                                    <div class="fw-bold text-dark">${item.nama}</div>
                                    <small class="text-primary fw-semibold">${item.no_pendaftaran}</small>
                                </td>
                                <td>${item.gelombang}</td>
                                <td>${phoneDisplay}</td>
                                <td class="text-end fw-semibold text-success">${item.total_bayar}</td>
                                <td class="text-end fw-bold text-danger">${item.sisa_tagihan}</td>
                                <td class="text-center">${badgeBayar}</td>
                                <td class="text-center">${badgePendaftar}</td>
                            </tr>
                        `;
                    });

                    if (data.total_target > data.receivers.length) {
                        html += `
                            <tr>
                                <td colspan="9" class="text-center py-2 text-muted bg-light">
                                    <em>(Menampilkan 100 dari ${data.total_target} siswa. Seluruh siswa yang memenuhi filter akan dikirimi pesan).</em>
                                </td>
                            </tr>
                        `;
                    }

                    tblPreviewReceiversBody.innerHTML = html;
                }
            }
        })
        .catch(err => {
            spinnerPreview.classList.add('d-none');
            console.error('Fetch preview error:', err);
        });
    }

    // Trigger filter change dengan debounce
    filterTriggers.forEach(el => {
        el.addEventListener('change', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchPreview, 300);
        });
    });

    // Panggil preview awal
    fetchPreview();

    // Live Text Preview
    function updateTextPreview() {
        const text = txtPesanWA.value;
        charCount.innerText = text.length;

        if (!text.trim()) {
            waPreviewContent.innerHTML = '<em>(Tulis pesan di atas untuk melihat simulasi tampilan di sini...)</em>';
            return;
        }

        // Simulasi penggantian tag sampel untuk pratinjau
        let previewText = text
            .replace(/{nama}/g, 'Deyar Cipta Rizky')
            .replace(/{no_pendaftaran}/g, 'SPMB/H/2027-2028/I/004')
            .replace(/{no_daftar}/g, 'REG-2026-004')
            .replace(/{username}/g, 'REG-2026-004')
            .replace(/{gelombang}/g, 'Gelombang 1')
            .replace(/{jurusan}/g, 'Pengembangan Perangkat Lunak & Gim (PPLG)')
            .replace(/{asal_sekolah}/g, 'SMP Negeri 1 Bogor')
            .replace(/{total_biaya}/g, 'Rp 3.500.000')
            .replace(/{total_bayar}/g, 'Rp 1.500.000')
            .replace(/{total_dibayar}/g, 'Rp 1.500.000')
            .replace(/{sisa_tagihan}/g, 'Rp 2.000.000')
            .replace(/{sisa_pembayaran}/g, 'Rp 2.000.000')
            .replace(/{status_pembayaran}/g, 'Belum Lunas')
            .replace(/{status_pendaftar}/g, 'DITERIMA')
            .replace(/{nama_sekolah}/g, '{{ $pengaturan->nama_sekolah ?? "SMK Wikrama" }}')
            .replace(/{tipe_aplikasi}/g, '{{ $pengaturan->tipe_aplikasi ?? "PPDB" }}')
            .replace(/{link_login}/g, '{{ route("siswa.login") }}')
            .replace(/{url_sistem}/g, '{{ route("siswa.login") }}')
            .replace(/{no_admin}/g, '{{ $pengaturan->telepon ?? "0812-3456-7890" }}');

        // Format WA (*bold*, _italic_, ~strike~)
        let formatted = previewText
            .replace(/\*(.*?)\*/g, '<strong>$1</strong>')
            .replace(/_(.*?)_/g, '<em>$1</em>')
            .replace(/~(.*?)~/g, '<del>$1</del>');

        waPreviewContent.innerHTML = formatted;
    }

    txtPesanWA.addEventListener('input', updateTextPreview);

    // Sisipkan Tag di Cursor
    document.querySelectorAll('.btn-insert-tag').forEach(btn => {
        btn.addEventListener('click', function () {
            const tag = this.getAttribute('data-tag');
            const startPos = txtPesanWA.selectionStart;
            const endPos = txtPesanWA.selectionEnd;
            const text = txtPesanWA.value;

            txtPesanWA.value = text.substring(0, startPos) + tag + text.substring(endPos, text.length);
            txtPesanWA.focus();
            txtPesanWA.selectionStart = txtPesanWA.selectionEnd = startPos + tag.length;

            updateTextPreview();
        });
    });

    // Quick Template Selector
    selectQuickTemplate.addEventListener('change', function () {
        const val = this.value;
        if (!val) return;

        let content = '';
        if (quickTemplates[val]) {
            content = quickTemplates[val];
        } else {
            const opt = this.options[this.selectedIndex];
            content = opt.getAttribute('data-content') || '';
        }

        if (content) {
            txtPesanWA.value = content;
            updateTextPreview();

            // Otomatis sinkronkan filter bila memilih template khusus
            if (val === 'template_tagihan_belum_lunas') {
                document.getElementById('filter_status_pembayaran').value = 'belum_lunas';
                fetchPreview();
            } else if (val === 'template_pemberitahuan_diterima') {
                document.getElementById('filter_status_pendaftar').value = 'diterima';
                fetchPreview();
            }
        } else if (val === 'custom') {
            txtPesanWA.value = '';
            updateTextPreview();
        }
    });

    // Default template awal (Pengumuman Gelombang)
    selectQuickTemplate.value = 'template_pengumuman_gelombang';
    selectQuickTemplate.dispatchEvent(new Event('change'));

    // Konfirmasi Sebelum Kirim Blast
    btnTriggerBlast.addEventListener('click', function () {
        const pesan = txtPesanWA.value.trim();
        if (!pesan) {
            Swal.fire({
                icon: 'warning',
                title: 'Pesan Kosong',
                text: 'Harap tulis isi pesan WhatsApp sebelum mengirim broadcast.',
                confirmButtonColor: '#696cff'
            });
            return;
        }

        const totalTarget = parseInt(lblTotalTarget.innerText) || 0;
        const totalValid = parseInt(lblTotalValid.innerText) || 0;

        if (totalTarget === 0 || totalValid === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Tidak Ada Penerima',
                text: 'Kombinasi filter yang Anda pilih tidak memiliki siswa dengan nomor WhatsApp valid.',
                confirmButtonColor: '#696cff'
            });
            return;
        }

        const gelombangText = document.getElementById('filter_gelombang').options[document.getElementById('filter_gelombang').selectedIndex].text;
        const statusBayarText = document.getElementById('filter_status_pembayaran').options[document.getElementById('filter_status_pembayaran').selectedIndex].text;

        Swal.fire({
            title: 'Konfirmasi Kirim Broadcast WA',
            html: `
                <div class="text-start small p-3 bg-light rounded border mb-3">
                    <p class="mb-1"><strong>Sasaran Gelombang:</strong> ${gelombangText}</p>
                    <p class="mb-1"><strong>Filter Keuangan:</strong> ${statusBayarText}</p>
                    <p class="mb-1"><strong>Penerima Valid:</strong> <span class="badge bg-success">${totalValid} Siswa / No. WA</span></p>
                    <p class="mb-0 text-muted fs-11">Pesan akan dimasukkan ke antrean Queue dan dikirim bertahap dengan Anti-Ban Delay.</p>
                </div>
                Apakah Anda yakin ingin menjadwalkan pengiriman pesan ini?
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#71dd37',
            cancelButtonColor: '#8592a3',
            confirmButtonText: '<i class="bx bxl-whatsapp me-1"></i> Ya, Jadwalkan Sekarang',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form
                formBlastWA.submit();
            }
        });
    });

});
</script>
@endpush
