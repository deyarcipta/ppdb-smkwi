@extends('admin.layouts.app')
@section('title', 'Data Diterima')

@section('content')
<div class="container-fluid p-0">
    <div class="card card-full-width">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Siswa Diterima</h5>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('data-diterima.export') }}" class="btn btn-success btn-sm">
                    <i class="bx bx-download me-1"></i>Download Excel
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="40">#</th>
                            <th>No Daftar</th>
                            <th>Password</th>
                            <th width="200">Nama Pendaftar</th>
                            <th width="200">Asal Sekolah</th>
                            <th>Gelombang</th>
                            <th>Jurusan</th>
                            <th>Total Bayar</th>
                            <th>Status Bayar</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $key => $row)
                        @php
                            // Hitung total pembayaran yang sudah diverifikasi
                            $totalBayar = $row->pembayaran->where('status', 'diverifikasi')->sum('jumlah');
                            $statusPembayaran = 'belum_bayar';
                            $badgeClass = 'bg-secondary';
                            $statusText = 'Belum Bayar';

                            if ($totalBayar > 0) {
                                if ($totalBayar >= $totalBiaya) {
                                    $statusPembayaran = 'lunas';
                                    $badgeClass = 'bg-success';
                                    $statusText = 'Lunas';
                                } else {
                                    $statusPembayaran = 'belum_lunas';
                                    $badgeClass = 'bg-warning';
                                    $statusText = 'Belum Lunas';
                                }
                            }

                            // Cek jika ada pembayaran pending
                            $pembayaranPending = $row->pembayaran->where('status', 'pending')->count() > 0;
                            if ($pembayaranPending) {
                                $statusPembayaran = 'menunggu_verifikasi';
                                $badgeClass = 'bg-info';
                                $statusText = 'Menunggu Verifikasi';
                            }
                        @endphp
                        <tr>
                            <td class="text-center">{{ $data->firstItem() + $key }}</td>
                            <td class="text-center">{{ $row->username }}</td>
                            <td class="text-center">{{ $row->password_plain ?? 'password123' }}</td>
                            <td style="min-width: 200px;">{{ $row->dataSiswa->nama_lengkap ?? '-' }}</td>
                            <td style="min-width: 200px;">{{ $row->dataSiswa->asal_sekolah ?? '-' }}</td>
                            <td class="text-center">{{ $row->dataSiswa->gelombang->nama_gelombang ?? 'Gelombang 1' }}</td>
                            <td class="text-center">{{ $row->dataSiswa->jurusan->nama_jurusan ?? '-' }}</td>
                            <td class="text-center">
                                @if($totalBayar > 0)
                                    Rp {{ number_format($totalBayar, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $badgeClass }} text-white">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <!-- Tombol Detail/Lihat Formulir -->
                                    <button class="btn btn-primary btn-sm p-1" data-bs-toggle="modal" data-bs-target="#detailModal{{ $row->id }}" title="Lihat Formulir Lengkap">
                                        <i class="bx bx-show fs-12"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bx bx-data display-4"></i>
                                    <p class="mt-2">Belum ada data siswa yang diterima</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Component -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    Menampilkan {{ $data->firstItem() }} hingga {{ $data->lastItem() }} dari {{ $data->total() }} data
                </div>
                {{ $data->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Formulir Lengkap Siswa -->
@foreach ($data as $row)
@php
    $totalBayar = $row->pembayaran->where('status', 'diverifikasi')->sum('jumlah');
    $statusPembayaran = 'belum_bayar';
    $badgeClass = 'bg-secondary';
    $statusText = 'Belum Bayar';

    if ($totalBayar > 0) {
        if ($totalBayar >= $totalBiaya) {
            $statusPembayaran = 'lunas';
            $badgeClass = 'bg-success';
            $statusText = 'Lunas';
        } else {
            $statusPembayaran = 'belum_lunas';
            $badgeClass = 'bg-warning';
            $statusText = 'Belum Lunas';
        }
    }

    $pembayaranPending = $row->pembayaran->where('status', 'pending')->count() > 0;
    if ($pembayaranPending) {
        $statusPembayaran = 'menunggu_verifikasi';
        $badgeClass = 'bg-info';
        $statusText = 'Menunggu Verifikasi';
    }
@endphp
<div class="modal fade" id="detailModal{{ $row->id }}" tabindex="-1"
     data-nama="{{ $row->dataSiswa->nama_lengkap ?? $row->username }}"
     data-no-pendaftaran="{{ $row->dataSiswa->no_pendaftaran ?? $row->username }}"
     data-nisn="{{ $row->dataSiswa->nisn ?? '-' }}"
     data-nik="{{ $row->dataSiswa->nik ?? '-' }}"
     data-no-kk="{{ $row->dataSiswa->no_kk ?? '-' }}"
     data-tempat-lahir="{{ $row->dataSiswa->tempat_lahir ?? '-' }}"
     data-tanggal-lahir="{{ $row->dataSiswa->tanggal_lahir ? \Carbon\Carbon::parse($row->dataSiswa->tanggal_lahir)->format('d-m-Y') : '-' }}"
     data-jenis-kelamin="{{ $row->dataSiswa->jenis_kelamin ?? '-' }}"
     data-agama="{{ $row->dataSiswa->agama ?? '-' }}"
     data-no-hp="{{ $row->dataSiswa->no_hp ?? '-' }}"
     data-asal-sekolah="{{ $row->dataSiswa->asal_sekolah ?? '-' }}"
     data-alamat="{{ $row->dataSiswa->alamat ?? '-' }}"
     data-rt="{{ $row->dataSiswa->rt ?? '-' }}"
     data-rw="{{ $row->dataSiswa->rw ?? '-' }}"
     data-desa="{{ $row->dataSiswa->desa ?? '-' }}"
     data-kecamatan="{{ $row->dataSiswa->kecamatan ?? '-' }}"
     data-kota="{{ $row->dataSiswa->kota ?? '-' }}"
     data-provinsi="{{ $row->dataSiswa->provinsi ?? '-' }}"
     data-kode-pos="{{ $row->dataSiswa->kode_pos ?? '-' }}"
     data-tinggi-badan="{{ $row->dataSiswa->tinggi_badan ?? '-' }}"
     data-berat-badan="{{ $row->dataSiswa->berat_badan ?? '-' }}"
     data-anak-ke="{{ $row->dataSiswa->anak_ke ?? '-' }}"
     data-jumlah-saudara="{{ $row->dataSiswa->jumlah_saudara ?? '-' }}"
     data-status-dalam-keluarga="{{ $row->dataSiswa->status_dalam_keluarga ?? '-' }}"
     data-tinggal-bersama="{{ $row->dataSiswa->tinggal_bersama ?? '-' }}"
     data-jarak-kesekolah="{{ $row->dataSiswa->jarak_kesekolah ?? '-' }}"
     data-waktu-tempuh="{{ $row->dataSiswa->waktu_tempuh ?? '-' }}"
     data-transportasi="{{ $row->dataSiswa->transportasi ?? '-' }}"
     data-ukuran-baju="{{ $row->dataSiswa->ukuran_baju ?? '-' }}"
     data-hobi="{{ $row->dataSiswa->hobi ?? '-' }}"
     data-cita-cita="{{ $row->dataSiswa->cita_cita ?? '-' }}"
     data-tahun-lulus="{{ $row->dataSiswa->tahun_lulus ?? '-' }}"
     data-no-kip="{{ $row->dataSiswa->no_kip ?? '-' }}"
     data-referensi="{{ $row->dataSiswa->referensi ?? '-' }}"
     data-ket-referensi="{{ $row->dataSiswa->ket_referensi ?? '-' }}"
     data-nik-ayah="{{ $row->dataSiswa->nik_ayah ?? '-' }}"
     data-nama-ayah="{{ $row->dataSiswa->nama_ayah ?? '-' }}"
     data-tempat-lahir-ayah="{{ $row->dataSiswa->tempat_lahir_ayah ?? '-' }}"
     data-tanggal-lahir-ayah="{{ $row->dataSiswa->tanggal_lahir_ayah ? \Carbon\Carbon::parse($row->dataSiswa->tanggal_lahir_ayah)->format('d-m-Y') : '-' }}"
     data-pendidikan-ayah="{{ $row->dataSiswa->pendidikan_ayah ?? '-' }}"
     data-pekerjaan-ayah="{{ $row->dataSiswa->pekerjaan_ayah ?? '-' }}"
     data-penghasilan-ayah="{{ $row->dataSiswa->penghasilan_ayah ?? '-' }}"
     data-no-hp-ayah="{{ $row->dataSiswa->no_hp_ayah ?? '-' }}"
     data-nik-ibu="{{ $row->dataSiswa->nik_ibu ?? '-' }}"
     data-nama-ibu="{{ $row->dataSiswa->nama_ibu ?? '-' }}"
     data-tempat-lahir-ibu="{{ $row->dataSiswa->tempat_lahir_ibu ?? '-' }}"
     data-tanggal-lahir-ibu="{{ $row->dataSiswa->tanggal_lahir_ibu ? \Carbon\Carbon::parse($row->dataSiswa->tanggal_lahir_ibu)->format('d-m-Y') : '-' }}"
     data-pendidikan-ibu="{{ $row->dataSiswa->pendidikan_ibu ?? '-' }}"
     data-pekerjaan-ibu="{{ $row->dataSiswa->pekerjaan_ibu ?? '-' }}"
     data-penghasilan-ibu="{{ $row->dataSiswa->penghasilan_ibu ?? '-' }}"
     data-no-hp-ibu="{{ $row->dataSiswa->no_hp_ibu ?? '-' }}"
     data-nik-wali="{{ $row->dataSiswa->nik_wali ?? '-' }}"
     data-nama-wali="{{ $row->dataSiswa->nama_wali ?? '-' }}"
     data-tempat-lahir-wali="{{ $row->dataSiswa->tempat_lahir_wali ?? '-' }}"
     data-tanggal-lahir-wali="{{ $row->dataSiswa->tanggal_lahir_wali ? \Carbon\Carbon::parse($row->dataSiswa->tanggal_lahir_wali)->format('d-m-Y') : '-' }}"
     data-pendidikan-wali="{{ $row->dataSiswa->pendidikan_wali ?? '-' }}"
     data-pekerjaan-wali="{{ $row->dataSiswa->pekerjaan_wali ?? '-' }}"
     data-penghasilan-wali="{{ $row->dataSiswa->penghasilan_wali ?? '-' }}"
     data-no-hp-wali="{{ $row->dataSiswa->no_hp_wali ?? '-' }}"
     data-gelombang="{{ $row->dataSiswa->gelombang->nama_gelombang ?? 'Gelombang 1' }}"
     data-jurusan="{{ $row->dataSiswa->jurusan->nama_jurusan ?? '-' }}"
     data-email="{{ $row->email ?? '-' }}"
     data-tanggal-daftar="{{ $row->created_at->format('d-m-Y') }}">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Formulir Pendaftaran Lengkap - {{ $row->dataSiswa->nama_lengkap ?? $row->username }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Informasi Akun & Pendaftaran -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 text-primary">Informasi Akun</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Username:</strong></div>
                                    <div class="col-sm-8">{{ $row->username }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Password:</strong></div>
                                    <div class="col-sm-8">{{ $row->password_plain ?? 'password123' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Email:</strong></div>
                                    <div class="col-sm-8">{{ $row->email ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Status:</strong></div>
                                    <div class="col-sm-8">
                                        <span class="badge bg-success text-white">DITERIMA</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 text-primary">Informasi Pendaftaran</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>No. Pendaftaran:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->no_pendaftaran ?? $row->username }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Gelombang:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->gelombang->nama_gelombang ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Jurusan:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->jurusan->nama_jurusan ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tanggal Daftar:</strong></div>
                                    <div class="col-sm-8">{{ $row->created_at->format('d M Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Pribadi -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">Data Pribadi</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Nama Lengkap:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->nama_lengkap ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>NISN:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->nisn ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>NIK:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->nik ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>No. KK:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->no_kk ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Jenis Kelamin:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->jenis_kelamin ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tempat Lahir:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->tempat_lahir ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tanggal Lahir:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->tanggal_lahir ? \Carbon\Carbon::parse($row->dataSiswa->tanggal_lahir)->format('d M Y') : '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Agama:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->agama ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>No. HP:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->no_hp ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Email:</strong></div>
                                    <div class="col-sm-8">{{ $row->email ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Ukuran Baju:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->ukuran_baju ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Hobi:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->hobi ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Cita-cita:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->cita_cita ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Anak Ke:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->anak_ke ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Jumlah Saudara:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->jumlah_saudara ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Fisik -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">Data Fisik</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tinggi Badan:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->tinggi_badan ? $row->dataSiswa->tinggi_badan . ' cm' : '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Berat Badan:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->berat_badan ? $row->dataSiswa->berat_badan . ' kg' : '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alamat -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">Alamat Tempat Tinggal</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Alamat Lengkap:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->alamat ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>RT/RW:</strong></div>
                                    <div class="col-sm-8">
                                        @if($row->dataSiswa->rt || $row->dataSiswa->rw)
                                            RT {{ $row->dataSiswa->rt ?? '-' }} / RW {{ $row->dataSiswa->rw ?? '-' }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Desa/Kelurahan:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->desa ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Kecamatan:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->kecamatan ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Kota/Kabupaten:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->kota ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Provinsi:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->provinsi ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Kode Pos:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->kode_pos ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Status dalam Keluarga:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->status_dalam_keluarga ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tempat Tinggal -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">Informasi Tempat Tinggal</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tinggal Bersama:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->tinggal_bersama ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Jarak ke Sekolah:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->jarak_kesekolah ? $row->dataSiswa->jarak_kesekolah . ' km' : '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Waktu Tempuh:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->waktu_tempuh ? $row->dataSiswa->waktu_tempuh . ' menit' : '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Transportasi:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->transportasi ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Sekolah Asal -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">Data Sekolah Asal</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Asal Sekolah:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->asal_sekolah ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tahun Lulus:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->tahun_lulus ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Ayah -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">Data Ayah</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>NIK Ayah:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->nik_ayah ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Nama Ayah:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->nama_ayah ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tempat Lahir:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->tempat_lahir_ayah ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tanggal Lahir:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->tanggal_lahir_ayah ? \Carbon\Carbon::parse($row->dataSiswa->tanggal_lahir_ayah)->format('d M Y') : '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Pendidikan:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->pendidikan_ayah ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Pekerjaan:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->pekerjaan_ayah ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Penghasilan:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->penghasilan_ayah ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>No. HP:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->no_hp_ayah ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Ibu -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">Data Ibu</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>NIK Ibu:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->nik_ibu ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Nama Ibu:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->nama_ibu ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tempat Lahir:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->tempat_lahir_ibu ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tanggal Lahir:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->tanggal_lahir_ibu ? \Carbon\Carbon::parse($row->dataSiswa->tanggal_lahir_ibu)->format('d M Y') : '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Pendidikan:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->pendidikan_ibu ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Pekerjaan:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->pekerjaan_ibu ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Penghasilan:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->penghasilan_ibu ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>No. HP:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->no_hp_ibu ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Wali (jika ada) -->
                @if($row->dataSiswa->nik_wali || $row->dataSiswa->nama_wali)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">Data Wali</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>NIK Wali:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->nik_wali ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Nama Wali:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->nama_wali ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tempat Lahir:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->tempat_lahir_wali ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Tanggal Lahir:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->tanggal_lahir_wali ? \Carbon\Carbon::parse($row->dataSiswa->tanggal_lahir_wali)->format('d M Y') : '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Pendidikan:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->pendidikan_wali ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Pekerjaan:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->pekerjaan_wali ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Penghasilan:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->penghasilan_wali ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>No. HP:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->no_hp_wali ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Informasi Tambahan -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">Informasi Tambahan</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>No. KIP:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->no_kip ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-sm-4"><strong>Referensi:</strong></div>
                                    <div class="col-sm-8">{{ $row->dataSiswa->referensi ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                        @if($row->dataSiswa->ket_referensi)
                        <div class="row">
                            <div class="col-12">
                                <div class="row mb-2">
                                    <div class="col-sm-2"><strong>Keterangan Referensi:</strong></div>
                                    <div class="col-sm-10">{{ $row->dataSiswa->ket_referensi }}</div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Informasi Pembayaran -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">Informasi Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="row mb-2">
                                    <div class="col-sm-6"><strong>Total Biaya:</strong></div>
                                    <div class="col-sm-6">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-6"><strong>Total Dibayar:</strong></div>
                                    <div class="col-sm-6">Rp {{ number_format($totalBayar, 0, ',', '.') }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-6"><strong>Sisa Pembayaran:</strong></div>
                                    <div class="col-sm-6">
                                        @if($totalBayar >= $totalBiaya)
                                            <span class="text-success">LUNAS</span>
                                        @else
                                            Rp {{ number_format($totalBiaya - $totalBayar, 0, ',', '.') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-6"><strong>Status:</strong></div>
                                    <div class="col-sm-6">
                                        <span class="badge {{ $badgeClass }} text-white">{{ $statusText }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted">Riwayat Pembayaran</h6>
                                @if($row->pembayaran->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Jumlah</th>
                                                    <th>Status</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($row->pembayaran as $pembayaran)
                                                <tr>
                                                    <td>{{ $pembayaran->created_at->format('d M Y') }}</td>
                                                    <td>Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                                                    <td>
                                                        @if($pembayaran->status == 'diverifikasi')
                                                            <span class="badge bg-success">Terverifikasi</span>
                                                        @elseif($pembayaran->status == 'pending')
                                                            <span class="badge bg-warning">Menunggu</span>
                                                        @else
                                                            <span class="badge bg-danger">Ditolak</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $pembayaran->catatan ?? '-' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-light border text-center">
                                        <i class="bx bx-info-circle"></i> Belum ada riwayat pembayaran
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i>Tutup
                </button>
                <button type="button" class="btn btn-primary" onclick="printFormulir({{ $row->id }})">
                    <i class="bx bx-printer me-1"></i>Print Formulir
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('styles')
<style>
.card-full-width {
    margin-left: -1.5rem;
    margin-right: -1.5rem;
    border-radius: 0;
    border-left: none;
    border-right: none;
    margin-bottom: 0 !important;
    box-shadow: none;
    border-top: 1px solid #e4e6e8;
    border-bottom: 1px solid #e4e6e8;
}

@media (min-width: 1200px) {
    .card-full-width {
        margin-left: -2rem;
        margin-right: -2rem;
    }
}

.container-fluid.p-0 {
    padding: 0 !important;
}

.badge.text-white {
    color: white !important;
    font-weight: 500;
}

.fs-12 {
    font-size: 12px;
}

.table-sm th,
.table-sm td {
    padding: 0.4rem 0.5rem;
    font-size: 0.875rem;
}

.btn-sm.p-1 {
    padding: 0.25rem 0.4rem;
}

.table-responsive {
    overflow-x: auto;
}

.table td {
    white-space: normal;
    word-wrap: break-word;
}

.table th {
    font-weight: 600;
    background-color: #f8f9fa;
    border-bottom: 2px solid #e4e6e8;
}

.badge {
    font-size: 0.7rem;
    padding: 0.35em 0.65em;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('sneat/js/printFormulir.js') }}"></script>

<script>
// Fungsi Print Formulir Admin
window.printFormulir = function(id) {
    doPrintFormulir(id, "detailModal");
};
</script>
@endpush
