@extends('siswa.layouts.app')
@section('title', 'Pengumuman PPDB')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bx bxs-megaphone me-2"></i>Pengumuman
                    </h5>
                    <div class="d-flex">
                        <!-- Form Pencarian -->
                        <form action="{{ route('siswa.pengumuman.search') }}" method="GET" class="me-2">
                            <div class="input-group input-group-merge">
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Cari pengumuman..." 
                                       value="{{ request('search') }}">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="bx bx-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Pengumuman -->
    <div class="row">
        @if($pengumuman->count() > 0)
            @foreach($pengumuman as $item)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <!-- Gambar Pengumuman -->
                    @if($item->gambar)
                    <img class="card-img-top" 
                         src="{{ asset('storage/pengumuman/' . $item->gambar) }}" 
                         alt="{{ $item->judul }}"
                         style="height: 200px; object-fit: cover;">
                    @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                         style="height: 200px;">
                        <i class="bx bx-bullhorn display-4 text-muted"></i>
                    </div>
                    @endif

                    <div class="card-body">
                        <!-- Badge Tanggal -->
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="bx bx-calendar me-1"></i>
                                {{ $item->tanggal->format('d M Y') }}
                            </small>
                        </div>

                        <!-- Judul -->
                        <h5 class="card-title text-primary">{{ Str::limit($item->judul, 50) }}</h5>

                        <!-- Isi Ringkas -->
                        <p class="card-text text-muted">
                            {{ Str::limit(strip_tags($item->isi), 100) }}
                        </p>
                    </div>

                    <div class="card-footer bg-transparent">
                        <a href="{{ route('siswa.pengumuman.show', $item->id) }}" 
                           class="btn btn-primary btn-sm">
                            <i class="bx bx-show me-1"></i>Baca Selengkapnya
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-bullhorn display-1 text-muted mb-3"></i>
                        <h5 class="text-muted">
                            @if(request('search'))
                                Tidak ada pengumuman yang sesuai dengan pencarian "{{ request('search') }}"
                            @else
                                Belum ada pengumuman tersedia
                            @endif
                        </h5>
                        @if(request('search'))
                            <a href="{{ route('siswa.pengumuman.index') }}" class="btn btn-primary mt-3">
                                <i class="bx bx-arrow-back me-1"></i>Kembali ke Semua Pengumuman
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($pengumuman->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{ $pengumuman->links() }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('styles')
<style>
    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border: 1px solid #e3e3e3;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .card-title {
        font-weight: 600;
        line-height: 1.4;
    }
    
    .card-text {
        line-height: 1.6;
    }
</style>
@endsection