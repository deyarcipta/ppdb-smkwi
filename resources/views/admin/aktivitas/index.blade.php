@extends('admin.layouts.app')

@section('title', 'Log Aktivitas - Admin Panel')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-time me-2"></i>Log Aktivitas Admin
                    </h5>
                    <div class="d-flex">
                        <form action="{{ route('admin.aktivitas.search') }}" method="GET" class="me-2">
                            <div class="input-group input-group-merge">
                                <input type="text" 
                                    class="form-control" 
                                    name="search" 
                                    placeholder="Cari aktivitas..." 
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

    <!-- Statistik -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="fw-semibold d-block">Total Aktivitas</span>
                            <h4 class="card-title mb-0">{{ number_format($aktivitas->total()) }}</h4>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-list-ul"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="fw-semibold d-block">Create</span>
                            <h4 class="card-title mb-0">{{ number_format($totalCreate ?? 0) }}</h4>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-plus"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Verify -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="fw-semibold d-block">Verify</span>
                            <h4 class="card-title mb-0">{{ number_format($totalVerify ?? 0) }}</h4>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="bx bx-check-circle"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="fw-semibold d-block">Delete</span>
                            <h4 class="card-title mb-0">{{ number_format($totalDelete ?? 0) }}</h4>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="bx bx-trash"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Tabel Aktivitas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Admin</th>
                                    <th>Aksi</th>
                                    <th>Deskripsi</th>
                                    <th>IP Address</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($aktivitas as $item)
                                <tr>
                                    <td>{{ $loop->iteration + ($aktivitas->currentPage() - 1) * $aktivitas->perPage() }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-{{ $item->color }}">
                                                    {{ substr($item->admin->name ?? 'A', 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="fw-semibold">{{ $item->admin->name ?? 'System' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $item->color }}">
                                            {{ ucfirst($item->action) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 300px;">
                                            {{ $item->description }}
                                        </span>
                                    </td>
                                    <td><code>{{ $item->ip_address }}</code></td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $item->created_at->format('d M Y') }}<br>
                                            {{ $item->created_at->format('H:i:s') }}
                                        </small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="bx bx-time display-4 text-muted mb-2"></i>
                                        <p class="text-muted mb-0">
                                            @if(request('search'))
                                                Tidak ada aktivitas yang sesuai dengan pencarian
                                            @else
                                                Belum ada aktivitas yang tercatat
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($aktivitas->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <p class="mb-0 text-muted">
                                Menampilkan {{ $aktivitas->firstItem() }} - {{ $aktivitas->lastItem() }} dari {{ $aktivitas->total() }} aktivitas
                            </p>
                        </div>
                        {{ $aktivitas->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.avatar-initial {
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}
</style>
@endsection