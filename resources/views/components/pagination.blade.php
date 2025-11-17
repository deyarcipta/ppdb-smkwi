@props(['paginator'])

@php
    // Cek apakah object adalah instance Paginator
    $isPaginator = $paginator instanceof \Illuminate\Pagination\LengthAwarePaginator;
@endphp

@if($isPaginator && $paginator->hasPages())
@php
    // Konfigurasi pagination
    $currentPage = $paginator->currentPage();
    $lastPage = $paginator->lastPage();
    $maxPages = 5; // Maksimal tampil 5 halaman
    
    // Hitung range halaman yang akan ditampilkan
    $startPage = max(1, $currentPage - floor($maxPages / 2));
    $endPage = min($lastPage, $startPage + $maxPages - 1);
    
    // Adjust start page jika end page mencapai batas
    if ($endPage - $startPage + 1 < $maxPages) {
        $startPage = max(1, $endPage - $maxPages + 1);
    }
@endphp

<div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
    <div class="text-muted small">
        Menampilkan {{ $paginator->firstItem() }} sampai {{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
    </div>
    <div class="d-flex gap-2">
        <!-- Previous Button -->
        @if($paginator->onFirstPage())
            <button class="btn btn-outline-secondary btn-sm pagination-btn disabled" disabled>
                <i class="bx bx-chevron-left"></i>
            </button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-outline-primary btn-sm pagination-btn">
                <i class="bx bx-chevron-left"></i>
            </a>
        @endif

        <!-- First Page (jika tidak di halaman 1-3) -->
        @if($startPage > 1)
            <a href="{{ $paginator->url(1) }}" class="btn btn-outline-primary btn-sm pagination-btn">
                1
            </a>
            @if($startPage > 2)
                <button class="btn btn-outline-secondary btn-sm pagination-btn disabled" disabled>
                    ...
                </button>
            @endif
        @endif

        <!-- Page Numbers (maksimal 5 halaman) -->
        @for($page = $startPage; $page <= $endPage; $page++)
            @if($page == $currentPage)
                <button class="btn btn-primary btn-sm pagination-btn active" disabled>
                    {{ $page }}
                </button>
            @else
                <a href="{{ $paginator->url($page) }}" class="btn btn-outline-primary btn-sm pagination-btn">
                    {{ $page }}
                </a>
            @endif
        @endfor

        <!-- Last Page (jika tidak di halaman akhir) -->
        @if($endPage < $lastPage)
            @if($endPage < $lastPage - 1)
                <button class="btn btn-outline-secondary btn-sm pagination-btn disabled" disabled>
                    ...
                </button>
            @endif
            <a href="{{ $paginator->url($lastPage) }}" class="btn btn-outline-primary btn-sm pagination-btn">
                {{ $lastPage }}
            </a>
        @endif

        <!-- Next Button -->
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-outline-primary btn-sm pagination-btn">
                <i class="bx bx-chevron-right"></i>
            </a>
        @else
            <button class="btn btn-outline-secondary btn-sm pagination-btn disabled" disabled>
                <i class="bx bx-chevron-right"></i>
            </button>
        @endif
    </div>
</div>

@elseif($isPaginator)
<!-- Tampilkan info untuk data tanpa pagination (hanya 1 halaman) -->
<div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
    <div class="text-muted small">
        Menampilkan {{ $paginator->count() }} dari {{ $paginator->count() }} data
    </div>
</div>

@else
<!-- Jika bukan Paginator (Collection biasa), tampilkan info sederhana -->
<div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
    <div class="text-muted small">
        Menampilkan {{ $paginator->count() }} data
    </div>
</div>
@endif