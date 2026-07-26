@extends('admin.layouts.app')
@section('title', 'Data SMP')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
        <div>
            <h5 class="mb-1"><i class="bx bx-building-house me-2 text-primary"></i>Data SMP / MTs Asal</h5>
            <small class="text-muted">Kelola data sekolah asal siswa pendaftar PPDB.</small>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-2">
            <form action="{{ route('data-smp.index') }}" method="GET" class="d-flex gap-2">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" name="search" class="form-control" placeholder="Cari Nama SMP..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i></button>
                </div>
                @if(request()->filled('search'))
                    <a href="{{ route('data-smp.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter"><i class="bx bx-refresh"></i></a>
                @endif
            </form>

            <a href="{{ route('data-smp.export-excel', request()->all()) }}" class="btn btn-success btn-sm text-nowrap">
                <i class="bx bx-download me-1"></i>Export Excel
            </a>

            <button class="btn btn-info btn-sm text-nowrap text-white" data-bs-toggle="modal" data-bs-target="#importSmpModal">
                <i class="bx bx-upload me-1"></i>Import Excel
            </button>

            <button class="btn btn-primary btn-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#dataSmpModal">
                <i class="bx bx-plus me-1"></i>Tambah SMP
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>Nama SMP</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataSmp as $row)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="align-middle">{{ $row->nama_smp }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-warning btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#dataSmpModal"
                                        onclick="editDataSmp({{ $row->id_smp }}, '{{ $row->nama_smp }}')"
                                        title="Edit"
                                        style="margin: 0 5px">
                                    <i class="bx bx-edit"></i>
                                </button>

                                <form action="{{ route('data-smp.destroy', $row->id_smp) }}" 
                                      method="POST" class="d-inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bx bx-package display-6"></i>
                                <p class="mt-2">Belum ada data SMP</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Component -->
        <x-pagination :paginator="$dataSmp" />
    </div>
</div>

<!-- Modal Create/Edit -->
<div class="modal fade" id="dataSmpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="dataSmpForm" class="modal-content" method="POST">
            @csrf
            <div id="formMethod"></div>
            
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Data SMP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div class="mb-3">
                    <label for="nama_smp" class="form-label">Nama SMP <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_smp" name="nama_smp" 
                           placeholder="Contoh: SMP Negeri 1 Jakarta" required>
                    <div class="form-text">Masukkan nama SMP lengkap</div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i>Batal
                </button>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="bx bx-save me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importSmpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('data-smp.import-excel') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title text-white"><i class="bx bx-upload me-2"></i>Import Data SMP dari Excel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body py-3">
                <div class="alert alert-success bg-light-success border border-success p-3 rounded mb-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bx bx-file text-success fs-3"></i>
                        <div>
                            <strong class="text-success d-block mb-1">Unduh Template Contoh Excel</strong>
                            <p class="small mb-2 text-muted">Gunakan template berkas Excel resmi agar format kolom data sesuai saat diunggah ke sistem.</p>
                            <a href="{{ route('data-smp.download-template') }}" class="btn btn-sm btn-success fw-bold">
                                <i class="bx bx-download me-1"></i>Unduh Template (.xlsx)
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Berkas Excel <span class="text-danger">*</span></label>
                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                    <div class="form-text mt-1">
                        Format yang didukung: <strong>.xlsx, .xls, .csv</strong> (Maksimal 10MB).
                    </div>
                </div>

                <div class="p-3 bg-light rounded border small">
                    <strong class="d-block mb-1 text-dark"><i class="bx bx-info-circle text-primary me-1"></i>Petunjuk Pengisian Kolom:</strong>
                    <ul class="mb-0 ps-3 text-muted">
                        <li>Kolom utama yang dibaca adalah <strong>NAMA SMP</strong> pada kolom B.</li>
                        <li>Data SMP yang sudah terdaftar di database akan dilewati secara otomatis (pencegahan duplikasi).</li>
                    </ul>
                </div>
            </div>
            
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i>Batal
                </button>
                <button type="submit" class="btn btn-info text-white fw-bold">
                    <i class="bx bx-cloud-upload me-1"></i>Unggah & Import Data
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Custom Styles */
.table td, .table th {
    vertical-align: middle !important;
}

.btn-sm {
    padding: 0.3rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.2;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 0.25rem;
}

/* Spacing untuk tombol aksi */
.btn-group {
    gap: 4px;
}

/* Untuk form delete inline */
.form-delete {
    display: inline;
    margin: 0;
}

/* Hover effect untuk tombol */
.btn-warning:hover {
    background-color: #ffca2c;
    border-color: #ffca2c;
}

.btn-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
}

