<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="{{ asset($pengaturan->favicon ?? 'sneat/img/logowi.png') }}" type="image/png">
  <title>@yield('title', 'Website PPDB SMK Wisata Indonesia')</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <!-- Core Styles -->
  <link rel="stylesheet" href="{{ asset('sneat/css/bootstrap.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('sneat/css/aos.css') }}" />
  <link rel="stylesheet" href="{{ asset('sneat/css/swiper-bundle.min.css') }}" />

  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('sneat/css/frontend-style.css') }}">
  @stack('styles')
</head>

<body>
<header class="navbar-header">

<!-- Info Bar dengan Data Real-time -->
<div class="info-bar text-white">
    <div class="marquee-container">
        <div class="marquee-content">
            <p>
                INFORMASI !!! 
                @if($gelombangAktif->isNotEmpty())
                    @foreach($gelombangAktif as $gelombang)
                        {{ $gelombang->nama_gelombang }} Dibuka dari tanggal 
                        {{ $gelombang->tanggal_mulai->translatedFormat('d F Y') }} - 
                        {{ $gelombang->tanggal_selesai->translatedFormat('d F Y') }}
                        ({{ $gelombang->tanggal_selesai->diffInDays(now()) }} hari lagi)
                    @endforeach
                @else
                    Pendaftaran akan segera dibuka. Stay tuned!
                @endif
            </p>
        </div>
    </div>
</div>

<nav class="navbar navbar-expand-lg navbar-purple fixed-top">
  <div class="container d-flex justify-content-between align-items-center">
    <!-- Logo -->
    <a class="navbar-brand d-flex align-items-center" href="/">
      <img src="{{ asset($pengaturan->logo ?? 'sneat/img/logowi.png') }}" alt="Logo" height="40" width="40">
      <span class="ms-2 fw-bold text-white">{{$pengaturan->nama_aplikasi ?? 'PPDB SMK WI'}}</span>
    </a>

    <!-- Toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menu (Semua masuk collapse) -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav mx-auto text-center">
        <li class="nav-item"><a class="nav-link text-white" href="/">Beranda</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="#informasi">Informasi</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="#alur-pendaftaran">Alur Pendaftaran</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="#hubungi-kami">Hubungi Kami</a></li>
      </ul>

      <ul class="navbar-nav d-flex align-items-center mt-3 mt-lg-0">
        <li class="nav-item"><a class="nav-link text-white" href="{{route('siswa.login')}}">Login</a></li>
        <li class="nav-item">
          <a href="{{ route('frontend.pendaftaran') }}" class="btn btn-ppdb ms-0 ms-lg-2">Pendaftaran</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
</header>


<section class="hero-ppdb">
  <div class="container hero-content">
    <div class="hero-text">
      <h1>PPDB TA 2026-2027<br><span class="school-name">SMK WISATA INDONESIA</span></h1>
      <p>Ayo! segera daftarkan dirimu ke SMK Wistin dengan cara klik <strong>PENDAFTARAN PPDB</strong> dibawah ini!<br>
      <span class="motto">Kreatif, Unggul dan Berakhlak Mulia.</span></p>
      <a href="{{ route('frontend.pendaftaran') }}" class="btn-ppdb">PENDAFTARAN PPDB</a>
    </div>
  </div>
</section>

