@extends('layouts.app')

@section('title', 'Persyaratan Pendaftaran')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bx bx-info-circle"></i> Persyaratan Pendaftaran</h4>
                </div>
                <div class="card-body">
                    @php
                        $jadwal = \App\Models\PersyaratanPendaftaran::tipe('jadwal')->aktif()->orderBy('urutan')->get();
                        $umum = \App\Models\PersyaratanPendaftaran::tipe('umum')->aktif()->orderBy('urutan')->get();
                        $dokumen = \App\Models\PersyaratanPendaftaran::tipe('dokumen')->aktif()->orderBy('urutan')->get();
                    @endphp

                    <!-- Jadwal Pendaftaran -->
                    @if($jadwal->count() > 0)
                    <div class="mb-5">
                        <h5 class="text-primary mb-3">Jadwal Pendaftaran</h5>
                        @foreach($jadwal as $item)
                        <div class="mb-4">
                            <h6 class="fw-bold">{{ $item->judul }}</h6>
                            <div class="ms-3">
                                {!! nl2br(e($item->konten)) !!}
                            </div>
                        </div>
                        @endforeach
                        <hr class="my-4">
                    </div>
                    @endif

                    <!-- Persyaratan Umum -->
                    @if($umum->count() > 0)
                    <div class="mb-5">
                        <h5 class="text-primary mb-3">Persyaratan Umum</h5>
                        @foreach($umum as $item)
                        <div class="mb-3">
                            <h6 class="fw-bold">{{ $item->judul }}</h6>
                            <div class="ms-3">
                                {!! nl2br(e($item->konten)) !!}
                            </div>
                        </div>
                        @endforeach
                        <hr class="my-4">
                    </div>
                    @endif

                    <!-- Dokumen yang Diperlukan -->
                    @if($dokumen->count() > 0)
                    <div class="mb-3">
                        <h5 class="text-primary mb-3">Dokumen yang Diperlukan</h5>
                        @foreach($dokumen as $item)
                        <div class="mb-3">
                            <h6 class="fw-bold">{{ $item->judul }}</h6>
                            <div class="ms-3">
                                {!! nl2br(e($item->konten)) !!}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if($jadwal->count() == 0 && $umum->count() == 0 && $dokumen->count() == 0)
                    <div class="text-center text-muted py-4">
                        <i class="bx bx-info-circle display-4"></i>
                        <p class="mt-3">Informasi persyaratan pendaftaran akan segera diumumkan.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection