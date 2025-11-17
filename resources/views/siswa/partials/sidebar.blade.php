<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
      <span class="app-brand-text demo fw-bolder ms-2">PPDB Siswa</span>
    </a>
  </div>
  <ul class="menu-inner py-1">
    <li class="menu-item {{ Request::is('siswa/dashboard') ? 'active' : '' }}">
      <a href="{{route('siswa.dashboard')}}" class="menu-link">
        <i class="menu-icon bx bx-home-circle"></i>
        <div>Dashboard</div>
      </a>
    </li>
    
    @inject('formulirAccess', 'App\Services\FormulirAccessService')
    @php
        $accessStatus = $formulirAccess->getAccessStatus();
        $accessMessage = $formulirAccess->getAccessMessage();
    @endphp

    @if($formulirAccess->canAccessFormulir())
        <li class="menu-item {{ Request::is('siswa/create') ? 'active' : '' }}">
            <a href="{{ route('siswa.formulir') }}" class="menu-link">
                <i class="menu-icon bx bx-note"></i>
                <div>Formulir</div>
            </a>
        </li>
    @else
        <li class="menu-item disabled">
            <a href="javascript:void(0)" 
               class="menu-link menu-formulir-locked" 
               data-message="{{ $accessMessage }}"
               onclick="showAccessMessage(this)">
                <i class="menu-icon bx bx-note"></i>
                <div>Formulir</div>
                <span class="badge bg-warning ms-auto">Terkunci</span>
            </a>
        </li>
    @endif
    
    <li class="menu-item {{ Request::is('siswa/pembayaran') ? 'active' : '' }}">
      <a href="{{ route('siswa.pembayaran.index') }}" class="menu-link">
        <i class="menu-icon bx bx-wallet"></i>
        <div>Pembayaran</div>
      </a>
    </li>
    <li class="menu-item">
      <a href="#" class="menu-link">
        <i class="menu-icon bx bx-news"></i>
        <div>Pengumuman</div>
      </a>
    </li>
  </ul>
</aside>

<!-- Tambahkan script untuk menampilkan pesan -->
<script>
function showAccessMessage(element) {
    const message = element.getAttribute('data-message');
    
    // Tampilkan alert/swal/toast
    Swal.fire({
        icon: 'warning',
        title: 'Akses Dibatasi',
        text: message,
        confirmButtonText: 'Mengerti',
        confirmButtonColor: '#3085d6',
    });
    
    // Atau menggunakan alert biasa
    // alert(message);
    
    // Atau menggunakan toast
    // Toastify({
    //     text: message,
    //     duration: 3000,
    //     gravity: "top",
    //     position: "right",
    //     backgroundColor: "linear-gradient(to right, #ffa726, #fb8c00)",
    // }).showToast();
}

// Atau dengan event listener
document.addEventListener('DOMContentLoaded', function() {
    const lockedMenuItems = document.querySelectorAll('.menu-formulir-locked');
    
    lockedMenuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const message = this.getAttribute('data-message');
            
            // Gunakan SweetAlert2
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'Informasi Formulir',
                    html: `
                        <div class="text-start">
                            <p class="mb-3">${message}</p>
                            <hr>
                            <small class="text-muted">Lengkapi persyaratan terlebih dahulu untuk mengakses formulir.</small>
                        </div>
                    `,
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#696cff',
                });
            } else {
                // Fallback ke alert biasa
                alert(message);
            }
        });
    });
});
</script>