<section id="alasan" class="pd-top-50 pd-bottom-50">
  <div class="container">
    <div class="row">
      <div class="col-md-3 mb-4">
        <div class="card h-100 custom-card text-center">
          <img src="{{ asset('sneat/img/akreditasi.png') }}" class="card-img-top mx-auto d-block" alt="Akreditasi A" style="width: 80px; height: 80px; object-fit: contain;">
          <div class="card-body">
            <h5 class="card-title">Akreditasi A</h5>
            <p class="card-text">Terakreditasi A untuk semua program keahlian</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="card h-100 custom-card text-center">
          <img src="{{ asset('sneat/img/fasilitas.png') }}" class="card-img-top mx-auto d-block" alt="Fasilitas Lengkap" style="width: 80px; height: 80px; object-fit: contain;">
          <div class="card-body">
            <h5 class="card-title">Fasilitas Lengkap</h5>
            <p class="card-text">Sarana dan prasarana yang mendukung pembelajaran</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="card h-100 custom-card text-center">
          <img src="{{ asset('sneat/img/guru.png') }}" class="card-img-top mx-auto d-block" alt="Guru Profesional" style="width: 80px; height: 80px; object-fit: contain;">
          <div class="card-body">
            <h5 class="card-title">Guru Profesional</h5>
            <p class="card-text">Tenaga pengajar yang <i>up-to-date</i>, berpengalaman dan tersertifikasi</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="card h-100 custom-card text-center">
          <img src="{{ asset('sneat/img/lingkungan.png') }}" class="card-img-top mx-auto d-block" alt="Lingkungan Nyaman" style="width: 80px; height: 80px; object-fit: contain;">
          <div class="card-body">
            <h5 class="card-title">Lingkungan Nyaman</h5>
            <p class="card-text">Lingkungan yang nyaman dalam proses pembelajaran</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="programs-card">
  <div class="container">
    <div class="section-title text-center">
      <h2>Program Keahlian</h2>
      <h1>Konsesntrasi Keahlian</h1>
      <hr class="mx-auto">
    </div>
    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="card h-100 custom-card text-center">
          <img src="{{ asset('sneat/img/kuliner.png') }}" class="card-img-top mx-auto d-block" alt="Kuliner">
          <div class="card-body">
            <h5 class="card-title">Kuliner</h5>
            <p>Jurusan Kuliner mempelajari teknik memasak, penyajian makanan, manajemen dapur, dan keamanan pangan untuk mencetak chef profesional di industri boga dan restoran.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card h-100 custom-card text-center">
          <img src="{{ asset('sneat/img/perhotelan.png') }}" class="card-img-top mx-auto d-block" alt="Perhotelan">
          <div class="card-body">
            <h5 class="card-title">Perhotelan</h5>
            <p>Jurusan Perhotelan mempelajari pelayanan tamu, manajemen hotel, tata boga, tata graha, dan komunikasi, menyiapkan siswa bekerja profesional di industri pariwisata dan perhotelan.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card h-100 custom-card text-center">
          <img src="{{ asset('sneat/img/tkj.png') }}" class="card-img-top mx-auto d-block" alt="Teknik Komputer dan Jaringan">
          <div class="card-body">
            <h5 class="card-title">Teknik Komputer dan Jaringan</h5>
            <p>Jurusan TJKT (Teknik Jaringan Komputer dan Telekomunikasi) mempelajari instalasi, konfigurasi, pemeliharaan jaringan komputer serta sistem komunikasi untuk mendukung konektivitas digital modern.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Section Informasi -->
<section id="informasi" class="pd-top-80 pd-bottom-80 bg-light">
  <div class="container">
    <div class="section-title text-center mb-5">
      <h2>Informasi PPDB</h2>
      <h1>Jadwal & Persyaratan</h1>
      <hr class="mx-auto">
    </div>

    <div class="row">
      <!-- Jadwal Pendaftaran -->
      <div class="col-lg-6 mb-4">
        <div class="card custom-card h-100">
          <div class="card-header bg-primary text-white text-center py-3">
            <h4 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Jadwal Pendaftaran</h4>
          </div>
          <div class="card-body">
            @if($jadwalPendaftaran->count() > 0)
              <div class="timeline">
                @foreach($jadwalPendaftaran as $index => $jadwal)
                  <div class="timeline-item">
                    {{-- <div class="timeline-date">Gelombang {{ $index + 1 }}</div> --}}
                    <div class="timeline-date">{{ $jadwal->judul }}</div>
                    <div class="timeline-content">
                      {{-- <h5>{{ $jadwal->judul }}</h5> --}}
                      <div class="text-start">
                        {!! nl2br(e($jadwal->konten)) !!}
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div class="text-center text-muted py-4">
                <i class="bi bi-calendar-x display-4"></i>
                <p class="mt-3">Jadwal pendaftaran akan segera diumumkan.</p>
              </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Persyaratan -->
      <div class="col-lg-6 mb-4">
        <div class="card custom-card h-100">
          <div class="card-header bg-success text-white text-center py-3">
            <h4 class="mb-0"><i class="bi bi-file-text me-2"></i>Persyaratan</h4>
          </div>
          <div class="card-body">
            @if($persyaratanUmum->count() > 0 || $dokumenPersyaratan->count() > 0)
              <div class="requirements-list">
                <!-- Persyaratan Umum -->
                @foreach($persyaratanUmum as $umum)
                  <div class="requirement-item">
                    <div class="requirement-icon">
                      <i class="bi bi-person-check"></i>
                    </div>
                    <div class="requirement-content">
                      <h5>{{ $umum->judul }}</h5>
                      <div class="text-start">
                        {!! nl2br(e($umum->konten)) !!}
                      </div>
                    </div>
                  </div>
                @endforeach

                <!-- Dokumen Persyaratan -->
                @foreach($dokumenPersyaratan as $dokumen)
                  <div class="requirement-item">
                    <div class="requirement-icon">
                      <i class="bi bi-folder"></i>
                    </div>
                    <div class="requirement-content">
                      <h5>{{ $dokumen->judul }}</h5>
                      <div class="text-start">
                        {!! nl2br(e($dokumen->konten)) !!}
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div class="text-center text-muted py-4">
                <i class="bi bi-file-earmark-x display-4"></i>
                <p class="mt-3">Persyaratan pendaftaran akan segera diumumkan.</p>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Section Alur Pendaftaran -->