/* Responsive table */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
    }
    
    th.text-center, td.text-center {
        text-align: center !important;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Fungsi untuk edit data
function editDataSmp(id, namaSmp) {
    // Update modal title
    document.getElementById('modalTitle').textContent = 'Edit Data SMP';
    
    // Update form action dan method
    document.getElementById('dataSmpForm').action = `/admin/data-smp/${id}`;
    document.getElementById('formMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    
    // Isi form dengan data yang ada
    document.getElementById('nama_smp').value = namaSmp;
    
    // Update submit button text
    document.getElementById('submitBtn').innerHTML = '<i class="bx bx-save me-1"></i>Update';
}

// Fungsi untuk reset modal ke mode tambah
document.getElementById('dataSmpModal').addEventListener('hidden.bs.modal', function () {
    // Reset form
    document.getElementById('dataSmpForm').reset();
    document.getElementById('dataSmpForm').action = "{{ route('data-smp.store') }}";
    document.getElementById('formMethod').innerHTML = '';
    document.getElementById('modalTitle').textContent = 'Tambah Data SMP';
    document.getElementById('submitBtn').innerHTML = '<i class="bx bx-save me-1"></i>Simpan';
    
    // Reset validation
    const inputs = document.querySelectorAll('.is-invalid');
    inputs.forEach(input => input.classList.remove('is-invalid'));
    
    const feedbacks = document.querySelectorAll('.invalid-feedback');
    feedbacks.forEach(feedback => feedback.remove());
});

// Konfirmasi Hapus dengan tampilan yang lebih baik
document.querySelectorAll('.form-delete').forEach(form => {
    form.addEventListener('submit', e => {
        e.preventDefault();
        Swal.fire({
            title: 'Hapus Data SMP',
            text: 'Apakah Anda yakin ingin menghapus data ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            }
        }).then(result => {
            if (result.isConfirmed) form.submit();
        });
    });
});

// Notifikasi SweetAlert dengan toast yang lebih baik
@if(session('success'))
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    
    Toast.fire({
        icon: 'success',
        title: '{{ session('success') }}'
    });
@endif

@if(session('error'))
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    
    Toast.fire({
        icon: 'error',
        title: '{{ session('error') }}'
    });
@endif

// Validasi form dengan feedback yang lebih baik
document.getElementById('dataSmpForm').addEventListener('submit', function(e) {
    const namaSmp = document.getElementById('nama_smp').value.trim();
    const namaSmpField = document.getElementById('nama_smp');
    
    // Clear previous validation
    namaSmpField.classList.remove('is-invalid');
    const existingError = namaSmpField.nextElementSibling?.classList.contains('invalid-feedback');
    if (existingError) {
        namaSmpField.nextElementSibling.remove();
    }
    
    if (!namaSmp) {
        e.preventDefault();
        namaSmpField.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = 'Nama SMP tidak boleh kosong';
        namaSmpField.parentNode.appendChild(errorDiv);
        
        // Tampilkan sweetalert juga
        Swal.fire({
            icon: 'error',
            title: 'Validasi Error',
            text: 'Nama SMP tidak boleh kosong',
            timer: 2000,
            showConfirmButton: false
        });
        return false;
    }
    
    // Tampilkan loading
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
    submitBtn.disabled = true;
    
    // Auto enable setelah 5 detik jika ada error
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 5000);
});
</script>
@endpush