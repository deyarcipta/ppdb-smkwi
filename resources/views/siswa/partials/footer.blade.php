@php
  $currentYear = date('Y');
  $pengaturan = \App\Models\PengaturanAplikasi::first();
  $namaSekolah = $pengaturan->nama_sekolah ?? 'SMK Wisata Indonesia';
  $tipeAplikasi = $pengaturan->tipe_aplikasi ?? 'PPDB';
  $ta = $pengaturan ? $pengaturan->tahunAjaranAktif : null;
@endphp
<footer class="footer">
  <div class="container text-center text-muted" style="font-size: 0.85rem;">
    © {{$currentYear}} <strong>{{$namaSekolah}}</strong> - Portal {{ $tipeAplikasi }}{{ $ta ? ' (T.A '.$ta->nama.')' : '' }}. created by <a href="https://wistek.xyz" target="_blank">Wistin Teknologi</a>  
  </div>
</footer>