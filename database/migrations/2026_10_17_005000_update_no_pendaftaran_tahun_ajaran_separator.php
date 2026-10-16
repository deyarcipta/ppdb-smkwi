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
        // Ganti garing ganda pada tahun ajaran di no_pendaftaran (misal /2027/2028/ -> /2027-2028/)
        $dataSiswas = DataSiswa::whereNotNull('no_pendaftaran')->get();
        foreach ($dataSiswas as $s) {
            $updated = preg_replace('/\/(\d{4})\/(\d{4})\//', '/$1-$2/', $s->no_pendaftaran);
            if ($updated !== $s->no_pendaftaran) {
                $s->update(['no_pendaftaran' => $updated]);
            }
        }

        $pembayarans = Pembayaran::whereNotNull('no_pendaftaran')->get();
        foreach ($pembayarans as $p) {
            $updated = preg_replace('/\/(\d{4})\/(\d{4})\//', '/$1-$2/', $p->no_pendaftaran);
            if ($updated !== $p->no_pendaftaran) {
                $p->update(['no_pendaftaran' => $updated]);
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
