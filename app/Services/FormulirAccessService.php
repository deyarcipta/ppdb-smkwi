<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\DataSiswa;
use App\Models\Pembayaran;

class FormulirAccessService
{
    public function canAccessFormulir()
    {
        $dataSiswa = DataSiswa::where('user_id', Auth::id())->first();
        $pembayaran = Pembayaran::where('user_id', Auth::id())
                                ->where('jenis_pembayaran', 'formulir')
                                ->first();
        
        $hasChosenJurusan = $dataSiswa && $dataSiswa->jurusan_id !== null;
        $isPaymentVerified = $pembayaran && $pembayaran->status === 'diverifikasi'; // Sesuai dengan database
        
        return $hasChosenJurusan && $isPaymentVerified;
    }
    
    public function getAccessStatus()
    {
        $dataSiswa = DataSiswa::where('user_id', Auth::id())->first();
        $pembayaran = Pembayaran::where('user_id', Auth::id())
                                ->where('jenis_pembayaran', 'formulir')
                                ->first();
        
        return [
            'has_chosen_jurusan' => $dataSiswa && $dataSiswa->jurusan_id !== null,
            'is_payment_verified' => $pembayaran && $pembayaran->status === 'diverifikasi',
            'pembayaran_data' => $pembayaran,
            'message' => $this->getAccessMessage()
        ];
    }
    
    public function getAccessMessage()
    {
        $dataSiswa = DataSiswa::where('user_id', Auth::id())->first();
        $pembayaran = Pembayaran::where('user_id', Auth::id())
                                ->where('jenis_pembayaran', 'formulir')
                                ->first();
        
        if (!$pembayaran) {
            return 'Silakan lakukan upload bukti formulir terlebih dahulu.';
        }

        if ($pembayaran->status === 'pending') {
            return 'Tunggu proses verifikasi pembayaran formulir.';
        }

        if (!$dataSiswa || $dataSiswa->jurusan_id === null) {
            return 'Silakan pilih jurusan terlebih dahulu.';
        }        
        
        // PERBAIKAN: Gunakan 'diverifikasi' bukan 'verified'
        if ($pembayaran->status !== 'diverifikasi') {
            return 'Menunggu verifikasi pembayaran formulir. Status: ' . ucfirst($pembayaran->status);
        }
        
        return 'Akses diizinkan';
    }
    
    /**
     * Method tambahan untuk mendapatkan data pembayaran formulir
     */
    public function getPembayaranFormulir()
    {
        return Pembayaran::where('user_id', Auth::id())
                        ->where('jenis_pembayaran', 'formulir')
                        ->first();
    }
    
    /**
     * Method untuk mengecek apakah sudah ada pembayaran formulir (tanpa memandang status)
     */
    public function hasFormulirPayment()
    {
        return Pembayaran::where('user_id', Auth::id())
                        ->where('jenis_pembayaran', 'formulir')
                        ->exists();
    }
}