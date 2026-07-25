@php
  $currentYear = date('Y');
  $pengaturan = \App\Models\PengaturanAplikasi::first();
  $namaSekolah = $pengaturan->nama_sekolah ?? 'SMK Wisata Indonesia';
@endphp
<footer class="content-footer footer bg-footer-theme py-3 text-center">
  <div class="container-fluid">
    <div class="footer-text" style="font-size: 0.85rem; color: #697a8d;">
      © {{ $currentYear }} <strong style="color: #566a7f; font-weight: 700;">{{ mb_strtoupper($namaSekolah) }}</strong>. created by <a href="https://wistek.xyz" target="_blank" class="fw-bold" style="color: #696cff; text-decoration: none;">Wistin Teknologi</a>
    </div>
  </div>
</footer>