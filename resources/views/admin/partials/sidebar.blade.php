@php
    $pengaturan = \App\Models\PengaturanAplikasi::first();
    $logo = $pengaturan->logo ?? 'sneat/img/logowi.png';
    $namaAplikasi = $pengaturan->nama_aplikasi ?? 'PPDB SMK WI';
    $userRole = Auth::user()->role ?? 'admin'; // asumsi kolom role
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
        <img src="{{ asset($logo) }}" alt="{{ $namaAplikasi }}" />
      </span>
      <span class="app-brand-text demo menu-text fw-bold ms-2">{{ $namaAplikasi }}</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
  </div>

  <ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item {{ Request::is('panel/dashboard') ? 'active' : '' }}">
      <a href="{{ route('admin.dashboard') }}" class="menu-link">
        <i class="menu-icon bx bx-home-circle"></i>
        <div>Beranda</div>
      </a>
    </li>

    {{-- Menu hanya untuk superadmin --}}
    @if($userRole == 'superadmin')
      <!-- Data Master -->
      <li class="menu-item {{ Request::is('panel/tahun-ajaran*') || Request::is('panel/jurusan*') || Request::is('panel/gelombang*') || Request::is('panel/kuota-jurusan*') || Request::is('panel/master-biaya*') || Request::is('panel/template-pesan*') || Request::is('panel/data-smp*') ? 'open active' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon bx bx-data"></i>
          <div>Data Master</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ Request::is('panel/tahun-ajaran*') ? 'active' : '' }}">
            <a href="{{ route('tahun-ajaran.index') }}" class="menu-link">Tahun Ajaran</a>
          </li>
          <li class="menu-item {{ Request::is('panel/jurusan*') ? 'active' : '' }}">
            <a href="{{ route('jurusan.index') }}" class="menu-link">Jurusan</a>
          </li>
          <li class="menu-item {{ Request::is('panel/gelombang*') ? 'active' : '' }}">
            <a href="{{ route('gelombang.index') }}" class="menu-link">Gelombang Pendaftaran</a>
          </li>
          <li class="menu-item {{ Request::is('panel/kuota-jurusan*') ? 'active' : '' }}">
            <a href="{{ route('kuota-jurusan.index') }}" class="menu-link">Kuota Per Jurusan</a>
          </li>
          <li class="menu-item {{ Request::is('panel/master-biaya*') ? 'active' : '' }}">
            <a href="{{ route('master-biaya.index') }}" class="menu-link">Master Biaya</a>
          </li>
          @if(!empty($pengaturan->enable_whatsapp))
          <li class="menu-item {{ Request::is('panel/template-pesan*') ? 'active' : '' }}">
            <a href="{{ route('template-pesan.index') }}" class="menu-link">Template Pesan</a>
          </li>
          @endif
          <li class="menu-item {{ Request::is('panel/data-smp*') ? 'active' : '' }}">
            <a href="{{ route('data-smp.index') }}" class="menu-link">Data SMP</a>
          </li>
        </ul>
      </li>
    @endif

    {{-- Menu untuk admin & superadmin --}}
    @if(in_array($userRole, ['superadmin','admin']))
      <!-- Data Pendaftar -->
      <li class="menu-item {{ Request::is('panel/verifikasi-pendaftar*') || Request::is('panel/data-terverifikasi*') || Request::is('panel/data-diterima*') || Request::is('panel/data-ditolak*') || Request::is('panel/data-cetak-kartu*') ? 'open active' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon bx bx-user"></i>
          <div>Data Pendaftar</div>
        </a>
        <ul class="menu-sub">
          {{-- <li class="menu-item {{ Request::is('panel/verifikasi-pendaftar*') ? 'active' : '' }}">
            <a href="{{ route('verifikasi-pendaftar.index') }}" class="menu-link">Verifikasi Pendaftar</a>
          </li> --}}
          <li class="menu-item {{ Request::is('panel/data-terverifikasi*') ? 'active' : '' }}">
            <a href="{{ route('data-terverifikasi.index') }}" class="menu-link">Data Pendaftar</a>
          </li>
          <li class="menu-item {{ Request::is('panel/data-diterima*') ? 'active' : '' }}">
            <a href="{{ route('data-diterima.index') }}" class="menu-link">Data Diterima</a>
          </li>
          <li class="menu-item {{ Request::is('panel/data-ditolak*') ? 'active' : '' }}">
            <a href="{{ route('data-ditolak.index') }}" class="menu-link">Data Ditolak</a>
          </li>
          @if(!empty($pengaturan->enable_cetak_kartu))
          <li class="menu-item {{ Request::is('panel/data-cetak-kartu*') ? 'active' : '' }}">
            <a href="{{ route('data-cetak-kartu.index') }}" class="menu-link">Data Cetak Kartu</a>
          </li>
          @endif
        </ul>
      </li>

      <!-- Pembayaran -->
      <li class="menu-item {{ Request::is('panel/pembayaran*') || Request::is('panel/verifikasi-pembayaran*') || Request::is('panel/laporan-pembayaran*') ? 'open active' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon bx bx-money"></i>
          <div>Pembayaran</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ Request::is('panel/verifikasi-pembayaran*') ? 'active' : '' }}">
            <a href="{{ route('verifikasi-pembayaran.index') }}" class="menu-link">Verifikasi Pembayaran</a>
          </li>
          <li class="menu-item {{ Request::is('panel/pembayaran*') ? 'active' : '' }}">
            <a href="{{ route('pembayaran.index') }}" class="menu-link">Data Pembayaran</a>
          </li>
          <li class="menu-item {{ Request::is('panel/laporan-pembayaran*') ? 'active' : '' }}">
            <a href="{{ route('laporan-pembayaran.index') }}" class="menu-link">Laporan Pembayaran</a>
          </li>
        </ul>
      </li>
    @endif


    <!-- Data Statistik -->
    <li class="menu-item {{ Request::is('panel/statistik*') ? 'active' : '' }}">
      <a href="{{ route('statistik.index') }}" class="menu-link">
        <i class="menu-icon bx bx-bar-chart-alt-2"></i>
        <div>Data Statistik</div>
      </a>
    </li>
    @if(in_array($userRole, ['superadmin']))
    <!-- Web / Informasi -->
    <li class="menu-item {{ Request::is('panel/kontak-pendaftaran*') || Request::is('panel/info-pembayaran*') || Request::is('panel/persyaratan-pendaftaran*') || Request::is('panel/testimoni-alumni*') || Request::is('panel/faq*') ? 'open active' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon bx bx-globe"></i>
        <div>Web</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ Request::is('panel/kontak-pendaftaran*') ? 'active' : '' }}">
          <a href="{{ route('kontak-pendaftaran.index') }}" class="menu-link">Kontak Pendaftaran</a>
        </li>
        <li class="menu-item {{ Request::is('panel/info-pembayaran*') ? 'active' : '' }}">
          <a href="{{ route('info-pembayaran.index') }}" class="menu-link">Info Pembayaran</a>
        </li>
        <li class="menu-item {{ Request::is('panel/persyaratan-pendaftaran*') ? 'active' : '' }}">
          <a href="{{ route('persyaratan-pendaftaran.index') }}" class="menu-link">Persyaratan & Alur</a>
        </li>
        <li class="menu-item {{ Request::is('panel/testimoni-alumni*') ? 'active' : '' }}">
          <a href="{{ route('testimoni-alumni.index') }}" class="menu-link">Testimoni Alumni</a>
        </li>
        <li class="menu-item {{ Request::is('panel/faq*') ? 'active' : '' }}">
          <a href="{{ route('faq.index') }}" class="menu-link">FAQ</a>
        </li>
      </ul>
    </li>
    @endif

    <!-- Pengumuman -->
    <li class="menu-item {{ Request::is('panel/pengumuman*') ? 'active' : '' }}">
      <a href="{{ route('pengumuman.index') }}" class="menu-link">
        <i class="menu-icon bx bx-broadcast"></i>
        <div>Pengumuman</div>
      </a>
    </li>

    {{-- WhatsApp Bot --}}
    @if(!empty($pengaturan->enable_whatsapp))
    <li class="menu-item {{ Request::is('panel/whatsapp*') ? 'active' : '' }}">
      <a href="{{ route('whatsapp.index') }}" class="menu-link">
        <i class="menu-icon bx bx-message-square-dots"></i>
        <div>WhatsApp Bot</div>
      </a>
    </li>
    @endif

    {{-- Pengaturan hanya untuk superadmin --}}
    @if($userRole == 'superadmin')
      <li class="menu-header small text-uppercase"><span class="menu-header-text">Pengaturan</span></li>

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
