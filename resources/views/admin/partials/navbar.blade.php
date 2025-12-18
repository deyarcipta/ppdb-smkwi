<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <!-- Tombol Hamburger -->
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center w-100" id="navbar-collapse">
    <!-- Bagian Kiri (opsional, misal pencarian) -->
    <ul class="navbar-nav flex-row align-items-center ms-auto">
      <!-- Dropdown User -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center" href="#" data-bs-toggle="dropdown">
          <span class="fw-semibold me-2 d-none d-sm-inline">{{ auth()->user()->name }}</span>
          <div class="avatar avatar-online">
            <img src="{{ asset('sneat/img/avatars/1.png') }}" alt="Admin" class="w-px-40 h-auto rounded-circle" />
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <!-- Option untuk mengubah password -->
          <li>
            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
              <i class="bx bx-lock-alt me-2"></i>
              <span class="align-middle">Ubah Password</span>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            <form action="{{ route('backend.logout') }}" method="POST">
              @csrf
              <button type="submit" class="dropdown-item">Logout</button>
            </form>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</nav>

<!-- Modal Ubah Password -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title">Ubah Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <!-- Form Ubah Password -->
      <form id="changePasswordForm" action="{{ route('admin.password.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="modal-body">
          <!-- Password Lama -->
          <div class="mb-3">
            <label for="current_password" class="form-label">Password Lama</label>
            <div class="input-group input-group-merge">
              <input type="password" 
                     id="current_password" 
                     name="current_password" 
                     class="form-control @error('current_password') is-invalid @enderror"
                     placeholder="Masukkan password lama"
                     required>
              <span class="input-group-text cursor-pointer">
                <i class="bx bx-hide toggle-password" data-target="current_password"></i>
              </span>
              @error('current_password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          
          <!-- Password Baru -->
          <div class="mb-3">
            <label for="new_password" class="form-label">Password Baru</label>
            <div class="input-group input-group-merge">
              <input type="password" 
                     id="new_password" 
                     name="new_password" 
                     class="form-control @error('new_password') is-invalid @enderror"
                     placeholder="Masukkan password baru"
                     minlength="8"
                     required>
              <span class="input-group-text cursor-pointer">
                <i class="bx bx-hide toggle-password" data-target="new_password"></i>
              </span>
              @error('new_password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <small class="text-muted">Minimal 8 karakter</small>
          </div>
          
          <!-- Konfirmasi Password Baru -->
          <div class="mb-3">
            <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
            <div class="input-group input-group-merge">
              <input type="password" 
                     id="new_password_confirmation" 
                     name="new_password_confirmation" 
                     class="form-control @error('new_password_confirmation') is-invalid @enderror"
                     placeholder="Konfirmasi password baru"
                     minlength="8"
                     required>
              <span class="input-group-text cursor-pointer">
                <i class="bx bx-hide toggle-password" data-target="new_password_confirmation"></i>
              </span>
              @error('new_password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Toggle show/hide password
  document.querySelectorAll('.toggle-password').forEach(function(icon) {
    icon.addEventListener('click', function() {
      const targetId = this.getAttribute('data-target');
      const passwordInput = document.getElementById(targetId);
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      
      passwordInput.setAttribute('type', type);
      
      // Toggle icon
      this.classList.toggle('bx-hide');
      this.classList.toggle('bx-show');
    });
  });

  // Reset form ketika modal ditutup
  const modal = document.getElementById('changePasswordModal');
  modal.addEventListener('hidden.bs.modal', function() {
    document.getElementById('changePasswordForm').reset();
    
    // Reset error states
    document.querySelectorAll('.is-invalid').forEach(function(el) {
      el.classList.remove('is-invalid');
    });
    
    // Reset feedback messages
    document.querySelectorAll('.invalid-feedback').forEach(function(el) {
      el.style.display = 'none';
    });
  });

  // Validasi form
  document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('new_password_confirmation').value;
    
    if (newPassword !== confirmPassword) {
      e.preventDefault();
      alert('Password baru dan konfirmasi password tidak sama!');
      return false;
    }
    
    return true;
  });
});
</script>
@endpush

<style>
.cursor-pointer {
  cursor: pointer;
}

.toggle-password:hover {
  color: #696cff;
}

/* Validasi real-time */
.is-invalid {
  border-color: #ff3e1d !important;
}

.invalid-feedback {
  display: block;
}
</style>
