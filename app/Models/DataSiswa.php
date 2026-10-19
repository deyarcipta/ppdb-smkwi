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
        'jenjang_sekolah_asal',
        'status_sekolah_asal',
        'kecamatan_desa_sekolah_asal',
        'kota_sekolah_asal',
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
        'status_ayah',
        'nik_ayah',
        'nama_ayah',
        'tempat_lahir_ayah',
        'tanggal_lahir_ayah',
        'pendidikan_ayah',
        'pekerjaan_ayah',
        'penghasilan_ayah',
        'no_hp_ayah',
        'status_ibu',
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
        'is_paid',
        'is_card_printed'
    ];

    protected $casts = [
        'is_form_completed' => 'boolean',
        'is_verified' => 'boolean',
        'is_paid' => 'boolean',
        'is_card_printed' => 'boolean',
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

    public function gelombangPendaftaran()
    {
        return $this->belongsTo(GelombangPendaftaran::class, 'gelombang_id');
    }

    public function tahunAjaran()
    {
        return $this->hasOneThrough(
            TahunAjaran::class,
            GelombangPendaftaran::class,
            'id', // Foreign key on gelombang_pendaftaran (matches gelombang_id)
            'id', // Foreign key on tahun_ajaran
            'gelombang_id', // Local key on data_siswa
            'tahun_ajaran_id' // Local key on gelombang_pendaftaran
        );
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
     * Pastikan nomor pendaftaran selalu mengikuti pengaturan tipe_aplikasi (SPMB atau PPDB)
     */
    public function getNoPendaftaranAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }

        static $currentTipe = null;
        if ($currentTipe === null) {
            $pengaturan = \App\Models\PengaturanAplikasi::first();
            $currentTipe = $pengaturan->tipe_aplikasi ?? 'PPDB';
        }

        return preg_replace('/^(PPDB|SPMB)/i', $currentTipe, $value);
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
            'no_hp', 'asal_sekolah', 'jenjang_sekolah_asal', 'status_sekolah_asal', 'kecamatan_desa_sekolah_asal', 'agama', 'alamat', 'rt', 'rw',
            'desa', 'kecamatan', 'kota', 'provinsi', 'kode_pos',
            'anak_ke', 'jumlah_saudara', 'tinggi_badan', 'berat_badan', 'status_ayah', 'nik_ayah', 'nama_ayah', 'tanggal_lahir_ayah', 'pendidikan_ayah',
            'pekerjaan_ayah', 'penghasilan_ayah', 'no_hp_ayah',
            'status_ibu', 'nik_ibu', 'nama_ibu', 'tanggal_lahir_ibu', 'pendidikan_ibu',
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

    /**
     * Fallback accessor untuk kecamatan_desa_sekolah_asal dan kota_sekolah_asal
     */
    public function getKecamatanDesaSekolahAsalAttribute($value)
    {
        return $value ?: ($this->attributes['kota_sekolah_asal'] ?? null);
    }

    public function getKotaSekolahAsalAttribute($value)
    {
        return $value ?: ($this->attributes['kecamatan_desa_sekolah_asal'] ?? null);
    }

    /**
     * Helper accessor status ayah & ibu default
     */
    public function getStatusAyahAttribute($value)
    {
        return $value ?: 'Masih Hidup';
    }

    public function getStatusIbuAttribute($value)
    {
        return $value ?: 'Masih Hidup';
    }

    /**
     * Generate Nomor Pendaftaran Resmi (Format Panjang: TIPE/JURUSAN/TAHUN_AJARAN/GELOMBANG/NOMOR_URUT)
     */
    public static function generateNoPendaftaranStatic($jurusanId, $gelombangId, $username = null)
    {
        try {
            $jurusan = Jurusan::find($jurusanId);
            $kodeJurusan = $jurusan ? $jurusan->kode_jurusan : 'UMUM';

            $gelombang = GelombangPendaftaran::with('tahunAjaran')->find($gelombangId);
            $tahunAjaran = $gelombang?->tahunAjaran ? $gelombang->tahunAjaran->nama : date('Y');
            $tahunAjaranClean = str_replace('/', '-', $tahunAjaran);

            $namaGelombang = $gelombang ? $gelombang->nama_gelombang : 'Gelombang 1';
            preg_match('/\d+/', $namaGelombang, $matches);
            $angka = $matches[0] ?? 1;
            $romawiMap = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V'];
            $angkaGelombang = $romawiMap[$angka] ?? 'I';

            $pengaturan = PengaturanAplikasi::first();
            $tipeAplikasi = $pengaturan->tipe_aplikasi ?? 'PPDB';

            // Hitung nomor urut berikutnya berdasarkan jurusan dan gelombang
            $count = self::where('gelombang_id', $gelombangId)
                ->where('jurusan_id', $jurusanId)
                ->whereNotNull('no_pendaftaran')
                ->count();

            $nomorUrut = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            $candidate = "{$tipeAplikasi}/{$kodeJurusan}/{$tahunAjaranClean}/{$angkaGelombang}/{$nomorUrut}";

            while (self::where('no_pendaftaran', $candidate)->exists()) {
                $count++;
                $nomorUrut = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
                $candidate = "{$tipeAplikasi}/{$kodeJurusan}/{$tahunAjaranClean}/{$angkaGelombang}/{$nomorUrut}";
            }

            return $candidate;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error generating no_pendaftaran: ' . $e->getMessage());
            $tipeFallback = PengaturanAplikasi::first()->tipe_aplikasi ?? 'PPDB';
            return "{$tipeFallback}/UMUM/" . date('Y') . "/I/001";
        }
    }
}