<section id="alur-pendaftaran" class="pd-top-80 pd-bottom-80">
  <div class="container">
    <div class="section-title text-center mb-5">
      <h2>Alur Pendaftaran</h2>
      <h1>Cara Mendaftar</h1>
      <hr class="mx-auto">
    </div>

    <div class="registration-steps">
      <div class="step-item">
        <div class="step-number">1</div>
        <div class="step-content">
          <h4>Registrasi Awal</h4>
          <p>Siswa melakukan registrasi awal di website PPDB</p>
          <div class="step-details">
            <ul>
              <li>Klik tombol "Pendaftaran PPDB"</li>
              <li>Isi data dasar (nama, email, no. telepon)</li>
              <li>Submit formulir registrasi</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="step-item">
        <div class="step-number">2</div>
        <div class="step-content">
          <h4>Menerima Akses Login</h4>
          <p>Siswa akan mendapatkan username dan password melalui WhatsApp</p>
          <div class="step-details">
            <ul>
              <li>Username dan password dikirim via WhatsApp</li>
              <li>Informasi lengkap cara login</li>
              <li>Simpan baik-baik informasi login</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="step-item">
        <div class="step-number">3</div>
        <div class="step-content">
          <h4>Login & Pembayaran Formulir</h4>
          <p>Siswa login dan melakukan pembayaran formulir pendaftaran</p>
          <div class="step-details">
            <ul>
              <li>Login dengan username/password yang diterima</li>
              <li>Lakukan pembayaran biaya formulir</li>
              <li>Upload bukti pembayaran formulir</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="step-item">
        <div class="step-number">4</div>
        <div class="step-content">
          <h4>Pengisian Formulir Lengkap</h4>
          <p>Siswa mengisi formulir pendaftaran secara lengkap</p>
          <div class="step-details">
            <ul>
              <li>Data pribadi lengkap</li>
              <li>Data orang tua/wali</li>
              <li>Data pendidikan sebelumnya</li>
              <li>Pilihan program keahlian</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="step-item">
        <div class="step-number">5</div>
        <div class="step-content">
          <h4>Pembayaran PPDB</h4>
          <p>Siswa melakukan pembayaran biaya PPDB</p>
          <div class="step-details">
            <ul>
              <li>Pembayaran dapat dilakukan melalui transfer ataupun cash</li>
              <li>Upload bukti pembayaran PPDB</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="step-item">
        <div class="step-number">6</div>
        <div class="step-content">
          <h4>Verifikasi & Pengumuman</h4>
          <p>Menunggu proses verifikasi oleh panitia PPDB</p>
          <div class="step-details">
            <ul>
              <li>Proses verifikasi 1-3 hari kerja</li>
              <li>Notifikasi hasil via WhatsApp/Email</li>
              <li>Dinyatakan diterima sebagai siswa</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="step-item">
        <div class="step-number">7</div>
        <div class="step-content">
          <h4>Daftar Ulang</h4>
          <p>Siswa melakukan daftar ulang dengan membawa dokumen</p>
          <div class="step-details">
            <ul>
              <li>Bawa dokumen asli untuk verifikasi</li>
              <li>Konfirmasi kehadiran di sekolah</li>
              {{-- <li>Pengambilan seragam dan kelengkapan</li>
              <li>Foto untuk kartu siswa</li> --}}
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA Full Width Section -->
<section class="cta-full-width">
  <div class="container">
    <div class="cta-content">
      <h3>Siap Bergabung Dengan Kami?</h3>
      <p>Jangan lewatkan kesempatan untuk menjadi bagian dari SMK Wisata Indonesia</p>
      <a href="{{ route('frontend.pendaftaran') }}" class="btn cta-btn">
        <i class="bi bi-pencil-square me-2"></i>Daftar Sekarang
      </a>
    </div>
  </div>
