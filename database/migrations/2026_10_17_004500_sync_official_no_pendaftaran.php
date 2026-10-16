<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\DataSiswa;
use App\Models\Pembayaran;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Lengkapi no_pendaftaran pada data_siswa yang masih kosong / belum berformat resmi
        $dataSiswas = DataSiswa::with('user')->get();
        foreach ($dataSiswas as $siswa) {
            if (empty($siswa->no_pendaftaran) || !str_contains($siswa->no_pendaftaran, '/')) {
                $jurusanId = $siswa->jurusan_id ?? 1;
                $gelombangId = $siswa->gelombang_id ?? 1;
                $username = $siswa->user?->username;

                $officialNo = DataSiswa::generateNoPendaftaranStatic($jurusanId, $gelombangId, $username);
                $siswa->update(['no_pendaftaran' => $officialNo]);
            }
        }

        // 2. Sinkronkan seluruh no_pendaftaran di tabel pembayaran sesuai data_siswa
        $pembayarans = Pembayaran::with('dataSiswa')->get();
        foreach ($pembayarans as $p) {
            $officialNo = $p->dataSiswa?->no_pendaftaran;
            if ($officialNo && $p->no_pendaftaran !== $officialNo) {
                $p->update(['no_pendaftaran' => $officialNo]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu rollback
    }
};
