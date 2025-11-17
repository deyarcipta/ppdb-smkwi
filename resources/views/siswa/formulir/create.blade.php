@extends('siswa.layouts.app')
@section('title', 'Formulir Pendaftaran Siswa')

@section('content')
<div class="container-fluid">
    <!-- Header Card -->
    <div class="card gradient-card shadow-lg border-0 mb-4">
        <div class="card-body text-center text-white py-4">
            <h2 class="mb-2"><i class="fas fa-file-alt me-2"></i>FORMULIR PENDAFTARAN PPDB</h2>
            <p class="mb-0 opacity-75">Lengkapi data diri Anda dengan benar dan teliti</p>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Profil -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-lg border-0 profile-card">
                <div class="profile-header">
                    <div class="profile-img">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h5 class="mb-1" style="color: whitesmoke"> {{ $existingData->nama_lengkap ?? '-' }}</h5>
                    <p class="mb-2">PPDB Tahun {{ $existingData->gelombang->tahunAjaran->nama ?? '-' }}</p>
                    @if($progress['total'] == 100)
                        <div class="status-badge status-completed bg-success text-white">
                            <i class="fas fa-check-circle me-1"></i> Lengkap
                        </div>
                    @elseif($progress['total'] >= 80)
                        <div class="status-badge status-almost bg-info text-white">
                            <i class="fas fa-tasks me-1"></i> Hampir Lengkap ({{ $progress['total'] }}%)
                        </div>
                    @elseif($progress['total'] >= 50)
                        <div class="status-badge status-progress bg-warning text-dark">
                            <i class="fas fa-spinner me-1"></i> Sedang Berjalan ({{ $progress['total'] }}%)
                        </div>
                    @else
                        <div class="status-badge status-pending bg-danger text-white">
                            <i class="fas fa-clock me-1"></i> Belum Lengkap ({{ $progress['total'] }}%)
                        </div>
                    @endif
                    <div class="wave-badge">
                        <i class="fas fa-wave-square me-1"></i> {{$existingData->gelombang->nama_gelombang ?? '-'}}
                    </div>
                </div>
                <div class="card-body">
                    <div class="progress-card card border-0">
                        <div class="card-body">
                            <h5 class="progress-title">
                                <i class="fas fa-tasks"></i> Progres Pengisian Formulir
                            </h5>
                            
                            <div class="progress-item">
                                <div class="progress-label">
                                    <span>Data Diri & Keluarga</span>
                                    <span>{{ $progress['data_diri'] }}%</span>
                                </div>
                                <div class="progress-bar-custom">
                                    <div class="progress-fill" style="width: {{ $progress['data_diri'] }}%"></div>
                                </div>
                            </div>
                            
                            <div class="progress-item">
                                <div class="progress-label">
                                    <span>Data Alamat</span>
                                    <span>{{$progress['alamat']}}%</span>
                                </div>
                                <div class="progress-bar-custom">
                                    <div class="progress-fill" style="width: {{$progress['alamat']}}%"></div>
                                </div>
                            </div>

                            <div class="progress-item">
                                <div class="progress-label">
                                    <span>Data Orang Tua</span>
                                    <span>{{$progress['orangtua']}}%</span>
                                </div>
                                <div class="progress-bar-custom">
                                    <div class="progress-fill" style="width: {{$progress['orangtua']}}%"></div>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">Total Progress</span>
                                    <span class="fw-bold text-primary">{{$progress['total']}}%</span>
                                </div>
                                <div class="progress-bar-custom mt-2 mb-2">
                                    <div class="progress-fill" style="width: {{$progress['total']}}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card border-0 mt-3" style="background-color: #f8f9fa;">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi</h6>
                            <p class="card-text small">
                                Pastikan semua data yang Anda isi sudah benar sebelum mengirim formulir. 
                                Data yang sudah dikirim tidak dapat diubah.
                            </p>
                            <div class="d-grid">
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-download me-1"></i> Unduh Panduan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Container -->
        <div class="col-lg-9">
            <div class="card shadow-lg border-0 form-container">
                <div class="form-tabs">
                    <div class="form-tab active" data-tab="data-diri">
                        <i class="fas fa-user"></i> Data Diri & Keluarga
                    </div>
                    <div class="form-tab" data-tab="data-alamat">
                        <i class="fas fa-home"></i> Data Alamat
                    </div>
                    <div class="form-tab" data-tab="data-orangtua">
                        <i class="fas fa-user-friends"></i> Data Orang Tua
                    </div>
                </div>
                
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Terdapat kesalahan dalam pengisian form. Silakan periksa kembali.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('siswa.formulir.store') }}" method="POST" id="multiStepForm">
                        @csrf

                        <!-- Tab 1: Data Diri & Keluarga -->
                        <div class="tab-pane active" id="data-diri">
                            <div class="step-header mb-4">
                                <h4 class="text-primary mb-2">
                                    <i class="fas fa-user-circle me-2"></i>Data Diri & Keluarga Siswa
                                </h4>
                                <p class="text-muted">Informasi pribadi, identitas, dan kondisi keluarga siswa</p>
                            </div>

                            <!-- Data Pribadi -->
                            <div class="section-header mb-4">
                                <h5 class="text-primary">
                                    <i class="fas fa-user me-2"></i>Data Pribadi
                                </h5>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="nisn" class="form-label fw-semibold">
                                            <i class="fas fa-id-card me-2 text-primary"></i>NISN
                                        </label>
                                        <input type="text" class="form-control form-control-lg @error('nisn') is-invalid @enderror" id="nisn" name="nisn" 
                                               value="{{ $existingData->nisn ?? old('nisn') }}" 
                                               placeholder="Masukkan NISN">
                                        @error('nisn')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="nik" class="form-label fw-semibold">
                                            <i class="fas fa-address-card me-2 text-primary"></i>NIK
                                        </label>
                                        <input type="text" class="form-control form-control-lg @error('nik') is-invalid @enderror" id="nik" name="nik" 
                                               value="{{ $existingData->nik ?? old('nik') }}" 
                                               placeholder="Masukkan NIK">
                                        @error('nik')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="no_kk" class="form-label fw-semibold">
                                            <i class="fas fa-address-card me-2 text-primary"></i>No. KK
                                        </label>
                                        <input type="text" class="form-control form-control-lg @error('no_kk') is-invalid @enderror" id="no_kk" name="no_kk" 
                                               value="{{ $existingData->no_kk ?? old('no_kk') }}" 
                                               placeholder="Masukkan No. Kartu Keluarga">
                                        @error('no_kk')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="nama_lengkap" class="form-label fw-semibold">
                                            <i class="fas fa-signature me-2 text-primary"></i>Nama Lengkap <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control form-control-lg @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" 
                                               value="{{ $existingData->nama_lengkap ?? old('nama_lengkap') }}" 
                                               placeholder="Masukkan nama lengkap" required>
                                        @error('nama_lengkap')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="jenis_kelamin" class="form-label fw-semibold">
                                            <i class="fas fa-venus-mars me-2 text-primary"></i>Jenis Kelamin
                                        </label>
                                        <select class="form-select form-select-lg @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin">
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="Laki-Laki" {{ ($existingData->jenis_kelamin ?? old('jenis_kelamin')) == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                            <option value="Perempuan" {{ ($existingData->jenis_kelamin ?? old('jenis_kelamin')) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        @error('jenis_kelamin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="tempat_lahir" class="form-label fw-semibold">
                                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>Tempat Lahir
                                        </label>
                                        <input type="text" class="form-control form-control-lg @error('tempat_lahir') is-invalid @enderror" id="tempat_lahir" name="tempat_lahir" 
                                               value="{{ $existingData->tempat_lahir ?? old('tempat_lahir') }}" 
                                               placeholder="Kota tempat lahir">
                                        @error('tempat_lahir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="tanggal_lahir" class="form-label fw-semibold">
                                            <i class="fas fa-calendar-alt me-2 text-primary"></i>Tanggal Lahir
                                        </label>
                                        <input type="date" class="form-control form-control-lg @error('tanggal_lahir') is-invalid @enderror" 
                                            id="tanggal_lahir" name="tanggal_lahir" 
                                            value="{{ isset($existingData->tanggal_lahir) ? \Carbon\Carbon::parse($existingData->tanggal_lahir)->format('Y-m-d') : old('tanggal_lahir') }}">
                                        @error('tanggal_lahir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="no_hp" class="form-label fw-semibold">
                                            <i class="fas fa-phone me-2 text-primary"></i>No. HP/WhatsApp
                                        </label>
                                        <input type="text" class="form-control form-control-lg @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" 
                                               value="{{ $existingData->no_hp ?? old('no_hp') }}" 
                                               placeholder="Contoh: 081234567890">
                                        @error('no_hp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="asal_sekolah" class="form-label fw-semibold">
                                            <i class="fas fa-school me-2 text-primary"></i>Asal Sekolah
                                        </label>
                                        <input type="text" class="form-control form-control-lg @error('asal_sekolah') is-invalid @enderror" id="asal_sekolah" name="asal_sekolah" 
                                               value="{{ $existingData->asal_sekolah ?? old('asal_sekolah') }}" 
                                               placeholder="Nama sekolah asal">
                                        @error('asal_sekolah')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="agama" class="form-label fw-semibold">
                                            <i class="fas fa-pray me-2 text-primary"></i>Agama
                                        </label>
                                        <select class="form-select form-select-lg @error('agama') is-invalid @enderror" id="agama" name="agama">
                                            <option value="">Pilih Agama</option>
                                            <option value="Islam" {{ ($existingData->agama ?? old('agama')) == 'Islam' ? 'selected' : '' }}>Islam</option>
                                            <option value="Kristen" {{ ($existingData->agama ?? old('agama')) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                            <option value="Katolik" {{ ($existingData->agama ?? old('agama')) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                            <option value="Hindu" {{ ($existingData->agama ?? old('agama')) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                            <option value="Buddha" {{ ($existingData->agama ?? old('agama')) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                            <option value="Konghucu" {{ ($existingData->agama ?? old('agama')) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                        </select>
                                        @error('agama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="ukuran_baju" class="form-label fw-semibold">
                                            <i class="fas fa-tshirt me-2 text-primary"></i>Ukuran Baju
                                        </label>
                                        <select class="form-select form-select-lg @error('ukuran_baju') is-invalid @enderror" id="ukuran_baju" name="ukuran_baju">
                                            <option value="">Pilih Ukuran</option>
                                            <option value="S" {{ ($existingData->ukuran_baju ?? old('ukuran_baju')) == 'S' ? 'selected' : '' }}>S</option>
                                            <option value="M" {{ ($existingData->ukuran_baju ?? old('ukuran_baju')) == 'M' ? 'selected' : '' }}>M</option>
                                            <option value="L" {{ ($existingData->ukuran_baju ?? old('ukuran_baju')) == 'L' ? 'selected' : '' }}>L</option>
                                            <option value="XL" {{ ($existingData->ukuran_baju ?? old('ukuran_baju')) == 'XL' ? 'selected' : '' }}>XL</option>
                                            <option value="XXL" {{ ($existingData->ukuran_baju ?? old('ukuran_baju')) == 'XXL' ? 'selected' : '' }}>XXL</option>
                                        </select>
                                        @error('ukuran_baju')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="no_kip" class="form-label fw-semibold">
                                            <i class="fas fa-id-card-alt me-2 text-primary"></i>No. KIP
                                        </label>
                                        <input type="text" class="form-control form-control-lg @error('no_kip') is-invalid @enderror" id="no_kip" name="no_kip" 
                                               value="{{ $existingData->no_kip ?? old('no_kip') }}" 
                                               placeholder="No. Kartu Indonesia Pintar">
                                        @error('no_kip')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="hobi" class="form-label fw-semibold">
                                            <i class="fas fa-heart me-2 text-primary"></i>Hobi
                                        </label>
                                        <input type="text" class="form-control form-control-lg @error('hobi') is-invalid @enderror" id="hobi" name="hobi" 
                                               value="{{ $existingData->hobi ?? old('hobi') }}" 
                                               placeholder="Hobi siswa">
                                        @error('hobi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="cita_cita" class="form-label fw-semibold">
                                            <i class="fas fa-star me-2 text-primary"></i>Cita-cita
                                        </label>
                                        <input type="text" class="form-control form-control-lg @error('cita_cita') is-invalid @enderror" id="cita_cita" name="cita_cita" 
                                               value="{{ $existingData->cita_cita ?? old('cita_cita') }}" 
                                               placeholder="Cita-cita siswa">
                                        @error('cita_cita')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Data Keluarga -->
                            <div class="section-header mb-4 mt-5">
                                <h5 class="text-primary">
                                    <i class="fas fa-users me-2"></i>Data Keluarga
                                </h5>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="anak_ke" class="form-label fw-semibold">
                                            <i class="fas fa-sort-numeric-up me-2 text-primary"></i>Anak Ke
                                        </label>
                                        <input type="number" class="form-control form-control-lg @error('anak_ke') is-invalid @enderror" id="anak_ke" name="anak_ke" 
                                               value="{{ $existingData->anak_ke ?? old('anak_ke') }}" 
                                               placeholder="Contoh: 1, 2, 3">
                                        @error('anak_ke')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="jumlah_saudara" class="form-label fw-semibold">
                                            <i class="fas fa-user-friends me-2 text-primary"></i>Jumlah Saudara
                                        </label>
                                        <input type="number" class="form-control form-control-lg @error('jumlah_saudara') is-invalid @enderror" id="jumlah_saudara" name="jumlah_saudara" 
                                               value="{{ $existingData->jumlah_saudara ?? old('jumlah_saudara') }}" 
                                               placeholder="Jumlah saudara kandung">
                                        @error('jumlah_saudara')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="status_dalam_keluarga" class="form-label fw-semibold">
                                            <i class="fas fa-user-tag me-2 text-primary"></i>Status dalam Keluarga
                                        </label>
                                        <select class="form-select form-select-lg @error('status_dalam_keluarga') is-invalid @enderror" id="status_dalam_keluarga" name="status_dalam_keluarga">
                                            <option value="">Pilih Status</option>
                                            <option value="Anak Kandung" {{ ($existingData->status_dalam_keluarga ?? old('status_dalam_keluarga')) == 'Anak Kandung' ? 'selected' : '' }}>Anak Kandung</option>
                                            <option value="Anak Angkat" {{ ($existingData->status_dalam_keluarga ?? old('status_dalam_keluarga')) == 'Anak Angkat' ? 'selected' : '' }}>Anak Angkat</option>
                                            <option value="Anak Asuh" {{ ($existingData->status_dalam_keluarga ?? old('status_dalam_keluarga')) == 'Anak Asuh' ? 'selected' : '' }}>Anak Asuh</option>
                                        </select>
                                        @error('status_dalam_keluarga')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="tinggi_badan" class="form-label fw-semibold">
                                            <i class="fas fa-arrows-alt-v me-2 text-primary"></i>Tinggi Badan (cm)
                                        </label>
                                        <input type="number" class="form-control form-control-lg @error('tinggi_badan') is-invalid @enderror" id="tinggi_badan" name="tinggi_badan" 
                                               value="{{ $existingData->tinggi_badan ?? old('tinggi_badan') }}" 
                                               placeholder="Tinggi badan dalam cm">
                                        @error('tinggi_badan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="berat_badan" class="form-label fw-semibold">
                                            <i class="fas fa-weight me-2 text-primary"></i>Berat Badan (kg)
                                        </label>
                                        <input type="number" class="form-control form-control-lg @error('berat_badan') is-invalid @enderror" id="berat_badan" name="berat_badan" 
                                               value="{{ $existingData->berat_badan ?? old('berat_badan') }}" 
                                               placeholder="Berat badan dalam kg">
                                        @error('berat_badan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="tinggal_bersama" class="form-label fw-semibold">
                                            <i class="fas fa-house-user me-2 text-primary"></i>Tinggal Bersama
                                        </label>
                                        <select class="form-select form-select-lg @error('tinggal_bersama') is-invalid @enderror" id="tinggal_bersama" name="tinggal_bersama">
                                            <option value="">Pilih Status Tinggal</option>
                                            <option value="Orang Tua" {{ ($existingData->tinggal_bersama ?? old('tinggal_bersama')) == 'Orang Tua' ? 'selected' : '' }}>Orang Tua</option>
                                            <option value="Wali" {{ ($existingData->tinggal_bersama ?? old('tinggal_bersama')) == 'Wali' ? 'selected' : '' }}>Wali</option>
                                            <option value="Asrama" {{ ($existingData->tinggal_bersama ?? old('tinggal_bersama')) == 'Asrama' ? 'selected' : '' }}>Asrama</option>
                                            <option value="Kost" {{ ($existingData->tinggal_bersama ?? old('tinggal_bersama')) == 'Kost' ? 'selected' : '' }}>Kost</option>
                                        </select>
                                        @error('tinggal_bersama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="jarak_kesekolah" class="form-label fw-semibold">
                                            <i class="fas fa-road me-2 text-primary"></i>Jarak ke Sekolah (km)
                                        </label>
                                        <input type="number" class="form-control form-control-lg @error('jarak_kesekolah') is-invalid @enderror" id="jarak_kesekolah" name="jarak_kesekolah" 
                                               value="{{ $existingData->jarak_kesekolah ?? old('jarak_kesekolah') }}" 
                                               placeholder="Jarak dalam kilometer">
                                        @error('jarak_kesekolah')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="waktu_tempuh" class="form-label fw-semibold">
                                            <i class="fas fa-clock me-2 text-primary"></i>Waktu Tempuh (menit)
                                        </label>
                                        <input type="number" class="form-control form-control-lg @error('waktu_tempuh') is-invalid @enderror" id="waktu_tempuh" name="waktu_tempuh" 
                                               value="{{ $existingData->waktu_tempuh ?? old('waktu_tempuh') }}" 
                                               placeholder="Waktu tempuh dalam menit">
                                        @error('waktu_tempuh')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="transportasi" class="form-label fw-semibold">
                                            <i class="fas fa-bus me-2 text-primary"></i>Transportasi
                                        </label>
                                        <select class="form-select form-select-lg @error('transportasi') is-invalid @enderror" id="transportasi" name="transportasi">
                                            <option value="">Pilih Transportasi</option>
                                            <option value="Jalan Kaki" {{ ($existingData->transportasi ?? old('transportasi')) == 'Jalan Kaki' ? 'selected' : '' }}>Jalan Kaki</option>
                                            <option value="Sepeda" {{ ($existingData->transportasi ?? old('transportasi')) == 'Sepeda' ? 'selected' : '' }}>Sepeda</option>
                                            <option value="Sepeda Motor" {{ ($existingData->transportasi ?? old('transportasi')) == 'Sepeda Motor' ? 'selected' : '' }}>Sepeda Motor</option>
                                            <option value="Mobil" {{ ($existingData->transportasi ?? old('transportasi')) == 'Mobil' ? 'selected' : '' }}>Mobil</option>
                                            <option value="Angkutan Umum" {{ ($existingData->transportasi ?? old('transportasi')) == 'Angkutan Umum' ? 'selected' : '' }}>Angkutan Umum</option>
                                        </select>
                                        @error('transportasi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Referensi -->
                            <div class="section-header mb-4 mt-5">
                                <h5 class="text-primary">
                                    <i class="fas fa-share-alt me-2"></i>Referensi
                                </h5>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="referensi" class="form-label fw-semibold">
                                            <i class="fas fa-user-friends me-2 text-primary"></i>Referensi
                                        </label>
                                        <select class="form-select form-select-lg @error('referensi') is-invalid @enderror" id="referensi" name="referensi">
                                            <option value="" selected disabled>--Pilih Referensi--</option>
                                            <option value="guru-staff" {{ ($existingData->referensi ?? old('referensi')) == 'guru-staff' ? 'selected' : '' }}>Guru/Staff/Laboran/Pegawai Wisata Indonesia</option>
                                            <option value="siswa" {{ ($existingData->referensi ?? old('referensi')) == 'siswa' ? 'selected' : '' }}>Siswa SMK Wisata Indonesia</option>
                                            <option value="alumni" {{ ($existingData->referensi ?? old('referensi')) == 'alumni' ? 'selected' : '' }}>Alumni SMK Wisata Indonesia</option>
                                            <option value="guru-smp" {{ ($existingData->referensi ?? old('referensi')) == 'guru-smp' ? 'selected' : '' }}>Guru SMP</option>
                                            <option value="calon-siswa" {{ ($existingData->referensi ?? old('referensi')) == 'calon-siswa' ? 'selected' : '' }}>Calon Siswa SMK Wisata Indonesia</option>
                                            <option value="sosial-media" {{ ($existingData->referensi ?? old('referensi')) == 'sosial-media' ? 'selected' : '' }}>Sosial Media</option>
                                            <option value="referensi-langsung" {{ ($existingData->referensi ?? old('referensi')) == 'referensi-langsung' ? 'selected' : '' }}>Referensi Langsung</option>
                                        </select>
                                        @error('referensi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="ket_referensi" class="form-label fw-semibold">
                                            <i class="fas fa-info-circle me-2 text-primary"></i>Keterangan Referensi
                                        </label>
                                        <input type="text" class="form-control form-control-lg @error('ket_referensi') is-invalid @enderror" id="ket_referensi" name="ket_referensi" 
                                               value="{{ $existingData->ket_referensi ?? old('ket_referensi') }}" 
                                               placeholder="Keterangan tambahan referensi">
                                        @error('ket_referensi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions mt-5">
                                <button type="button" class="btn btn-primary btn-lg next-tab" data-next="data-alamat">
                                    Lanjutkan <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Tab 2: Data Alamat -->
                        <div class="tab-pane" id="data-alamat">
                            <div class="step-header mb-4">
                                <h4 class="text-primary mb-2">
                                    <i class="fas fa-home me-2"></i>Data Alamat Siswa
                                </h4>
                                <p class="text-muted">Informasi tempat tinggal dan kontak</p>
                            </div>

                            <div class="form-group mb-4">
                                <label for="alamat" class="form-label fw-semibold">
                                    <i class="fas fa-map-marked-alt me-2 text-primary"></i>Alamat Lengkap
                                </label>
                                <textarea class="form-control form-control-lg @error('alamat') is-invalid @enderror" id="alamat" name="alamat" 
                                          rows="3" placeholder="Masukkan alamat lengkap">{{ $existingData->alamat ?? old('alamat') }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="rt" class="form-label fw-semibold">RT</label>
                                        <input type="text" class="form-control form-control-lg @error('rt') is-invalid @enderror" id="rt" name="rt" 
                                               value="{{ $existingData->rt ?? old('rt') }}" 
                                               placeholder="000">
                                        @error('rt')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="rw" class="form-label fw-semibold">RW</label>
                                        <input type="text" class="form-control form-control-lg @error('rw') is-invalid @enderror" id="rw" name="rw" 
                                               value="{{ $existingData->rw ?? old('rw') }}" 
                                               placeholder="000">
                                        @error('rw')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="desa" class="form-label fw-semibold">Desa/Kelurahan</label>
                                        <input type="text" class="form-control form-control-lg @error('desa') is-invalid @enderror" id="desa" name="desa" 
                                               value="{{ $existingData->desa ?? old('desa') }}" 
                                               placeholder="Nama desa/kelurahan">
                                        @error('desa')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="kecamatan" class="form-label fw-semibold">Kecamatan</label>
                                        <input type="text" class="form-control form-control-lg @error('kecamatan') is-invalid @enderror" id="kecamatan" name="kecamatan" 
                                               value="{{ $existingData->kecamatan ?? old('kecamatan') }}" 
                                               placeholder="Nama kecamatan">
                                        @error('kecamatan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="kota" class="form-label fw-semibold">Kota/Kabupaten</label>
                                        <input type="text" class="form-control form-control-lg @error('kota') is-invalid @enderror" id="kota" name="kota" 
                                               value="{{ $existingData->kota ?? old('kota') }}" 
                                               placeholder="Nama kota/kabupaten">
                                        @error('kota')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="provinsi" class="form-label fw-semibold">Provinsi</label>
                                        <input type="text" class="form-control form-control-lg @error('provinsi') is-invalid @enderror" id="provinsi" name="provinsi" 
                                               value="{{ $existingData->provinsi ?? old('provinsi') }}" 
                                               placeholder="Nama provinsi">
                                        @error('provinsi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="kode_pos" class="form-label fw-semibold">Kode Pos</label>
                                        <input type="text" class="form-control form-control-lg @error('kode_pos') is-invalid @enderror" id="kode_pos" name="kode_pos" 
                                               value="{{ $existingData->kode_pos ?? old('kode_pos') }}" 
                                               placeholder="00000">
                                        @error('kode_pos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions mt-5 d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary btn-lg prev-tab" data-prev="data-diri">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </button>
                                <button type="button" class="btn btn-primary btn-lg next-tab" data-next="data-orangtua">
                                    Lanjutkan <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Tab 3: Data Orang Tua -->
                        <div class="tab-pane" id="data-orangtua">
                            <div class="step-header mb-4">
                                <h4 class="text-primary mb-2">
                                    <i class="fas fa-user-friends me-2"></i>Data Orang Tua
                                </h4>
                                <p class="text-muted">Informasi orang tua/wali siswa</p>
                            </div>

                            <!-- Data Ayah -->
                            <div class="parent-section mb-5">
                                <div class="section-header-bg">
                                    <h5 class="mb-0">
                                        <i class="fas fa-male me-2"></i>Data Ayah Kandung
                                    </h5>
                                </div>
                                <div class="section-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="nik_ayah" class="form-label fw-semibold">NIK Ayah</label>
                                                <input type="text" class="form-control @error('nik_ayah') is-invalid @enderror" id="nik_ayah" name="nik_ayah" 
                                                       value="{{ $existingData->nik_ayah ?? old('nik_ayah') }}" 
                                                       placeholder="Masukkan NIK ayah">
                                                @error('nik_ayah')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="nama_ayah" class="form-label fw-semibold">Nama Lengkap Ayah</label>
                                                <input type="text" class="form-control @error('nama_ayah') is-invalid @enderror" id="nama_ayah" name="nama_ayah" 
                                                       value="{{ $existingData->nama_ayah ?? old('nama_ayah') }}" 
                                                       placeholder="Nama lengkap ayah">
                                                @error('nama_ayah')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="tempat_lahir_ayah" class="form-label fw-semibold">Tempat Lahir</label>
                                                <input type="text" class="form-control @error('tempat_lahir_ayah') is-invalid @enderror" id="tempat_lahir_ayah" name="tempat_lahir_ayah" 
                                                       value="{{ $existingData->tempat_lahir_ayah ?? old('tempat_lahir_ayah') }}" 
                                                       placeholder="Tempat lahir ayah">
                                                @error('tempat_lahir_ayah')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="tanggal_lahir_ayah" class="form-label fw-semibold">Tanggal Lahir</label>
                                                <input type="date" class="form-control @error('tanggal_lahir_ayah') is-invalid @enderror" id="tanggal_lahir_ayah" name="tanggal_lahir_ayah" 
                                                       value="{{ isset($existingData->tanggal_lahir_ayah) ? \Carbon\Carbon::parse($existingData->tanggal_lahir_ayah)->format('Y-m-d') : old('tanggal_lahir_ayah') }}">
                                                @error('tanggal_lahir_ayah')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="no_hp_ayah" class="form-label fw-semibold">No. HP Ayah</label>
                                                <input type="text" class="form-control @error('no_hp_ayah') is-invalid @enderror" id="no_hp_ayah" name="no_hp_ayah" 
                                                       value="{{ $existingData->no_hp_ayah ?? old('no_hp_ayah') }}" 
                                                       placeholder="No. telepon ayah">
                                                @error('no_hp_ayah')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="pendidikan_ayah" class="form-label fw-semibold">Pendidikan Terakhir</label>
                                                <select class="form-select @error('pendidikan_ayah') is-invalid @enderror" id="pendidikan_ayah" name="pendidikan_ayah">
                                                    <option value="">Pilih Pendidikan</option>
                                                    <option value="Tidak Sekolah" {{ ($existingData->pendidikan_ayah ?? old('pendidikan_ayah')) == 'Tidak Sekolah' ? 'selected' : '' }}>Tidak Sekolah</option>
                                                    <option value="SD/Sederajat" {{ ($existingData->pendidikan_ayah ?? old('pendidikan_ayah')) == 'SD/Sederajat' ? 'selected' : '' }}>SD/Sederajat</option>
                                                    <option value="SMP/Sederajat" {{ ($existingData->pendidikan_ayah ?? old('pendidikan_ayah')) == 'SMP/Sederajat' ? 'selected' : '' }}>SMP/Sederajat</option>
                                                    <option value="SMA/Sederajat" {{ ($existingData->pendidikan_ayah ?? old('pendidikan_ayah')) == 'SMA/Sederajat' ? 'selected' : '' }}>SMA/Sederajat</option>
                                                    <option value="D1" {{ ($existingData->pendidikan_ayah ?? old('pendidikan_ayah')) == 'D1' ? 'selected' : '' }}>D1</option>
                                                    <option value="D2" {{ ($existingData->pendidikan_ayah ?? old('pendidikan_ayah')) == 'D2' ? 'selected' : '' }}>D2</option>
                                                    <option value="D3" {{ ($existingData->pendidikan_ayah ?? old('pendidikan_ayah')) == 'D3' ? 'selected' : '' }}>D3</option>
                                                    <option value="D4/S1" {{ ($existingData->pendidikan_ayah ?? old('pendidikan_ayah')) == 'D4/S1' ? 'selected' : '' }}>D4/S1</option>
                                                    <option value="S2" {{ ($existingData->pendidikan_ayah ?? old('pendidikan_ayah')) == 'S2' ? 'selected' : '' }}>S2</option>
                                                    <option value="S3" {{ ($existingData->pendidikan_ayah ?? old('pendidikan_ayah')) == 'S3' ? 'selected' : '' }}>S3</option>
                                                </select>
                                                @error('pendidikan_ayah')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="pekerjaan_ayah" class="form-label fw-semibold">Pekerjaan</label>
                                                <select class="form-select @error('pekerjaan_ayah') is-invalid @enderror" id="pekerjaan_ayah" name="pekerjaan_ayah">
                                                    <option value="">Pilih Pekerjaan</option>
                                                    <option value="Tidak Bekerja" {{ ($existingData->pekerjaan_ayah ?? old('pekerjaan_ayah')) == 'Tidak Bekerja' ? 'selected' : '' }}>Tidak Bekerja</option>
                                                    <option value="PNS" {{ ($existingData->pekerjaan_ayah ?? old('pekerjaan_ayah')) == 'PNS' ? 'selected' : '' }}>PNS</option>
                                                    <option value="TNI/Polri" {{ ($existingData->pekerjaan_ayah ?? old('pekerjaan_ayah')) == 'TNI/Polri' ? 'selected' : '' }}>TNI/Polri</option>
                                                    <option value="Karyawan Swasta" {{ ($existingData->pekerjaan_ayah ?? old('pekerjaan_ayah')) == 'Karyawan Swasta' ? 'selected' : '' }}>Karyawan Swasta</option>
                                                    <option value="Wiraswasta" {{ ($existingData->pekerjaan_ayah ?? old('pekerjaan_ayah')) == 'Wiraswasta' ? 'selected' : '' }}>Wiraswasta</option>
                                                    <option value="Petani" {{ ($existingData->pekerjaan_ayah ?? old('pekerjaan_ayah')) == 'Petani' ? 'selected' : '' }}>Petani</option>
                                                    <option value="Nelayan" {{ ($existingData->pekerjaan_ayah ?? old('pekerjaan_ayah')) == 'Nelayan' ? 'selected' : '' }}>Nelayan</option>
                                                    <option value="Pensiunan" {{ ($existingData->pekerjaan_ayah ?? old('pekerjaan_ayah')) == 'Pensiunan' ? 'selected' : '' }}>Pensiunan</option>
                                                    <option value="Lainnya" {{ ($existingData->pekerjaan_ayah ?? old('pekerjaan_ayah')) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                                </select>
                                                @error('pekerjaan_ayah')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="penghasilan_ayah" class="form-label fw-semibold">Penghasilan</label>
                                                <select class="form-select @error('penghasilan_ayah') is-invalid @enderror" id="penghasilan_ayah" name="penghasilan_ayah">
                                                    <option value="">Pilih Penghasilan</option>
                                                    <option value="< Rp 1.000.000" {{ ($existingData->penghasilan_ayah ?? old('penghasilan_ayah')) == '< Rp 1.000.000' ? 'selected' : '' }}>< Rp 1.000.000</option>
                                                    <option value="Rp 1.000.000 - Rp 2.000.000" {{ ($existingData->penghasilan_ayah ?? old('penghasilan_ayah')) == 'Rp 1.000.000 - Rp 2.000.000' ? 'selected' : '' }}>Rp 1.000.000 - Rp 2.000.000</option>
                                                    <option value="Rp 2.000.000 - Rp 3.000.000" {{ ($existingData->penghasilan_ayah ?? old('penghasilan_ayah')) == 'Rp 2.000.000 - Rp 3.000.000' ? 'selected' : '' }}>Rp 2.000.000 - Rp 3.000.000</option>
                                                    <option value="Rp 3.000.000 - Rp 5.000.000" {{ ($existingData->penghasilan_ayah ?? old('penghasilan_ayah')) == 'Rp 3.000.000 - Rp 5.000.000' ? 'selected' : '' }}>Rp 3.000.000 - Rp 5.000.000</option>
                                                    <option value="> Rp 5.000.000" {{ ($existingData->penghasilan_ayah ?? old('penghasilan_ayah')) == '> Rp 5.000.000' ? 'selected' : '' }}>> Rp 5.000.000</option>
                                                </select>
                                                @error('penghasilan_ayah')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Ibu -->
                            <div class="parent-section mb-5">
                                <div class="section-header-bg info">
                                    <h5 class="mb-0">
                                        <i class="fas fa-female me-2"></i>Data Ibu Kandung
                                    </h5>
                                </div>
                                <div class="section-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="nik_ibu" class="form-label fw-semibold">NIK Ibu</label>
                                                <input type="text" class="form-control @error('nik_ibu') is-invalid @enderror" id="nik_ibu" name="nik_ibu" 
                                                       value="{{ $existingData->nik_ibu ?? old('nik_ibu') }}" 
                                                       placeholder="Masukkan NIK ibu">
                                                @error('nik_ibu')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="nama_ibu" class="form-label fw-semibold">Nama Lengkap Ibu</label>
                                                <input type="text" class="form-control @error('nama_ibu') is-invalid @enderror" id="nama_ibu" name="nama_ibu" 
                                                       value="{{ $existingData->nama_ibu ?? old('nama_ibu') }}" 
                                                       placeholder="Nama lengkap ibu">
                                                @error('nama_ibu')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="tempat_lahir_ibu" class="form-label fw-semibold">Tempat Lahir</label>
                                                <input type="text" class="form-control @error('tempat_lahir_ibu') is-invalid @enderror" id="tempat_lahir_ibu" name="tempat_lahir_ibu" 
                                                       value="{{ $existingData->tempat_lahir_ibu ?? old('tempat_lahir_ibu') }}" 
                                                       placeholder="Tempat lahir ibu">
                                                @error('tempat_lahir_ibu')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="tanggal_lahir_ibu" class="form-label fw-semibold">Tanggal Lahir</label>
                                                <input type="date" class="form-control @error('tanggal_lahir_ibu') is-invalid @enderror" id="tanggal_lahir_ibu" name="tanggal_lahir_ibu" 
                                                       value="{{ isset($existingData->tanggal_lahir_ibu) ? \Carbon\Carbon::parse($existingData->tanggal_lahir_ibu)->format('Y-m-d') : old('tanggal_lahir_ibu') }}">
                                                @error('tanggal_lahir_ibu')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="no_hp_ibu" class="form-label fw-semibold">No. HP Ibu</label>
                                                <input type="text" class="form-control @error('no_hp_ibu') is-invalid @enderror" id="no_hp_ibu" name="no_hp_ibu" 
                                                       value="{{ $existingData->no_hp_ibu ?? old('no_hp_ibu') }}" 
                                                       placeholder="No. telepon ibu">
                                                @error('no_hp_ibu')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="pendidikan_ibu" class="form-label fw-semibold">Pendidikan Terakhir</label>
                                                <select class="form-select @error('pendidikan_ibu') is-invalid @enderror" id="pendidikan_ibu" name="pendidikan_ibu">
                                                    <option value="">Pilih Pendidikan</option>
                                                    <option value="Tidak Sekolah" {{ ($existingData->pendidikan_ibu ?? old('pendidikan_ibu')) == 'Tidak Sekolah' ? 'selected' : '' }}>Tidak Sekolah</option>
                                                    <option value="SD/Sederajat" {{ ($existingData->pendidikan_ibu ?? old('pendidikan_ibu')) == 'SD/Sederajat' ? 'selected' : '' }}>SD/Sederajat</option>
                                                    <option value="SMP/Sederajat" {{ ($existingData->pendidikan_ibu ?? old('pendidikan_ibu')) == 'SMP/Sederajat' ? 'selected' : '' }}>SMP/Sederajat</option>
                                                    <option value="SMA/Sederajat" {{ ($existingData->pendidikan_ibu ?? old('pendidikan_ibu')) == 'SMA/Sederajat' ? 'selected' : '' }}>SMA/Sederajat</option>
                                                    <option value="D1" {{ ($existingData->pendidikan_ibu ?? old('pendidikan_ibu')) == 'D1' ? 'selected' : '' }}>D1</option>
                                                    <option value="D2" {{ ($existingData->pendidikan_ibu ?? old('pendidikan_ibu')) == 'D2' ? 'selected' : '' }}>D2</option>
                                                    <option value="D3" {{ ($existingData->pendidikan_ibu ?? old('pendidikan_ibu')) == 'D3' ? 'selected' : '' }}>D3</option>
                                                    <option value="D4/S1" {{ ($existingData->pendidikan_ibu ?? old('pendidikan_ibu')) == 'D4/S1' ? 'selected' : '' }}>D4/S1</option>
                                                    <option value="S2" {{ ($existingData->pendidikan_ibu ?? old('pendidikan_ibu')) == 'S2' ? 'selected' : '' }}>S2</option>
                                                    <option value="S3" {{ ($existingData->pendidikan_ibu ?? old('pendidikan_ibu')) == 'S3' ? 'selected' : '' }}>S3</option>
                                                </select>
                                                @error('pendidikan_ibu')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="pekerjaan_ibu" class="form-label fw-semibold">Pekerjaan</label>
                                                <select class="form-select @error('pekerjaan_ibu') is-invalid @enderror" id="pekerjaan_ibu" name="pekerjaan_ibu">
                                                    <option value="">Pilih Pekerjaan</option>
                                                    <option value="Tidak Bekerja" {{ ($existingData->pekerjaan_ibu ?? old('pekerjaan_ibu')) == 'Tidak Bekerja' ? 'selected' : '' }}>Tidak Bekerja</option>
                                                    <option value="PNS" {{ ($existingData->pekerjaan_ibu ?? old('pekerjaan_ibu')) == 'PNS' ? 'selected' : '' }}>PNS</option>
                                                    <option value="TNI/Polri" {{ ($existingData->pekerjaan_ibu ?? old('pekerjaan_ibu')) == 'TNI/Polri' ? 'selected' : '' }}>TNI/Polri</option>
                                                    <option value="Karyawan Swasta" {{ ($existingData->pekerjaan_ibu ?? old('pekerjaan_ibu')) == 'Karyawan Swasta' ? 'selected' : '' }}>Karyawan Swasta</option>
                                                    <option value="Wiraswasta" {{ ($existingData->pekerjaan_ibu ?? old('pekerjaan_ibu')) == 'Wiraswasta' ? 'selected' : '' }}>Wiraswasta</option>
                                                    <option value="Petani" {{ ($existingData->pekerjaan_ibu ?? old('pekerjaan_ibu')) == 'Petani' ? 'selected' : '' }}>Petani</option>
                                                    <option value="Nelayan" {{ ($existingData->pekerjaan_ibu ?? old('pekerjaan_ibu')) == 'Nelayan' ? 'selected' : '' }}>Nelayan</option>
                                                    <option value="Pensiunan" {{ ($existingData->pekerjaan_ibu ?? old('pekerjaan_ibu')) == 'Pensiunan' ? 'selected' : '' }}>Pensiunan</option>
                                                    <option value="Lainnya" {{ ($existingData->pekerjaan_ibu ?? old('pekerjaan_ibu')) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                                </select>
                                                @error('pekerjaan_ibu')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="penghasilan_ibu" class="form-label fw-semibold">Penghasilan</label>
                                                <select class="form-select @error('penghasilan_ibu') is-invalid @enderror" id="penghasilan_ibu" name="penghasilan_ibu">
                                                    <option value="">Pilih Penghasilan</option>
                                                    <option value="< Rp 1.000.000" {{ ($existingData->penghasilan_ibu ?? old('penghasilan_ibu')) == '< Rp 1.000.000' ? 'selected' : '' }}>< Rp 1.000.000</option>
                                                    <option value="Rp 1.000.000 - Rp 2.000.000" {{ ($existingData->penghasilan_ibu ?? old('penghasilan_ibu')) == 'Rp 1.000.000 - Rp 2.000.000' ? 'selected' : '' }}>Rp 1.000.000 - Rp 2.000.000</option>
                                                    <option value="Rp 2.000.000 - Rp 3.000.000" {{ ($existingData->penghasilan_ibu ?? old('penghasilan_ibu')) == 'Rp 2.000.000 - Rp 3.000.000' ? 'selected' : '' }}>Rp 2.000.000 - Rp 3.000.000</option>
                                                    <option value="Rp 3.000.000 - Rp 5.000.000" {{ ($existingData->penghasilan_ibu ?? old('penghasilan_ibu')) == 'Rp 3.000.000 - Rp 5.000.000' ? 'selected' : '' }}>Rp 3.000.000 - Rp 5.000.000</option>
                                                    <option value="> Rp 5.000.000" {{ ($existingData->penghasilan_ibu ?? old('penghasilan_ibu')) == '> Rp 5.000.000' ? 'selected' : '' }}>> Rp 5.000.000</option>
                                                </select>
                                                @error('penghasilan_ibu')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Wali -->
                            <div class="parent-section mb-5">
                                <div class="section-header-bg warning">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user-tie me-2"></i>Data Wali
                                    </h5>
                                </div>
                                <div class="section-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="nik_wali" class="form-label fw-semibold">NIK Wali</label>
                                                <input type="text" class="form-control @error('nik_wali') is-invalid @enderror" id="nik_wali" name="nik_wali" 
                                                       value="{{ $existingData->nik_wali ?? old('nik_wali') }}" 
                                                       placeholder="Masukkan NIK wali">
                                                @error('nik_wali')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="nama_wali" class="form-label fw-semibold">Nama Lengkap Wali</label>
                                                <input type="text" class="form-control @error('nama_wali') is-invalid @enderror" id="nama_wali" name="nama_wali" 
                                                       value="{{ $existingData->nama_wali ?? old('nama_wali') }}" 
                                                       placeholder="Nama lengkap wali">
                                                @error('nama_wali')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="tempat_lahir_wali" class="form-label fw-semibold">Tempat Lahir</label>
                                                <input type="text" class="form-control @error('tempat_lahir_wali') is-invalid @enderror" id="tempat_lahir_wali" name="tempat_lahir_wali" 
                                                       value="{{ $existingData->tempat_lahir_wali ?? old('tempat_lahir_wali') }}" 
                                                       placeholder="Tempat lahir wali">
                                                @error('tempat_lahir_wali')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="tanggal_lahir_wali" class="form-label fw-semibold">Tanggal Lahir</label>
                                                <input type="date" class="form-control @error('tanggal_lahir_wali') is-invalid @enderror" id="tanggal_lahir_wali" name="tanggal_lahir_wali" 
                                                       value="{{ isset($existingData->tanggal_lahir_wali) ? \Carbon\Carbon::parse($existingData->tanggal_lahir_wali)->format('Y-m-d') : old('tanggal_lahir_wali') }}">
                                                @error('tanggal_lahir_wali')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="no_hp_wali" class="form-label fw-semibold">No. HP Wali</label>
                                                <input type="text" class="form-control @error('no_hp_wali') is-invalid @enderror" id="no_hp_wali" name="no_hp_wali" 
                                                       value="{{ $existingData->no_hp_wali ?? old('no_hp_wali') }}" 
                                                       placeholder="No. telepon wali">
                                                @error('no_hp_wali')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="pendidikan_wali" class="form-label fw-semibold">Pendidikan Terakhir</label>
                                                <select class="form-select @error('pendidikan_wali') is-invalid @enderror" id="pendidikan_wali" name="pendidikan_wali">
                                                    <option value="">Pilih Pendidikan</option>
                                                    <option value="Tidak Sekolah" {{ ($existingData->pendidikan_wali ?? old('pendidikan_wali')) == 'Tidak Sekolah' ? 'selected' : '' }}>Tidak Sekolah</option>
                                                    <option value="SD/Sederajat" {{ ($existingData->pendidikan_wali ?? old('pendidikan_wali')) == 'SD/Sederajat' ? 'selected' : '' }}>SD/Sederajat</option>
                                                    <option value="SMP/Sederajat" {{ ($existingData->pendidikan_wali ?? old('pendidikan_wali')) == 'SMP/Sederajat' ? 'selected' : '' }}>SMP/Sederajat</option>
                                                    <option value="SMA/Sederajat" {{ ($existingData->pendidikan_wali ?? old('pendidikan_wali')) == 'SMA/Sederajat' ? 'selected' : '' }}>SMA/Sederajat</option>
                                                    <option value="D1" {{ ($existingData->pendidikan_wali ?? old('pendidikan_wali')) == 'D1' ? 'selected' : '' }}>D1</option>
                                                    <option value="D2" {{ ($existingData->pendidikan_wali ?? old('pendidikan_wali')) == 'D2' ? 'selected' : '' }}>D2</option>
                                                    <option value="D3" {{ ($existingData->pendidikan_wali ?? old('pendidikan_wali')) == 'D3' ? 'selected' : '' }}>D3</option>
                                                    <option value="D4/S1" {{ ($existingData->pendidikan_wali ?? old('pendidikan_wali')) == 'D4/S1' ? 'selected' : '' }}>D4/S1</option>
                                                    <option value="S2" {{ ($existingData->pendidikan_wali ?? old('pendidikan_wali')) == 'S2' ? 'selected' : '' }}>S2</option>
                                                    <option value="S3" {{ ($existingData->pendidikan_wali ?? old('pendidikan_wali')) == 'S3' ? 'selected' : '' }}>S3</option>
                                                </select>
                                                @error('pendidikan_wali')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="pekerjaan_wali" class="form-label fw-semibold">Pekerjaan</label>
                                                <select class="form-select @error('pekerjaan_wali') is-invalid @enderror" id="pekerjaan_wali" name="pekerjaan_wali">
                                                    <option value="">Pilih Pekerjaan</option>
                                                    <option value="Tidak Bekerja" {{ ($existingData->pekerjaan_wali ?? old('pekerjaan_wali')) == 'Tidak Bekerja' ? 'selected' : '' }}>Tidak Bekerja</option>
                                                    <option value="PNS" {{ ($existingData->pekerjaan_wali ?? old('pekerjaan_wali')) == 'PNS' ? 'selected' : '' }}>PNS</option>
                                                    <option value="TNI/Polri" {{ ($existingData->pekerjaan_wali ?? old('pekerjaan_wali')) == 'TNI/Polri' ? 'selected' : '' }}>TNI/Polri</option>
                                                    <option value="Karyawan Swasta" {{ ($existingData->pekerjaan_wali ?? old('pekerjaan_wali')) == 'Karyawan Swasta' ? 'selected' : '' }}>Karyawan Swasta</option>
                                                    <option value="Wiraswasta" {{ ($existingData->pekerjaan_wali ?? old('pekerjaan_wali')) == 'Wiraswasta' ? 'selected' : '' }}>Wiraswasta</option>
                                                    <option value="Petani" {{ ($existingData->pekerjaan_wali ?? old('pekerjaan_wali')) == 'Petani' ? 'selected' : '' }}>Petani</option>
                                                    <option value="Nelayan" {{ ($existingData->pekerjaan_wali ?? old('pekerjaan_wali')) == 'Nelayan' ? 'selected' : '' }}>Nelayan</option>
                                                    <option value="Pensiunan" {{ ($existingData->pekerjaan_wali ?? old('pekerjaan_wali')) == 'Pensiunan' ? 'selected' : '' }}>Pensiunan</option>
                                                    <option value="Lainnya" {{ ($existingData->pekerjaan_wali ?? old('pekerjaan_wali')) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                                </select>
                                                @error('pekerjaan_wali')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="penghasilan_wali" class="form-label fw-semibold">Penghasilan</label>
                                                <select class="form-select @error('penghasilan_wali') is-invalid @enderror" id="penghasilan_wali" name="penghasilan_wali">
                                                    <option value="">Pilih Penghasilan</option>
                                                    <option value="< Rp 1.000.000" {{ ($existingData->penghasilan_wali ?? old('penghasilan_wali')) == '< Rp 1.000.000' ? 'selected' : '' }}>< Rp 1.000.000</option>
                                                    <option value="Rp 1.000.000 - Rp 2.000.000" {{ ($existingData->penghasilan_wali ?? old('penghasilan_wali')) == 'Rp 1.000.000 - Rp 2.000.000' ? 'selected' : '' }}>Rp 1.000.000 - Rp 2.000.000</option>
                                                    <option value="Rp 2.000.000 - Rp 3.000.000" {{ ($existingData->penghasilan_wali ?? old('penghasilan_wali')) == 'Rp 2.000.000 - Rp 3.000.000' ? 'selected' : '' }}>Rp 2.000.000 - Rp 3.000.000</option>
                                                    <option value="Rp 3.000.000 - Rp 5.000.000" {{ ($existingData->penghasilan_wali ?? old('penghasilan_wali')) == 'Rp 3.000.000 - Rp 5.000.000' ? 'selected' : '' }}>Rp 3.000.000 - Rp 5.000.000</option>
                                                    <option value="> Rp 5.000.000" {{ ($existingData->penghasilan_wali ?? old('penghasilan_wali')) == '> Rp 5.000.000' ? 'selected' : '' }}>> Rp 5.000.000</option>
                                                </select>
                                                @error('penghasilan_wali')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions mt-5 d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary btn-lg prev-tab" data-prev="data-alamat">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </button>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Formulir
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* CSS styles remain the same as previous version */
.gradient-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.profile-card {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.profile-card:hover {
    transform: translateY(-5px);
}

.profile-header {
    background: linear-gradient(135deg, #3498db, #2c3e50);
    padding: 30px 20px;
    text-align: center;
    color: white;
}

.profile-img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 5px solid rgba(255, 255, 255, 0.3);
    margin: 0 auto 15px;
    overflow: hidden;
    background-color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile-img i {
    font-size: 50px;
    color: #3498db;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
}

.status-pending {
    background-color: rgba(220, 53, 69, 0.2);
    color: #721c24;
}

.status-completed {
    background-color: rgba(40, 167, 69, 0.2);
    color: #155724;
}

.status-almost {
    background-color: rgba(23, 162, 184, 0.2);
    color: #0c5460;
}

.status-progress {
    background-color: rgba(255, 193, 7, 0.2);
    color: #856404;
}

.wave-badge {
    background: linear-gradient(45deg, #ff9a9e, #fad0c4);
    color: white;
    font-weight: bold;
    padding: 5px 15px;
    border-radius: 20px;
    margin-top: 10px;
    display: inline-block;
}

.progress-card {
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.progress-title {
    font-weight: 600;
    margin-bottom: 15px;
    color: #2c3e50;
    display: flex;
    align-items: center;
}

.progress-title i {
    margin-right: 10px;
    color: #3498db;
}

.progress-item {
    margin-bottom: 15px;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
    font-size: 14px;
}

.progress-bar-custom {
    height: 8px;
    border-radius: 10px;
    background-color: #e9ecef;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    border-radius: 10px;
    background: linear-gradient(90deg, #3498db, #1abc9c);
    transition: width 0.5s ease;
}

.form-container {
    background-color: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.form-tabs {
    display: flex;
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.form-tab {
    flex: 1;
    text-align: center;
    padding: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    border-bottom: 3px solid transparent;
    font-weight: 600;
    color: #343a40;
}

.form-tab.active {
    border-bottom: 3px solid #3498db;
    color: #3498db;
    background-color: rgba(52, 152, 219, 0.05);
}

.form-tab i {
    margin-right: 8px;
}

.form-tab:hover:not(.active) {
    background-color: rgba(0, 0, 0, 0.03);
}

.tab-pane {
    display: none;
    animation: fadeIn 0.5s ease;
}

.tab-pane.active {
    display: block;
}

.section-header {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.section-header-bg {
    background-color: #3498db;
    color: white;
    padding: 15px 20px;
}

.section-header-bg.info {
    background-color: #1abc9c;
}

.section-header-bg.warning {
    background-color: #a4bc1a;
}

.section-body {
    padding: 20px;
}

.step-header {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 20px;
}

.form-group {
    position: relative;
}

.form-control, .form-select {
    border-radius: 12px;
    border: 2px solid #e9ecef;
    padding: 12px 16px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    transform: translateY(-2px);
}

.parent-section {
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 25px;
    border: 1px solid #e9ecef;
}

.btn {
    border-radius: 12px;
    padding: 12px 30px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.btn-primary {
    background: linear-gradient(135deg, #3498db, #2980b9);
    border: none;
}

.btn-success {
    background: linear-gradient(135deg, #1abc9c, #16a085);
    border: none;
}

.form-actions {
    margin-top: 30px;
    display: flex;
    justify-content: space-between;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .profile-img {
        width: 100px;
        height: 100px;
    }
    
    .profile-img i {
        font-size: 40px;
    }
    
    .form-tab {
        padding: 12px 8px;
        font-size: 14px;
    }
    
    .form-tab i {
        margin-right: 5px;
    }
    
    .card-body.p-4 {
        padding: 1.5rem !important;
    }
}

/* Error styling */
.is-invalid {
    border-color: #dc3545 !important;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}
</style>

<script>
// JavaScript code remains the same as previous version
document.addEventListener('DOMContentLoaded', function() {
    const formTabs = document.querySelectorAll('.form-tab');
    const tabPanes = document.querySelectorAll('.tab-pane');
    const nextButtons = document.querySelectorAll('.next-tab');
    const prevButtons = document.querySelectorAll('.prev-tab');

    // Tab navigation
    formTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Update active tab
            formTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Show target tab content
            tabPanes.forEach(pane => pane.classList.remove('active'));
            document.getElementById(targetTab).classList.add('active');
            
            // Scroll to top of form
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    // Next button functionality
    nextButtons.forEach(button => {
        button.addEventListener('click', function() {
            const currentTab = this.closest('.tab-pane');
            const nextTabId = this.getAttribute('data-next');
            
            // Validation for current tab
            if (currentTab.id === 'data-diri') {
                const namaLengkap = document.getElementById('nama_lengkap').value;
                if (!namaLengkap.trim()) {
                    showAlert('error', 'Nama Lengkap harus diisi!');
                    document.getElementById('nama_lengkap').focus();
                    return;
                }
            }
            
            // Switch to next tab
            formTabs.forEach(tab => {
                tab.classList.remove('active');
                if (tab.getAttribute('data-tab') === nextTabId) {
                    tab.classList.add('active');
                }
            });
            
            tabPanes.forEach(pane => pane.classList.remove('active'));
            document.getElementById(nextTabId).classList.add('active');
            
            // Update progress (you can implement this based on your needs)
            updateProgress(nextTabId);
            
            // Scroll to top of form
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    // Previous button functionality
    prevButtons.forEach(button => {
        button.addEventListener('click', function() {
            const prevTabId = this.getAttribute('data-prev');
            
            // Switch to previous tab
            formTabs.forEach(tab => {
                tab.classList.remove('active');
                if (tab.getAttribute('data-tab') === prevTabId) {
                    tab.classList.add('active');
                }
            });
            
            tabPanes.forEach(pane => pane.classList.remove('active'));
            document.getElementById(prevTabId).classList.add('active');
            
            // Scroll to top of form
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    function showAlert(type, message) {
        // Simple alert implementation - you can replace with SweetAlert or similar
        alert(message);
    }

    function updateProgress(tabId) {
        // Update progress bars based on current tab
        const progressItems = document.querySelectorAll('.progress-fill');
        
        if (tabId === 'data-diri') {
            progressItems[0].style.width = '{{ $progress['data_diri'] }}';
            document.querySelector('.progress-item:nth-child(1) .progress-label span:last-child').textContent = '{{ $progress['data_diri'] }}';
        } else if (tabId === 'data-alamat') {
            progressItems[1].style.width = '{{$progress['alamat']}}';
            document.querySelector('.progress-item:nth-child(2) .progress-label span:last-child').textContent = '{{$progress['alamat']}}';
        } else if (tabId === 'data-orangtua') {
            progressItems[2].style.width = '{{$progress['orangtua']}}';
            document.querySelector('.progress-item:nth-child(3) .progress-label span:last-child').textContent = '{{$progress['orangtua']}}';
        }
        
        
    }

    // Initialize progress
    updateProgress('data-diri');
});
</script>
@endsection