</section>

<section id="testimoni" class="pd-top-80 pd-bottom-80 bg-light">
  <div class="container">
    <div class="section-title text-center mb-5">
      <h2>Testimoni Alumni</h2>
      <h1>Apa Kata Mereka?</h1>
      <hr class="mx-auto">
    </div>
    
    @if($testimoniAlumni->count() > 0)
      <div class="swiper alumniSwiper">
        <div class="swiper-wrapper">

          @foreach($testimoniAlumni as $testimoni)
          <!-- Slide -->
          <div class="swiper-slide">
            <div class="row align-items-center">
              <!-- Kiri: Text -->
              <div class="col-md-7 textAlumni">
                <h4 class="fw-bold mb-3">{{ $testimoni->headline }}</h4>
                <p class="mb-3">
                  {{ $testimoni->testimoni }}
                </p>
                <p class="fw-medium">
                  {{ $testimoni->nama_alumni }}<br>
                  <small>Alumni Jurusan {{ $testimoni->jurusan }}
                    @if($testimoni->pekerjaan)
                      • {{ $testimoni->pekerjaan }}
                    @endif
                  </small>
                </p>
              </div>
              <!-- Kanan: Gambar -->
              <div class="col-md-5 d-flex align-items-center justify-content-center">
                @if($testimoni->foto)
                  <img src="{{ Storage::url('public/testimoni-alumni/' . $testimoni->foto) }}" 
                       alt="{{ $testimoni->nama_alumni }}"
                       class="img-fluid rounded-4 shadow" 
                       style="max-height: 300px; object-fit: cover;">
                @else
                  <!-- Fallback image jika tidak ada foto -->
                  <div class="bg-primary rounded-4 shadow d-flex align-items-center justify-content-center text-white" 
                       style="width: 100%; height: 300px;">
                    <div class="text-center">
                      <i class="bx bx-user display-4 mb-2"></i>
                      <p class="mb-0">{{ $testimoni->nama_alumni }}</p>
                    </div>
                  </div>
                @endif
              </div>
            </div>
          </div>
          @endforeach

        </div>

        <!-- Pagination -->
        <div class="swiper-pagination"></div>
        
        <!-- Navigation -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
      </div>
    @else
    <!-- Pesan ketika tidak ada data testimoni -->
    <div class="text-center py-5">
      <i class="bx bx-comment-dots display-1 text-muted mb-3"></i>
      <h4 class="text-muted mb-3">Testimoni Segera Hadir</h4>
      <p class="text-muted mb-0">Pengalaman dan kesan alumni akan ditampilkan di sini.</p>
    </div>
  @endif
  </div>
</section>

