<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pendaftaran PPDB SMK Wisata Indonesia</title>
    <link rel="stylesheet" href="{{ asset('sneat/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            min-height: 100vh;
            position: relative;
        }
        
        .radial-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }
        
        .radial-1 {
            position: absolute;
            top: -20%;
            right: -10%;
            width: 60%;
            height: 60%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }
        
        .radial-2 {
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 70%;
            height: 70%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 10s ease-in-out infinite reverse;
        }
        
        .radial-3 {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40%;
            height: 40%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 60%);
            border-radius: 50%;
            animation: pulse 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-20px) scale(1.05); }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 0.5; transform: translate(-50%, -50%) scale(1); }
            50% { opacity: 0.8; transform: translate(-50%, -50%) scale(1.1); }
        }
        
        .form-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            padding: 30px;
            margin: 0 auto;
            max-width: 550px;
            position: relative;
        }
        
        .form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #16a34a, #15803d, #166534);
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            position: relative;
        }
        
        .form-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 25%;
            right: 25%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #6b21a8, transparent);
        }
        
        .form-header h1 {
            color: #2E004F;
            font-weight: 700;
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .form-header p {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 0;
        }
        
        .form-label {
            font-weight: 600;
            color: #2E004F;
            margin-bottom: 6px;
            font-size: 14px;
        }
        
        .form-control, .form-select {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 13px;
            transition: all 0.3s;
            height: 38px;
            background-color: #fafafa;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #6b21a8;
            box-shadow: 0 0 0 3px rgba(107, 33, 168, 0.1);
            background-color: white;
        }
        
        .form-control::placeholder {
            color: #9ca3af;
            font-size: 13px;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 25px;
            font-weight: 600;
            font-size: 14px;
            width: 100%;
            transition: all 0.3s;
            height: 42px;
            position: relative;
            overflow: hidden;
        }
        
        .btn-submit:hover {
            background: linear-gradient(135deg, #15803d, #166534);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(22, 163, 74, 0.4);
        }
        
        .btn-back {
            background: linear-gradient(135deg, #6b7280, #4b5563);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            font-weight: 500;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.3s;
            width: 100%;
            height: 42px;
        }
        
        .btn-back:hover {
            background: linear-gradient(135deg, #4b5563, #374151);
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(107, 114, 128, 0.3);
            color: white;
            text-decoration: none;
        }
        
        .example-text {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }
        
        .required::after {
            content: " *";
            color: #e53e3e;
        }
        
        .row {
            margin-bottom: 15px !important;
        }
        
        .mb-3 {
            margin-bottom: 15px !important;
        }
        
        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        
        .button-group .btn-back,
        .button-group .btn-submit {
            flex: 1;
            margin-top: 0;
        }
        
        .form-container {
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .btn-submit.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn-submit.loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-right-color: transparent;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .alert {
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 15px;
            font-size: 13px;
        }

        .alert-success {
            background-color: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .alert-danger {
            background-color: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-warning {
            background-color: #fef3c7;
            border: 1px solid #fde68a;
            color: #92400e;
        }

        .alert-info {
            background-color: #dbeafe;
            border: 1px solid #93c5fd;
            color: #1e40af;
        }

        .is-valid {
            border-color: #198754 !important;
            box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.1) !important;
            background-color: #f8fff9 !important;
        }

        .valid-feedback {
            display: block;
            width: 100%;
            margin-top: 4px;
            font-size: 11px;
            font-weight: 500;
            color: #198754;
        }

        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1) !important;
            background-color: #fff8f8 !important;
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 4px;
            font-size: 11px;
            font-weight: 500;
            color: #dc3545;
        }

        .success-icon {
            font-size: 4rem;
            color: #198754;
            margin-bottom: 1rem;
        }

        .modal-success {
            border: none;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            background-color: #ffffff
        }

        .modal-success .modal-header {
            background: linear-gradient(135deg, #16a34a, #15803d);
            border-bottom: none;
            border-radius: 12px 12px 0 0;
            padding: 1.5rem;
        }

        .modal-success .modal-title {
            font-weight: 700;
            font-size: 1.2rem;
        }

        .modal-success .modal-body {
            padding: 2rem;
            text-align: center;
        }

        .modal-success .modal-footer {
            border-top: 1px solid #e9ecef;
            border-radius: 0 0 12px 12px;
            padding: 1.5rem;
        }

        #asal_sekolah_lain_container {
            display: none;
            margin-top: 10px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 576px) {
            body { padding: 15px; }
            .form-container {
                padding: 20px 15px;
                margin: 0 auto;
                max-width: 100%;
                width: 100%;
                box-sizing: border-box;
            }
            .button-group { flex-direction: column; gap: 10px; }
            .button-group .btn-back, .button-group .btn-submit { width: 100%; }
            .radial-1, .radial-2, .radial-3 { display: none; }
            .modal-success .modal-body { padding: 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="radial-bg">
        <div class="radial-1"></div>
        <div class="radial-2"></div>
        <div class="radial-3"></div>
    </div>
    
    <div class="form-container">
        <div class="form-header">
            <h1>Formulir Pendaftaran SPMB</h1>
            <p>SMK Wisata Indonesia TP. 2026-2027</p>
        </div>

        <!-- Alert Area -->
        <div id="alertArea"></div>
        
        <form id="pendaftaranForm">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nisn" class="form-label required">NISN</label>
                    <input type="text" class="form-control" id="nisn" name="nisn" placeholder="Masukkan NISN" required>
                    <div class="invalid-feedback" id="nisn-error"></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="jenis_kelamin" class="form-label required">Jenis Kelamin</label>
                    <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                        <option value="" selected disabled>--Jenis Kelamin--</option>
                        <option value="Laki-Laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                    <div class="invalid-feedback" id="jenis-kelamin-error"></div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12 mb-3">
                    <label for="nama_lengkap" class="form-label required">Nama</label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan Nama Lengkap" required>
                    <div class="invalid-feedback" id="nama-lengkap-error"></div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12 mb-3">
                    <label for="asal_sekolah" class="form-label required">Asal Sekolah</label>
                    <select class="form-select" id="asal_sekolah" name="asal_sekolah" required>
                        <option value="" selected disabled>--Pilih Asal Sekolah--</option>
                        <option value="SMP Negeri 1 Bogor">SMP Negeri 1 Bogor</option>
                        <option value="SMP Negeri 2 Bogor">SMP Negeri 2 Bogor</option>
                        <option value="SMP Negeri 3 Bogor">SMP Negeri 3 Bogor</option>
                        <option value="SMP Negeri 4 Bogor">SMP Negeri 4 Bogor</option>
                        <option value="SMP Negeri 5 Bogor">SMP Negeri 5 Bogor</option>
                        <option value="SMP Negeri 6 Bogor">SMP Negeri 6 Bogor</option>
                        <option value="SMP Negeri 7 Bogor">SMP Negeri 7 Bogor</option>
                        <option value="SMP Negeri 8 Bogor">SMP Negeri 8 Bogor</option>
                        <option value="SMP Negeri 9 Bogor">SMP Negeri 9 Bogor</option>
                        <option value="SMP Negeri 10 Bogor">SMP Negeri 10 Bogor</option>
                        <option value="SMP Lainnya">SMP Lainnya</option>
                    </select>
                    <div class="invalid-feedback" id="asal-sekolah-error"></div>
                    
                    <div id="asal_sekolah_lain_container" class="mt-4">
                        <label for="asal_sekolah_lain" class="form-label required">Nama SMP Lainnya</label>
                        <input type="text" class="form-control" id="asal_sekolah_lain" name="asal_sekolah_lain" placeholder="Masukkan Nama SMP Anda">
                        <div class="invalid-feedback" id="asal-sekolah-lain-error"></div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12 mb-3">
                    <label for="email" class="form-label required">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan Email Aktif" required>
                    <div class="invalid-feedback" id="email-error"></div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12 mb-3">
                    <label for="no_hp" class="form-label required">Nomor Handphone</label>
                    <input type="tel" class="form-control" id="no_hp" name="no_hp" placeholder="Contoh : 08---" required>
                    <div class="example-text">Pastikan nomor aktif untuk verifikasi</div>
                    <div class="invalid-feedback" id="no-hp-error"></div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="no_hp_ayah" class="form-label required">Nomor HP Ayah</label>
                    <input type="tel" class="form-control" id="no_hp_ayah" name="no_hp_ayah" placeholder="Contoh : 08---" required>
                    <div class="invalid-feedback" id="no-hp-ayah-error"></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="no_hp_ibu" class="form-label required">Nomor HP Ibu</label>
                    <input type="tel" class="form-control" id="no_hp_ibu" name="no_hp_ibu" placeholder="Contoh : 08---" required>
                    <div class="invalid-feedback" id="no-hp-ibu-error"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="referensi" class="form-label required">Referensi</label>
                    <select class="form-select" id="referensi" name="referensi" required>
                        <option value="" selected disabled>--Pilih Referensi--</option>
                        <option value="guru-staff">Guru/Staff/Laboran/Pegawai Wisata Indonesia</option>
                        <option value="siswa">Siswa SMK Wisata Indonesia</option>
                        <option value="alumni">Alumni SMK Wisata Indonesia</option>
                        <option value="guru-smp">Guru SMP</option>
                        <option value="calon-siswa">Calon Siswa SMK Wisata Indonesia</option>
                        <option value="sosial-media">Sosial Media</option>
                        <option value="referensi-langsung">Referensi Langsung</option>
                    </select>
                    <div class="invalid-feedback" id="referensi-error"></div>
                </div>
            </div>
            
            <div class="button-group">
                <a href="{{ url('/') }}" class="btn-back">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                    </svg>
                    Kembali ke Website
                </a>
                <button type="submit" class="btn-submit" id="submitBtn">
                    Daftar Sekarang
                </button>
            </div>
        </form>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-success">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="successModalLabel">
                        <i class="fas fa-check-circle me-2"></i>
                        Pendaftaran Berhasil!
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h4 class="text-success mb-3">Selamat! Pendaftaran Berhasil</h4>
                    <p class="mb-3">Data Anda telah berhasil disimpan dalam sistem PPDB SMK Wisata Indonesia.</p>
                    
                    <div id="modalUsername" class="alert alert-info" style="display: none;">
                        <strong>Username Login:</strong> <span id="usernameValue"></span>
                    </div>
                    
                    <div class="alert alert-warning">
                        <small>
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>Catat username Anda!</strong> Gunakan untuk login ke sistem.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="{{ url('/') }}" class="btn btn-success">
                        <i class="fas fa-home me-1"></i>Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('sneat/js/bootstrap.bundle.min.js') }}"></script>
    <script>
    // Event listener untuk dropdown asal sekolah
    document.getElementById('asal_sekolah').addEventListener('change', function() {
        const asalSekolahLainContainer = document.getElementById('asal_sekolah_lain_container');
        const asalSekolahLainInput = document.getElementById('asal_sekolah_lain');
        
        if (this.value === 'SMP Lainnya') {
            asalSekolahLainContainer.style.display = 'block';
            asalSekolahLainInput.required = true;
        } else {
            asalSekolahLainContainer.style.display = 'none';
            asalSekolahLainInput.required = false;
            asalSekolahLainInput.value = '';
            asalSekolahLainInput.classList.remove('is-invalid', 'is-valid');
            document.getElementById('asal-sekolah-lain-error').textContent = '';
        }
    });

    document.getElementById('pendaftaranForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        const alertArea = document.getElementById('alertArea');
        
        // Reset previous states
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        alertArea.innerHTML = '';
        
        // Reset semua state validasi sebelum submit
        resetValidationStates();
        
        // Validasi custom untuk SMP Lainnya
        const asalSekolah = document.getElementById('asal_sekolah').value;
        const asalSekolahLain = document.getElementById('asal_sekolah_lain').value;
        const asalSekolahLainInput = document.getElementById('asal_sekolah_lain');
        const asalSekolahLainError = document.getElementById('asal-sekolah-lain-error');
        
        if (asalSekolah === 'SMP Lainnya' && !asalSekolahLain.trim()) {
            asalSekolahLainInput.classList.add('is-invalid');
            asalSekolahLainError.textContent = 'Harap masukkan nama SMP Anda';
            asalSekolahLainError.className = 'invalid-feedback';
            
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            
            // Scroll ke atas
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return;
        }
        
        // Collect form data
        const formData = new FormData(this);
        
        // Jika memilih SMP Lainnya, gunakan nilai dari input manual
        if (asalSekolah === 'SMP Lainnya') {
            formData.set('asal_sekolah', asalSekolahLain);
        }
        
        // Send AJAX request
        fetch('{{ route("pendaftaran.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success case - Tampilkan modal sukses
                if (data.data && data.data.username) {
                    document.getElementById('usernameValue').textContent = data.data.username;
                    document.getElementById('modalUsername').style.display = 'block';
                    
                    // Tambahkan informasi tambahan di modal
                    const modalBody = document.querySelector('.modal-success .modal-body');
                    const existingInfo = modalBody.querySelector('.additional-info');
                    if (existingInfo) {
                        existingInfo.remove();
                    }
                    
                    const additionalInfo = document.createElement('div');
                    additionalInfo.className = 'additional-info mt-3';
                    additionalInfo.innerHTML = `
                        <div class="alert alert-info">
                            <h6 class="alert-heading">Informasi Pendaftaran:</h6>
                            <small>
                                <strong>Informasi Pendaftaran Telah Dikirim Ke Whatsapp : </strong>${data.data.no_hp}
                            </small>
                        </div>
                    `;
                    modalBody.insertBefore(additionalInfo, modalBody.querySelector('.alert-warning'));
                } else {
                    document.getElementById('modalUsername').style.display = 'none';
                }
                
                // Tampilkan modal sukses
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
                
                // Reset form dan semua state validasi
                document.getElementById('pendaftaranForm').reset();
                resetValidationStates();
                
                // Sembunyikan input SMP Lainnya setelah reset
                document.getElementById('asal_sekolah_lain_container').style.display = 'none';
                
            } else {
                // Error case
                if (data.errors) {
                    // Display field errors
                    Object.keys(data.errors).forEach(field => {
                        const input = document.querySelector(`[name="${field}"]`);
                        const errorElement = document.getElementById(`${field.replace(/_/g, '-')}-error`);
                        
                        if (input && errorElement) {
                            input.classList.add('is-invalid');
                            errorElement.textContent = data.errors[field][0];
                            errorElement.className = 'invalid-feedback';
                        }
                    });
                    
                    // Tampilkan alert umum untuk error gelombang/tahun ajaran
                    if (data.errors.gelombang) {
                        showAlert('error', 'Pendaftaran Gagal!', data.errors.gelombang[0]);
                    }
                } else {
                    // General error
                    showAlert('error', 'Terjadi Kesalahan!', data.message || 'Terjadi kesalahan saat mengirim data.');
                }
                
                // Scroll ke atas untuk menampilkan error
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Error Sistem!', 'Terjadi kesalahan jaringan atau sistem saat mengirim data.');
            
            // Scroll ke atas untuk menampilkan error
            window.scrollTo({ top: 0, behavior: 'smooth' });
        })
        .finally(() => {
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
        });
    });

    // Fungsi untuk menampilkan alert (untuk error saja)
    function showAlert(type, title, message) {
        const alertArea = document.getElementById('alertArea');
        
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';
        
        alertArea.innerHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas ${getAlertIcon(type)} me-2"></i>
                    <div>
                        <strong class="d-block">${title}</strong>
                        <span class="d-block mt-1">${message}</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
    }

    // Fungsi untuk mendapatkan icon alert
    function getAlertIcon(type) {
        const icons = {
            'success': 'fa-check-circle',
            'error': 'fa-exclamation-circle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle'
        };
        return icons[type] || 'fa-info-circle';
    }

    // Real-time validation for NISN
    document.getElementById('nisn').addEventListener('blur', function() {
        const nisn = this.value.trim();
        const errorElement = document.getElementById('nisn-error');
        
        if (nisn.length >= 10) {
            fetch(`/check-nisn/${nisn}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.available) {
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                        errorElement.textContent = data.message;
                        errorElement.className = 'invalid-feedback';
                    } else {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                        errorElement.textContent = '✓ NISN tersedia';
                        errorElement.className = 'valid-feedback';
                    }
                })
                .catch(error => {
                    console.error('Error checking NISN:', error);
                });
        } else if (nisn) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
            errorElement.textContent = 'NISN harus minimal 10 digit';
            errorElement.className = 'invalid-feedback';
        } else {
            this.classList.remove('is-invalid', 'is-valid');
            errorElement.textContent = '';
        }
    });

    // Real-time validation for Email
    document.getElementById('email').addEventListener('blur', function() {
        const email = this.value.trim();
        const errorElement = document.getElementById('email-error');
        
        if (email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                errorElement.textContent = 'Format email tidak valid';
                errorElement.className = 'invalid-feedback';
                return;
            }
            
            fetch(`/check-email/${email}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.available) {
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                        errorElement.textContent = data.message;
                        errorElement.className = 'invalid-feedback';
                    } else {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                        errorElement.textContent = '✓ Email tersedia';
                        errorElement.className = 'valid-feedback';
                    }
                })
                .catch(error => {
                    console.error('Error checking email:', error);
                });
        } else {
            this.classList.remove('is-invalid', 'is-valid');
            errorElement.textContent = '';
        }
    });

    // Validasi real-time untuk nomor handphone
    document.getElementById('no_hp').addEventListener('blur', function() {
        validatePhoneNumber(this, 'no-hp-error');
    });

    document.getElementById('no_hp_ayah').addEventListener('blur', function() {
        validatePhoneNumber(this, 'no-hp-ayah-error');
    });

    document.getElementById('no_hp_ibu').addEventListener('blur', function() {
        validatePhoneNumber(this, 'no-hp-ibu-error');
    });

    // Fungsi helper untuk validasi nomor handphone
    function validatePhoneNumber(input, errorElementId) {
        const phone = input.value.trim();
        const errorElement = document.getElementById(errorElementId);
        
        if (phone) {
            const phoneRegex = /^08[1-9][0-9]{7,10}$/;
            if (!phoneRegex.test(phone)) {
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
                errorElement.textContent = 'Format nomor handphone tidak valid. Harus diawali 08 dan 10-13 digit';
                errorElement.className = 'invalid-feedback';
            } else {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                errorElement.textContent = '✓ Format nomor handphone valid';
                errorElement.className = 'valid-feedback';
            }
        } else {
            input.classList.remove('is-invalid', 'is-valid');
            errorElement.textContent = '';
        }
    }

    // Fungsi untuk reset semua state validasi
    function resetValidationStates() {
        document.querySelectorAll('.form-control, .form-select').forEach(input => {
            input.classList.remove('is-valid', 'is-invalid');
        });
        
        document.querySelectorAll('.valid-feedback, .invalid-feedback').forEach(element => {
            element.textContent = '';
            element.className = 'invalid-feedback';
        });
    }

    // Reset validation ketika form di-reset manual
    document.getElementById('pendaftaranForm').addEventListener('reset', function() {
        setTimeout(() => {
            resetValidationStates();
            document.getElementById('asal_sekolah_lain_container').style.display = 'none';
        }, 0);
    });

    // Event listener untuk modal hidden
    document.getElementById('successModal').addEventListener('hidden.bs.modal', function () {
        console.log('Modal sukses ditutup');
    });
</script>
</body>
</html>