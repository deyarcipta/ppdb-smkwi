@php
  $currentYear = date('Y');
  $pengaturan = \App\Models\PengaturanAplikasi::first();
  $namaSekolah = $pengaturan->nama_sekolah ?? 'SMK Wisata Indonesia';
@endphp
<footer class="footer">
  <div class="container">
    © {{$currentYear}} <strong>{{$namaSekolah}}</strong>. created by <a href="https://wistek.xyz" target="_blank">Wistin Teknologi</a>  
  </div>
</footer>