<!-- Section Hubungi Kami -->
<section id="hubungi-kami" class="pd-top-80 pd-bottom-80 bg-light">
  <div class="container">
    <div class="section-title text-center mb-5">
      <h2>Hubungi Kami</h2>
      <h1>Butuh Bantuan?</h1>
      <hr class="mx-auto">
    </div>

    <div class="row justify-content-center">
      <!-- Informasi Kontak -->
      <div class="col-lg-8">
        <div class="card custom-card">
          <div class="card-body">
            <div class="row text-center">
              <div class="col-md-6 mb-4">
                <div class="contact-item">
                  <div class="contact-icon bg-primary text-white rounded-circle mx-auto mb-3">
                    <i class="bi bi-geo-alt"></i>
                  </div>
                  <h5>Alamat Sekolah</h5>
                  <p>{{$pengaturan->alamat}}</p>
                </div>
              </div>

                  <div class="col-md-6 mb-4">
                    <div class="contact-item">
                      <div class="contact-icon bg-success text-white rounded-circle mx-auto mb-3">
                        <i class="bi bi-telephone"></i>
                      </div>
                      <h5>Telepon</h5>
                      @if($kontakPendaftaran->count() > 0)
                        @foreach($kontakPendaftaran as $kontak)
                        <p>{{ $kontak->no_kontak }} - {{ $kontak->nama_kontak }}</p>
                        @endforeach
                        @else
                        <h5>Telepon</h5>
                        <p>(021) 123123123<br>081203412312</p>
                        @endif
                    </div>
                  </div>

              <div class="col-md-6 mb-4">
                <div class="contact-item">
                  <div class="contact-icon bg-info text-white rounded-circle mx-auto mb-3">
                    <i class="bi bi-envelope"></i>
                  </div>
                  <h5>Email</h5>
                  <p>{{$pengaturan->email}}</p>
                </div>
              </div>

              <div class="col-md-6 mb-4">
                <div class="contact-item">
                  <div class="contact-icon bg-warning text-white rounded-circle mx-auto mb-3">
                    <i class="bi bi-clock"></i>
                  </div>
                  <h5>Jam Operasional</h5>
                  <p>Senin - Jumat: 08.00 - 16.00 WIB<br>
                  Sabtu: 08.00 - 14.00 WIB</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- FAQ Section -->
    <div class="row mt-5">
      <div class="col-12">
        <div class="card custom-card">
          <div class="card-header bg-info text-white text-center py-3">
            <h4 class="mb-0"><i class="bi bi-question-circle me-2"></i>Pertanyaan Umum (FAQ)</h4>
          </div>
          <div class="card-body">

            @if($faqs->count() > 0)
              <div class="accordion" id="faqAccordion">
                @foreach($faqs as $index => $faq)
                  <div class="accordion-item">
                    <h2 class="accordion-header">
                      <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" 
                              type="button" 
                              data-bs-toggle="collapse" 
                              data-bs-target="#faq{{ $faq->id }}">
                        {{ $faq->pertanyaan }}
                      </button>
                    </h2>
                    <div id="faq{{ $faq->id }}" 
                        class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                        data-bs-parent="#faqAccordion">
                      <div class="accordion-body">
                        {!! nl2br(e($faq->jawaban)) !!}
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <!-- Tampilan ketika FAQ belum ada -->
              <div class="text-center py-4">
                <div class="text-muted">
                  <i class="bi bi-question-circle display-4 mb-3"></i>
                  <h5 class="text-muted">Belum Ada Pertanyaan</h5>
                  <p class="mb-0">Pertanyaan umum akan segera ditambahkan. Silakan hubungi kami jika ada pertanyaan.</p>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<footer class="bg-white border-top mt-5 py-4">
  <div class="container footer-container">
    <div class="row text-start align-items-start">

      {{-- Kolom 1: Logo & Kontak --}}
      <div class="col-md-5 mb-4">
        <div class="mb-2">
          <img src="{{ asset($pengaturan->logo) }}" alt="Logo SMK" width="100" class="img-fluid">
        </div>
        <h3 class="fw-bold">{{$pengaturan->nama_sekolah}}</h3>
        <p class="mb-1">{{$pengaturan->alamat}}</p>
        <p class="mb-1">
          <i class="bi bi-envelope-fill me-1"></i> {{$pengaturan->email}}
        </p>
        <p class="mb-1">
          <i class="bi bi-telephone-fill me-1"></i> {{$pengaturan->telepon}}
        </p>
        @if(!$pengaturan->no_hp)
        @else
        <p class="mb-0">
          <i class="bi bi-phone-fill me-1"></i> {{$pengaturan->no_hp}}
        </p>
        @endif
      </div>

      {{-- Kolom 2: Menu --}}
      <div class="col-md-3 mb-4">
        <h4 class="fw-bold">Menu Utama</h4>
        <ul class="list-unstyled">
          <li><a href="#" class="text-dark text-decoration-none">Beranda</a></li>
          <li><a href="#programs-card" class="text-dark text-decoration-none">Program Keahlian</a></li>
          <li><a href="#informasi" class="text-dark text-decoration-none">Informasi</a></li>
          <li><a href="#alur-pendaftaran" class="text-dark text-decoration-none">Alur Pendaftaran</a></li>
          <li><a href="#testimoni" class="text-dark text-decoration-none">Alumni</a></li>
          <li><a href="#hubungi-kami" class="text-dark text-decoration-none">Hubungi Kami</a></li>
        </ul>
        <h4 class="fw-bold mt-3">Aplikasi Siswa</h4>
        <ul class="list-unstyled">
          <li><a href="#" class="text-dark text-decoration-none">Siawi</a></li>
          <li><a href="#" class="text-dark text-decoration-none">Sistem Alumni</a></li>
        </ul>
      </div>

      {{-- Kolom 3: Statistik & Sosial Media --}}
      <div class="col-md-4 mb-4">
          <h4 class="fw-bold">Statistik</h4>
          <h3 class="mb-1"><strong>Website PPDB Wistin</strong></h3>
          <p class="mb-1">Pageview Hari Ini : {{ $statistikVisitor['hari_ini']['pageviews'] }}</p>
          <p class="mb-1">Visitor Hari Ini : {{ $statistikVisitor['hari_ini']['visitors'] }}</p>
          <p class="mb-1">Visitor Bulan Ini : {{ $statistikVisitor['bulan_ini']['visitors'] }}</p>
          <p class="mb-3">Total Visitor : {{ $statistikVisitor['total']['pageviews'] }}</p>

          <h4 class="fw-bold">Our Social Media</h4>
          <div class="d-flex gap-2 flex-wrap">
              <a href="{{$pengaturan->instagram}}"><img src="{{ asset('sneat/img/instagram.png') }}" width="50" alt="Instagram"></a>
              <a href="{{$pengaturan->youtube}}"><img src="{{ asset('sneat/img/youtube.png') }}" width="50" alt="YouTube"></a>
              <a href="{{$pengaturan->facebook}}"><img src="{{ asset('sneat/img/facebook.png') }}" width="50" alt="Facebook"></a>
              <a href="{{$pengaturan->tiktok}}"><img src="{{ asset('sneat/img/tiktok.png') }}" width="50" alt="Tiktok"></a>
          </div>
      </div>
    </div>
  </div>
