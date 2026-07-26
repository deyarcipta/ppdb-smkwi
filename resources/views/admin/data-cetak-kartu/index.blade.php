@extends('admin.layouts.app')
@section('title', 'Data Cetak Kartu Peserta')

@section('content')
<div class="container-fluid p-0">
    <div class="card card-full-width">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-1"><i class="bx bx-id-card me-2 text-warning"></i>Data Cetak Kartu Peserta</h5>
                <small class="text-muted">Menampilkan daftar akun dan informasi cetak kartu peserta pendaftaran PPDB.</small>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <form action="{{ route('data-cetak-kartu.index') }}" method="GET" class="d-flex gap-2">
                    <select name="jurusan_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusanList as $j)
                            <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
                        @endforeach
                    </select>

                    <select name="gelombang_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Gelombang</option>
                        @foreach($gelombangList as $g)
                            <option value="{{ $g->id }}" {{ request('gelombang_id') == $g->id ? 'selected' : '' }}>{{ $g->nama_gelombang }}</option>
                        @endforeach
                    </select>

                    <div class="input-group input-group-sm" style="width: 220px;">
                        <input type="text" name="search" class="form-control" placeholder="Cari Nama / NISN / No. Daftar..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i></button>
                    </div>

                    @if(request()->hasAny(['search', 'jurusan_id', 'gelombang_id']))
                        <a href="{{ route('data-cetak-kartu.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter"><i class="bx bx-refresh"></i></a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bx bx-check-circle me-1"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bx bx-error-circle me-1"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="40">#</th>
                            <th width="140">No. Pendaftaran</th>
                            <th>Nama Siswa</th>
                            <th>NISN</th>
                            <th>Jurusan</th>
                            <th>Gelombang</th>
                            <th width="150">Username Kartu</th>
                            <th width="130">Password Kartu</th>
                            <th width="120">Status</th>
                            <th width="110">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dataSiswaList as $key => $row)
                            @php
                                $user = $row->user;
                                // Username Kartu
                                $usernameKartu = '-';
                                $passwordKartu = '-';

                                if ($user) {
                                    $usernameKartu = empty($pengaturan->kartu_username_contoh) || $pengaturan->kartu_username_contoh === '[Username Anda]'
                                        ? $user->username
                                        : $pengaturan->kartu_username_contoh . substr($user->username, -3);

                                    $passwordKartu = !empty($pengaturan->kartu_password_contoh)
                                        ? $pengaturan->kartu_password_contoh
                                        : str_pad(abs(crc32($user->id . $user->username)) % 900000 + 100000, 6, '0', STR_PAD_LEFT);
                                }

                            @endphp
                            <tr>
                                <td class="text-center">{{ $dataSiswaList->firstItem() + $key }}</td>
                                <td class="text-center fw-bold text-primary">{{ $row->no_pendaftaran ?? '-' }}</td>
                                <td>
                                    <div class="fw-bold">{{ $row->nama_lengkap }}</div>
                                    <small class="text-muted">{{ $row->email ?? $user?->email }}</small>
                                </td>
                                <td class="text-center">{{ $row->nisn ?? '-' }}</td>
                                <td>{{ $row->jurusan->nama_jurusan ?? '-' }}</td>
                                <td>{{ $row->gelombang->nama_gelombang ?? '-' }}</td>
                                <td class="text-center">
                                    <code class="bg-light px-2 py-1 rounded border border-primary text-primary fw-bold">{{ $usernameKartu }}</code>
                                </td>
                                <td class="text-center">
                                    <code class="bg-light px-2 py-1 rounded border border-success text-success fw-bold">{{ $passwordKartu }}</code>
                                </td>
                                <td class="text-center">
                                    @if($row->status_pendaftar === 'diterima')
                                        <span class="badge bg-success">Diterima</span>
                                    @elseif($row->status_pendaftar === 'terverifikasi')
                                        <span class="badge bg-primary">Terverifikasi</span>
                                    @elseif($row->status_pendaftar === 'ditolak')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('data-cetak-kartu.cetak', $row->id) }}" target="_blank" class="btn btn-warning btn-sm" title="Cetak Kartu Peserta">
                                        <i class="bx bx-printer me-1"></i> Cetak
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    <i class="bx bx-info-circle fs-3 d-block mb-1"></i>
                                    Belum ada data pendaftar untuk dicetak kartu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Component -->
            <x-pagination :paginator="$dataSiswaList" />
        </div>
    </div>
</div>
@endsection
