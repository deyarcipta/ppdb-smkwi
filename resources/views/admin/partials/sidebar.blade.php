@php
    $pengaturan = \App\Models\PengaturanAplikasi::first();
    $logo = $pengaturan->logo ?? 'sneat/img/logowi.png';
    $namaAplikasi = $pengaturan->nama_aplikasi ?? 'PPDB SMK WI';
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
    <li class="menu-item {{ Request::is('w1s4t4/dashboard') ? 'active' : '' }}">
      <a href="{{route('admin.dashboard')}}" class="menu-link">
        <i class="menu-icon bx bx-home-circle"></i>
        <div>Beranda</div>
      </a>
    </li>

    <!-- Data Master -->
    <li class="menu-item {{ Request::is('w1s4t4/tahun-ajaran*') || Request::is('w1s4t4/jurusan*') || Request::is('w1s4t4/gelombang*') || Request::is('w1s4t4/kuota-jurusan*') || Request::is('w1s4t4/template-pesan*') ? 'open active' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon bx bx-data"></i>
        <div>Data Master</div>
      </a>
      <ul class="menu-sub ps-4">
        <li class="menu-item {{ Request::is('w1s4t4/tahun-ajaran*') ? 'active' : '' }}"><a href="{{ route('tahun-ajaran.index') }}" class="menu-link">Tahun Ajaran</a></li>
        <li class="menu-item {{ Request::is('w1s4t4/jurusan*') ? 'active' : '' }}"><a href="{{ route('jurusan.index') }}" class="menu-link">Jurusan</a></li>
        <li class="menu-item {{ Request::is('w1s4t4/gelombang*') ? 'active' : '' }}"><a href="{{ route('gelombang.index') }}" class="menu-link">Gelombang Pendaftaran</a></li>
        <li class="menu-item {{ Request::is('w1s4t4/kuota-jurusan*') ? 'active' : '' }}"><a href="{{ route('kuota-jurusan.index') }}" class="menu-link">Kuota Per Jurusan</a></li>
        <li class="menu-item {{ Request::is('w1s4t4/template-pesan*') ? 'active' : '' }}"><a href="{{ route('template-pesan.index') }}" class="menu-link">Template Pesan</a></li>
      </ul>
    </li>

    <!-- Data Pendaftar -->
    <li class="menu-item {{ Request::is('w1s4t4/verifikasi-pendaftar*') || Request::is('w1s4t4/data-terverifikasi*') || Request::is('w1s4t4/data-diterima*') || Request::is('w1s4t4/data-ditolak*') ? 'open active' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon bx bx-user"></i>
        <div>Data Pendaftar</div>
      </a>
      <ul class="menu-sub ps-4">
        {{-- <li class="menu-item {{ Request::is('w1s4t4/verifikasi-pendaftar*') ? 'active' : '' }}"><a href="{{ route('verifikasi-pendaftar.index') }}" class="menu-link">Verifikasi Pendaftar</a></li> --}}
        <li class="menu-item {{ Request::is('w1s4t4/data-terverifikasi*') ? 'active' : '' }}"><a href="{{ route('data-terverifikasi.index') }}" class="menu-link">Data Pendaftar</a></li>
        <li class="menu-item {{ Request::is('w1s4t4/data-diterima*') ? 'active' : '' }}"><a href="{{ route('data-diterima.index') }}" class="menu-link">Data Diterima</a></li>
        <li class="menu-item {{ Request::is('w1s4t4/data-ditolak*') ? 'active' : '' }}"><a href="{{ route('data-ditolak.index') }}" class="menu-link">Data Ditolak</a></li>
      </ul>
    </li>

    <!-- Pembayaran -->
    <li class="menu-item {{ Request::is('w1s4t4/master-biaya*') || Request::is('w1s4t4/pembayaran*') || Request::is('w1s4t4/verifikasi-pembayaran*') ? 'open active' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon bx bx-money"></i>
        <div>Pembayaran</div>
      </a>
      <ul class="menu-sub ps-4">
        <li class="menu-item {{ Request::is('w1s4t4/master-biaya*') ? 'active' : '' }}"><a href="{{ route('master-biaya.index') }}" class="menu-link">Mater Biaya</a></li>
        <li class="menu-item {{ Request::is('w1s4t4/verifikasi-pembayaran*') ? 'active' : '' }}"><a href="{{ route('verifikasi-pembayaran.index') }}" class="menu-link">Verifikasi Pembayaran</a></li>
        <li class="menu-item {{ Request::is('w1s4t4/pembayaran*') ? 'active' : '' }}"><a href="{{ route('pembayaran.index') }}" class="menu-link">Data Pembayaran</a></li>
        <li class="menu-item"><a href="{{ route('laporan-pembayaran.index') }}" class="menu-link">Laporan Pembayaran</a></li>
      </ul>
    </li>

    <!-- Data Ujian / Seleksi -->
    {{-- <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon bx bx-edit"></i>
        <div>Data Ujian / Seleksi</div>
      </a>
      <ul class="menu-sub ps-4">
        <li class="menu-item"><a href="#" class="menu-link">Input Nilai Seleksi</a></li>
        <li class="menu-item"><a href="#" class="menu-link">Hasil Seleksi</a></li>
      </ul>
    </li> --}}

    <!-- Data Statistik -->
    <li class="menu-item {{ Request::is('w1s4t4/statistik*') ? 'active' : '' }}">
      <a href="{{ route('statistik.index') }}" class="menu-link">
        <i class="menu-icon bx bx-bar-chart-alt-2"></i>
        <div>Data Statistik</div>
      </a>
    </li>

    <!-- Web / Informasi -->
    <li class="menu-item {{ Request::is('w1s4t4/kontak-pendaftaran*') || Request::is('w1s4t4/info-pembayaran*') || Request::is('w1s4t4/persyaratan-pendaftaran*') || Request::is('w1s4t4/testimoni-alumni*') || Request::is('w1s4t4/faq*') ? 'open active' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon bx bx-globe"></i>
        <div>Web</div>
      </a>
      <ul class="menu-sub ps-4">
        <li class="menu-item {{ Request::is('w1s4t4/kontak-pendaftaran*') ? 'active' : '' }}"><a href="{{ route('kontak-pendaftaran.index') }}" class="menu-link">Kontak Pendaftaran</a></li>
        <li class="menu-item {{ Request::is('w1s4t4/info-pembayaran*') ? 'active' : '' }}"><a href="{{ route('info-pembayaran.index') }}" class="menu-link">Info Pembayaran</a></li>
        <li class="menu-item {{ Request::is('w1s4t4/persyaratan-pendaftaran*') ? 'active' : '' }}"><a href="{{ route('persyaratan-pendaftaran.index') }}" class="menu-link">Info Persyaratan</a></li>
        <li class="menu-item {{ Request::is('w1s4t4/testimoni-alumni*') ? 'active' : '' }}"><a href="{{ route('testimoni-alumni.index') }}" class="menu-link">Testimoni Alumni</a></li>
        <li class="menu-item {{ Request::is('w1s4t4/faq*') ? 'active' : '' }}"><a href="{{ route('faq.index') }}" class="menu-link">FAQ</a></li>
      </ul>
    </li>

    <!-- Pengumuman -->
    <li class="menu-item {{ Request::is('w1s4t4/pengumuman*') ? 'active' : '' }}">
      <a href="{{ route('pengumuman.index') }}" class="menu-link">
        <i class="menu-icon bx bx-broadcast"></i>
        <div>Pengumuman</div>
      </a>
    </li>
    {{-- <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon bx bx-broadcast"></i>
        <div>Pengumuman</div>
      </a>
      <ul class="menu-sub ps-4">
        <li class="menu-item"><a href="#" class="menu-link">Data Pengumuman</a></li>
        <li class="menu-item"><a href="#" class="menu-link">Hasil Kelulusan</a></li>
      </ul>
    </li> --}}

    <!-- Pengaturan -->
    <li class="menu-header small text-uppercase"><span class="menu-header-text">Pengaturan</span></li>

    <li class="menu-item {{ Request::is('w1s4t4/user-management*') ? 'active' : '' }}">
      <a href="{{ route('user-management.index') }}" class="menu-link">
        <i class="menu-icon bx bx-group"></i>
        <div>Manajemen User</div>
      </a>
    </li>

    <li class="menu-item {{ Request::is('w1s4t4/pengaturan-aplikasi*') ? 'active' : '' }}">
      <a href="{{ route('pengaturan-aplikasi.index') }}" class="menu-link">
        <i class="menu-icon bx bx-cog"></i>
        <div>Pengaturan Aplikasi</div>
      </a>
    </li>

  </ul>
</aside>