</footer>

<footer class="footer-section text-center py-3">
  <div class="container">
    <span>
      &copy; {{ date('Y') }} <strong>SMK Wisata Indonesia</strong> |
      Website ini dibuat oleh <strong class="text-purple">Jurusan TJKT</strong> 
      untuk mendukung <em>Digitalisasi Sekolah</em>.
    </span>
  </div>
</footer>

  <!-- Scripts -->
  <script src="{{ asset('sneat/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('sneat/js/aos.js') }}"></script>
  <script src="{{ asset('sneat/js/swiper-bundle.min.js') }}"></script>

<script>
// Efek Navbar saat Scroll - PERBAIKI ID NAVBAR
document.addEventListener("scroll", function () {
  const navbar = document.querySelector(".navbar"); // Ubah dari mainNavbar ke .navbar
  if (navbar) {
    if (window.scrollY > 50) {
      navbar.classList.add("navbar-scrolled");
    } else {
      navbar.classList.remove("navbar-scrolled");
    }
  }
});

// HAPUS DOMContentLoaded yang duplikat, gunakan satu saja
document.addEventListener('DOMContentLoaded', function () {
  
  // Fix dropdown agar tidak langsung tertutup - SESUAIKAN DENGAN STRUKTUR HTML
  const dropdowns = document.querySelectorAll('.nav-item.dropdown');
  const toggles = document.querySelectorAll('.dropdown-toggle');

  // ======== DESKTOP (Hover) ========
  function handleDesktopDropdown() {
    dropdowns.forEach(item => {
      item.addEventListener('mouseenter', () => {
        const dropdown = item.querySelector('.dropdown-menu');
        if (dropdown) dropdown.classList.add('show');
      });
      item.addEventListener('mouseleave', () => {
        const dropdown = item.querySelector('.dropdown-menu');
        setTimeout(() => {
          if (dropdown && !item.matches(':hover')) {
            dropdown.classList.remove('show');
          }
        }, 150);
      });
    });
  }

  // ======== MOBILE (Click) ========
  function handleMobileDropdown() {
    toggles.forEach(toggle => {
      toggle.addEventListener('click', function (e) {
        if (window.innerWidth < 992) {
          e.preventDefault();
          e.stopPropagation();

          const parent = this.closest('.dropdown');
          if (parent) {
            const isActive = parent.classList.contains('show');

            // Tutup semua dropdown lain
            document.querySelectorAll('.dropdown.show').forEach(d => {
              if (d !== parent) d.classList.remove('show');
            });

            // Toggle dropdown ini
            if (!isActive) {
              parent.classList.add('show');
            } else {
              parent.classList.remove('show');
            }
          }
        }
      });
    });
  }

  // ======== Jalankan sesuai ukuran layar ========
  if (window.innerWidth >= 992) {
    handleDesktopDropdown();
  } else {
    handleMobileDropdown();
  }

  // ======== Update saat resize (ganti mode otomatis) ========
  window.addEventListener('resize', () => {
    // Hapus semua state dropdown saat mode berubah
    document.querySelectorAll('.dropdown.show').forEach(d => d.classList.remove('show'));
    
    // Re-initialize dropdown handlers berdasarkan ukuran layar
    if (window.innerWidth >= 992) {
      handleDesktopDropdown();
    } else {
      handleMobileDropdown();
    }
  });

  // Smooth scroll untuk navigasi
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      // Skip untuk href="#" saja
      if (href === '#') return;
      
      e.preventDefault();
      const target = document.querySelector(href);
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
        
        // Close mobile navbar setelah klik (jika mobile)
        if (window.innerWidth < 992) {
          const navbarCollapse = document.querySelector('.navbar-collapse');
          if (navbarCollapse && navbarCollapse.classList.contains('show')) {
            const bsCollapse = new bootstrap.Collapse(navbarCollapse);
            bsCollapse.hide();
          }
        }
      }
    });
  });

  // Inisialisasi Swiper untuk Testimoni - TAMBAH PENUNDAAN UNTUK MEMASTIKAN SWIPER LOAD
  function initSwiper() {
    const swiperEl = document.querySelector('.alumniSwiper');
    if (!swiperEl) {
      console.warn('Swiper element not found');
      return;
    }

    if (typeof Swiper === 'undefined') {
      console.warn('Swiper library not loaded, retrying...');
      setTimeout(initSwiper, 500);
      return;
    }

    const alumniSwiper = new Swiper('.alumniSwiper', {
      loop: true,
      speed: 600,
      autoplay: {
          delay: 5000,
          disableOnInteraction: false,
      },
      pagination: {
          el: '.swiper-pagination',
          clickable: true,
          dynamicBullets: true,
      },
      navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
      },
      breakpoints: {
          320: {
              slidesPerView: 1,
              spaceBetween: 20
          },
          768: {
              slidesPerView: 1,
              spaceBetween: 30
          },
          1024: {
              slidesPerView: 1,
              spaceBetween: 40
          }
      },
      effect: 'slide',
      keyboard: {
          enabled: true,
      },
      mousewheel: {
          forceToAxis: true,
      }
  });
  }

  // Tunggu sedikit untuk memastikan semua resource terload
  setTimeout(initSwiper, 100);

});

// Fallback untuk AOS (jika digunakan)
if (typeof AOS !== 'undefined') {
  AOS.init({
    duration: 800,
    once: true,
    offset: 100
  });
}
</script>

  @stack('scripts')
</body>
</html>