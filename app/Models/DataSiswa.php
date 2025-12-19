<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataSiswa extends Model
{
    use HasFactory;

    protected $table = 'data_siswa';

    protected $fillable = [
        'user_id',
        'jurusan_id',
        'gelombang_id',
        'no_pendaftaran',
        'nisn',
        'nik',
        'no_kk',
        'nama_lengkap',
        'status_pendaftar',
        'ket_pendaftaran',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'no_hp',
        'foto_siswa',
        'asal_sekolah',
        'id_smp',
        'agama',
        'ukuran_baju',
        'hobi',
        'cita_cita',
        'alamat',
        'rt',
        'rw',
        'desa',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'anak_ke',
        'jumlah_saudara',
        'tinggi_badan',
        'berat_badan',
        'status_dalam_keluarga',
        'tinggal_bersama',
        'jarak_kesekolah',
        'waktu_tempuh',
        'transportasi',
        'no_kip',
        'referensi',
        'ket_referensi',
        'nik_ayah',
        'nama_ayah',
        'tempat_lahir_ayah',
        'tanggal_lahir_ayah',
        'pendidikan_ayah',
        'pekerjaan_ayah',
        'penghasilan_ayah',
        'no_hp_ayah',
        'nik_ibu',
        'nama_ibu',
        'tempat_lahir_ibu',
        'tanggal_lahir_ibu',
        'pendidikan_ibu',
        'pekerjaan_ibu',
        'penghasilan_ibu',
        'no_hp_ibu',
        'nik_wali',
        'nama_wali',
        'tempat_lahir_wali',
        'tanggal_lahir_wali',
        'pendidikan_wali',
        'pekerjaan_wali',
        'penghasilan_wali',
        'no_hp_wali',
        'is_form_completed',
        'is_verified',
        'is_paid'
    ];

    protected $casts = [
        'is_form_completed' => 'boolean',
        'is_verified' => 'boolean',
        'is_paid' => 'boolean',
        'tanggal_lahir' => 'date',
        'tanggal_lahir_ayah' => 'date',
        'tanggal_lahir_ibu' => 'date',
        'tanggal_lahir_wali' => 'date',
        'anak_ke' => 'integer',
        'jumlah_saudara' => 'integer',
        'tinggi_badan' => 'integer',
        'berat_badan' => 'integer',
        'jarak_kesekolah' => 'integer',
        'waktu_tempuh' => 'integer',
    ];

    /**
     * Relasi ke user (akun login)
     */
    public function user()
    {
        return $this->belongsTo(UserSiswa::class, 'user_id');
    }

    public function dataSmp()
    {
        return $this->belongsTo(DataSmp::class, 'id_smp', 'id_smp');
    }

    /**
     * Relasi ke jurusan
     */
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    /**
     * Relasi ke gelombang pendaftaran
     */
    public function gelombang()
    {
        return $this->belongsTo(GelombangPendaftaran::class, 'gelombang_id');
    }

    /**
     * Accessor untuk mendapatkan nama jurusan
     */
    public function getNamaJurusanAttribute()
    {
        return $this->jurusan ? $this->jurusan->nama_jurusan : 'Belum dipilih';
    }

    /**
     * Accessor untuk mendapatkan kode jurusan
     */
    public function getKodeJurusanAttribute()
    {
        return $this->jurusan ? $this->jurusan->kode_jurusan : null;
    }

    /**
     * Accessor untuk mendapatkan nama gelombang
     */
    public function getNamaGelombangAttribute()
    {
        return $this->gelombang ? $this->gelombang->nama_gelombang : 'Belum dipilih';
    }

    /**
     * Accessor untuk status pendaftar dengan label
     */
    public function getStatusPendaftarLabelAttribute()
    {
        $statusLabels = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'diterima' => '<span class="badge bg-success">Diterima</span>',
            'ditolak' => '<span class="badge bg-danger">Ditolak</span>',
        ];

        return $statusLabels[$this->status_pendaftar] ?? $statusLabels['pending'];
    }

    /**
     * Accessor untuk status verifikasi dengan label
     */
    public function getStatusVerifikasiLabelAttribute()
    {
        return $this->is_verified 
            ? '<span class="badge bg-success">Terverifikasi</span>'
            : '<span class="badge bg-warning">Belum Verifikasi</span>';
    }

    /**
     * Accessor untuk status pembayaran dengan label
     */
    public function getStatusPembayaranLabelAttribute()
    {
        return $this->is_paid 
            ? '<span class="badge bg-success">Lunas</span>'
            : '<span class="badge bg-danger">Belum Bayar</span>';
    }

    /**
     * Scope untuk siswa yang sudah memilih jurusan
     */
    public function scopeHasJurusan($query)
    {
        return $query->whereNotNull('jurusan_id');
    }

    /**
     * Scope untuk siswa yang belum memilih jurusan
     */
    public function scopeNoJurusan($query)
    {
        return $query->whereNull('jurusan_id');
    }

    /**
     * Scope untuk siswa yang sudah melengkapi formulir
     */
    public function scopeFormCompleted($query)
    {
        return $query->where('is_form_completed', true);
    }

    /**
     * Scope untuk siswa yang sudah terverifikasi
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope untuk siswa yang sudah bayar
     */
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    /**
     * Scope untuk status pendaftar tertentu
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status_pendaftar', $status);
    }

    /**
     * Scope untuk jurusan tertentu
     */
    public function scopeJurusan($query, $jurusanId)
    {
        return $query->where('jurusan_id', $jurusanId);
    }

    /**
     * Scope untuk gelombang tertentu
     */
    public function scopeGelombang($query, $gelombangId)
    {
        return $query->where('gelombang_id', $gelombangId);
    }

    /**
     * Cek apakah data siswa sudah lengkap
     */
    public function getIsDataLengkapAttribute()
    {
        return $this->is_form_completed && 
               $this->jurusan_id && 
               $this->gelombang_id &&
               $this->nama_lengkap &&
               $this->tempat_lahir &&
               $this->tanggal_lahir &&
               $this->no_hp;
    }

    /**
     * Hitung progress pengisian formulir (dalam persen)
     */
    public function getProgressFormulirAttribute()
    {
        $fields = [
            'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
            'no_hp', 'asal_sekolah', 'agama', 'alamat', 'rt', 'rw',
            'desa', 'kecamatan', 'kota', 'provinsi', 'kode_pos',
            'anak_ke', 'jumlah_saudara', 'tinggi_badan', 'berat_badan', 'nik_ayah', 'nama_ayah', 'tanggal_lahir_ayah', 'pendidikan_ayah',
            'pekerjaan_ayah', 'penghasilan_ayah', 'no_hp_ayah',
            'nik_ibu', 'nama_ibu', 'tanggal_lahir_ibu', 'pendidikan_ibu',
            'pekerjaan_ibu', 'penghasilan_ibu', 'no_hp_ibu', 'nik_wali', 'nama_wali', 'tanggal_lahir_wali', 'pendidikan_wali',
            'pekerjaan_wali', 'penghasilan_wali', 'no_hp_wali',
        ];

        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $filled++;
            }
        }

        return round(($filled / count($fields)) * 100);
    }
}