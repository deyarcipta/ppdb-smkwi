<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventAdminDelete
{
    /**
     * Handle an incoming request.
     * Membatasi hak akses role admin: admin tidak dapat melakukan delete data,
     * hanya superadmin yang diizinkan untuk menghapus data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Periksa apakah operasi request adalah penghapusan (DELETE method)
        if ($request->isMethod('delete')) {
            // Jika user bukan superadmin (misalnya admin), tolak aksi delete
            if ($user && $user->role !== 'superadmin') {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses ditolak. Role Admin tidak memiliki izin untuk menghapus data. Operasi hapus hanya dapat dilakukan oleh Superadmin.'
                    ], 403);
                }

                return redirect()->back()->with('error', 'Akses ditolak. Role Admin tidak memiliki izin untuk menghapus data. Tindakan ini hanya dapat dilakukan oleh Superadmin.');
            }
        }

        return $next($request);
    }
}
