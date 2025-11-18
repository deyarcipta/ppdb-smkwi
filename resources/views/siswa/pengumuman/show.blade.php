@extends('siswa.layouts.app')
@section('title', $pengumuman->judul . ' - Pengumuman')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <!-- Konten Utama -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-transparent">
                    <!-- Breadcrumb -->
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('siswa.pengumuman.index') }}">Pengumuman</a>
                            </li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </nav>
                </div>

                <div class="card-body">
                    <!-- Header Pengumuman -->
                    <div class="text-center mb-4">
                        <!-- Tanggal -->
                        <div class="mb-3">
                            <span class="badge bg-primary">
                                <i class="bx bx-calendar me-1"></i>
                                {{ $pengumuman->tanggal->format('d F Y') }}
                            </span>
                        </div>

                        <!-- Judul -->
                        <h1 class="h3 text-primary mb-3">{{ $pengumuman->judul }}</h1>
                    </div>

                    <!-- Gambar -->
                    @if($pengumuman->gambar)
                    <div class="text-center mb-4">
                        <img src="{{ asset('storage/pengumuman/' . $pengumuman->gambar) }}" 
                             alt="{{ $pengumuman->judul }}" 
                             class="img-fluid rounded"
                             style="max-height: 400px; object-fit: contain;">
                    </div>
                    @endif

                    <!-- Isi Pengumuman -->
                    <div class="pengumuman-content">
                        {!! $pengumuman->isi !!}
                    </div>

                    <!-- Tombol Kembali -->
                    <div class="mt-5 pt-4 border-top text-center">
                        <a href="{{ route('siswa.pengumuman.index') }}" class="btn btn-primary">
                            <i class="bx bx-arrow-back me-1"></i>Kembali ke Daftar Pengumuman
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Pengumuman Terkait -->
            @if($pengumumanTerkait->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bx bx-link-external me-2"></i>Pengumuman Lainnya
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($pengumumanTerkait as $item)
                    <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <h6 class="mb-1">
                            <a href="{{ route('siswa.pengumuman.show', $item->id) }}" 
                               class="text-dark text-decoration-none">
                                {{ Str::limit($item->judul, 50) }}
                            </a>
                        </h6>
                        <small class="text-muted">
                            <i class="bx bx-calendar me-1"></i>
                            {{ $item->tanggal->format('d M Y') }}
                        </small>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Info -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bx bx-info-circle me-2"></i>Informasi
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">
                        <i class="bx bx-check-circle me-2 text-success"></i>
                        Pengumuman yang ditampilkan sudah diverifikasi oleh admin
                    </p>
                    <p class="small text-muted mb-2">
                        <i class="bx bx-time me-2 text-warning"></i>
                        Pengumuman diurutkan berdasarkan tanggal terbaru
                    </p>
                    <p class="small text-muted mb-0">
                        <i class="bx bx-search me-2 text-primary"></i>
                        Gunakan fitur pencarian untuk menemukan pengumuman tertentu
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .pengumuman-content {
        line-height: 1.8;
        font-size: 1.1rem;
    }
    
    .pengumuman-content p {
        margin-bottom: 1rem;
    }
    
    .pengumuman-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 1rem 0;
    }
    
    .pengumuman-content h1, 
    .pengumuman-content h2, 
    .pengumuman-content h3, 
    .pengumuman-content h4, 
    .pengumuman-content h5, 
    .pengumuman-content h6 {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        color: #566a7f;
    }
    
    .pengumuman-content ul, 
    .pengumuman-content ol {
        padding-left: 2rem;
        margin-bottom: 1rem;
    }
    
    .pengumuman-content blockquote {
        border-left: 4px solid #667eea;
        padding-left: 1rem;
        margin: 1rem 0;
        font-style: italic;
        color: #6c757d;
    }
    
    .pengumuman-content table {
        width: 100%;
        margin-bottom: 1rem;
        border-collapse: collapse;
    }
    
    .pengumuman-content table th,
    .pengumuman-content table td {
        padding: 0.75rem;
        border: 1px solid #dee2e6;
    }
    
    .pengumuman-content table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
</style>
@endsection