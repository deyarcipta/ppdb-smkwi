@php
    $pengaturan = \App\Models\PengaturanAplikasi::first();
    $logo = $pengaturan->logo ?? 'sneat/img/logowi.png';
    $namaAplikasi = $pengaturan->nama_aplikasi ?? 'PPDB SMK WI';
    $userRole = Auth::user()->role ?? 'admin';
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
        <img src="{{ asset($logo) }}" alt="{{ $namaAplikasi }}" />
      </span>
      <span class="app-brand-text demo menu-text fw-bold ms-2">{{ $namaAplikasi }}</span>
    </a>
  </div>

  <ul class="menu-inner py-1">
    <!-- ======== BERANDA & STATISTIK ======== -->
    <li class="menu-item {{ Request::is('panel/dashboard') ? 'active' : '' }}">
      <a href="{{ route('admin.dashboard') }}" class="menu-link">
        <i class="menu-icon bx bx-home-circle"></i>
        <div>Beranda</div>
      </a>
    </li>

    <li class="menu-item {{ Request::is('panel/statistik*') ? 'active' : '' }}">
      <a href="{{ route('statistik.index') }}" class="menu-link">
        <i class="menu-icon bx bx-bar-chart-alt-2"></i>
        <div>Statistik & Analitik</div>
      </a>
    </li>

    {{-- Menu Operasional untuk Admin & Superadmin --}}
    @if(in_array($userRole, ['superadmin', 'admin']))
      <!-- ======== OPERASIONAL PPDB ======== -->
      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Operasional PPDB</span>
      </li>

      <!-- Data Pendaftar -->
      <li class="menu-item {{ Request::is('panel/data-terverifikasi*') || Request::is('panel/data-diterima*') || Request::is('panel/data-ditolak*') || Request::is('panel/data-cetak-kartu*') ? 'open active' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon bx bx-user-check"></i>
          <div>Data Pendaftar</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ Request::is('panel/data-terverifikasi*') ? 'active' : '' }}">
            <a href="{{ route('data-terverifikasi.index') }}" class="menu-link">
              <div>Semua Pendaftar</div>
            </a>
          </li>
          <li class="menu-item {{ Request::is('panel/data-diterima*') ? 'active' : '' }}">
            <a href="{{ route('data-diterima.index') }}" class="menu-link">
              <div>Siswa Diterima</div>
            </a>
          </li>
          <li class="menu-item {{ Request::is('panel/data-ditolak*') ? 'active' : '' }}">
            <a href="{{ route('data-ditolak.index') }}" class="menu-link">
              <div>Siswa Ditolak</div>
            </a>
          </li>
          @if(!empty($pengaturan->enable_cetak_kartu))
          <li class="menu-item {{ Request::is('panel/data-cetak-kartu*') ? 'active' : '' }}">
            <a href="{{ route('data-cetak-kartu.index') }}" class="menu-link">
              <div>Cetak Kartu Ujian</div>
            </a>
          </li>
          @endif
        </ul>
      </li>

      <!-- Transaksi Pembayaran -->
      <li class="menu-item {{ Request::is('panel/pembayaran*') || Request::is('panel/verifikasi-pembayaran*') || Request::is('panel/laporan-pembayaran*') ? 'open active' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon bx bx-credit-card"></i>
          <div>Pembayaran</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ Request::is('panel/verifikasi-pembayaran*') ? 'active' : '' }}">
            <a href="{{ route('verifikasi-pembayaran.index') }}" class="menu-link">
              <div>Verifikasi Pembayaran</div>
            </a>
          </li>
          <li class="menu-item {{ Request::is('panel/pembayaran*') ? 'active' : '' }}">
            <a href="{{ route('pembayaran.index') }}" class="menu-link">
              <div>Riwayat Pembayaran</div>
            </a>
          </li>
          <li class="menu-item {{ Request::is('panel/laporan-pembayaran*') ? 'active' : '' }}">
            <a href="{{ route('laporan-pembayaran.index') }}" class="menu-link">
              <div>Laporan Pembayaran</div>
            </a>
          </li>
        </ul>
      </li>
    @endif

    <!-- ======== KOMUNIKASI & INFORMASI ======== -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Komunikasi & Informasi</span>
    </li>

    <!-- Pengumuman -->
    <li class="menu-item {{ Request::is('panel/pengumuman*') ? 'active' : '' }}">
      <a href="{{ route('pengumuman.index') }}" class="menu-link">
        <i class="menu-icon bx bx-broadcast"></i>
        <div>Pengumuman</div>
      </a>
    </li>

    {{-- WhatsApp Gateway --}}
    @if(!empty($pengaturan->enable_whatsapp))
      <li class="menu-item {{ Request::is('panel/whatsapp*') || Request::is('panel/template-pesan*') ? 'open active' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon bx bxl-whatsapp"></i>
          <div>WhatsApp Gateway</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ Request::is('panel/whatsapp*') ? 'active' : '' }}">
            <a href="{{ route('whatsapp.index') }}" class="menu-link">
              <div>Bot & Log Pesan</div>
            </a>
          </li>
          <li class="menu-item {{ Request::is('panel/template-pesan*') ? 'active' : '' }}">
            <a href="{{ route('template-pesan.index') }}" class="menu-link">
              <div>Template Pesan</div>
            </a>
          </li>
        </ul>
      </li>
    @endif

    {{-- Konten Web PPDB (Khusus Superadmin) --}}
    @if($userRole == 'superadmin')
      <li class="menu-item {{ Request::is('panel/kontak-pendaftaran*') || Request::is('panel/info-pembayaran*') || Request::is('panel/persyaratan-pendaftaran*') || Request::is('panel/testimoni-alumni*') || Request::is('panel/faq*') ? 'open active' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon bx bx-globe"></i>
          <div>Konten Web PPDB</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ Request::is('panel/persyaratan-pendaftaran*') ? 'active' : '' }}">
            <a href="{{ route('persyaratan-pendaftaran.index') }}" class="menu-link">
              <div>Persyaratan & Alur</div>
            </a>
          </li>
          <li class="menu-item {{ Request::is('panel/info-pembayaran*') ? 'active' : '' }}">
            <a href="{{ route('info-pembayaran.index') }}" class="menu-link">
              <div>Info Pembayaran</div>
            </a>
          </li>
          <li class="menu-item {{ Request::is('panel/kontak-pendaftaran*') ? 'active' : '' }}">
            <a href="{{ route('kontak-pendaftaran.index') }}" class="menu-link">
              <div>Kontak Pendaftaran</div>
            </a>
          </li>
          <li class="menu-item {{ Request::is('panel/testimoni-alumni*') ? 'active' : '' }}">
            <a href="{{ route('testimoni-alumni.index') }}" class="menu-link">
              <div>Testimoni Alumni</div>
            </a>
          </li>
          <li class="menu-item {{ Request::is('panel/faq*') ? 'active' : '' }}">
            <a href="{{ route('faq.index') }}" class="menu-link">
              <div>Tanya Jawab (FAQ)</div>
            </a>
          </li>
        </ul>
      </li>
    @endif

    {{-- Pengaturan & Master hanya untuk superadmin --}}
    @if($userRole == 'superadmin')
      <!-- ======== PENGATURAN & MASTER ======== -->
      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Master & Pengaturan</span>
      </li>

      <!-- Data Master -->
      <li class="menu-item {{ Request::is('panel/tahun-ajaran*') || Request::is('panel/jurusan*') || Request::is('panel/gelombang*') || Request::is('panel/kuota-jurusan*') || Request::is('panel/master-biaya*') || Request::is('panel/data-smp*') ? 'open active' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon bx bx-data"></i>
          <div>Data Master</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ Request::is('panel/tahun-ajaran*') ? 'active' : '' }}">
            <a href="{{ route('tahun-ajaran.index') }}" class="menu-link">
              <div>Tahun Ajaran</div>
            </a>
          </li>
          <li class="menu-item {{ Request::is('panel/jurusan*') ? 'active' : '' }}">
            <a href="{{ route('jurusan.index') }}" class="menu-link">
              <div>Jurusan</div>
            </a>
          </li>
          <li class="menu-item {{ Request::is('panel/kuota-jurusan*') ? 'active' : '' }}">
            <a href="{{ route('kuota-jurusan.index') }}" class="menu-link">
              <div>Kuota Jurusan</div>
            </a>
          </li>
          <li class="menu-item {{ Request::is('panel/gelombang*') ? 'active' : '' }}">
            <a href="{{ route('gelombang.index') }}" class="menu-link">
              <div>Gelombang Pendaftaran</div>
            </a>
          </li>
          <li class="menu-item {{ Request::is('panel/master-biaya*') ? 'active' : '' }}">
            <a href="{{ route('master-biaya.index') }}" class="menu-link">
              <div>Master Biaya</div>
            </a>
          </li>
          <li class="menu-item {{ Request::is('panel/data-smp*') ? 'active' : '' }}">
            <a href="{{ route('data-smp.index') }}" class="menu-link">
              <div>Data Asal SMP</div>
            </a>
          </li>
        </ul>
      </li>

      <li class="menu-item {{ Request::is('panel/user-management*') ? 'active' : '' }}">
        <a href="{{ route('user-management.index') }}" class="menu-link">
          <i class="menu-icon bx bx-group"></i>
          <div>Manajemen User</div>
        </a>
      </li>

      <li class="menu-item {{ Request::is('panel/pengaturan-aplikasi*') ? 'active' : '' }}">
        <a href="{{ route('pengaturan-aplikasi.index') }}" class="menu-link">
          <i class="menu-icon bx bx-cog"></i>
          <div>Pengaturan Aplikasi</div>
        </a>
      </li>

      <li class="menu-item {{ Request::is('panel/backup-restore*') ? 'active' : '' }}">
        <a href="{{ route('backup-restore.index') }}" class="menu-link">
          <i class="menu-icon bx bx-hdd"></i>
          <div>Backup & Restore</div>
        </a>
      </li>
    @endif
  </ul>
</aside>
