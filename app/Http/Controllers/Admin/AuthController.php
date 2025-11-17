<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login admin.
     * Jika user sudah login dan punya role admin/superadmin -> redirect ke dashboard.
     */
    public function showLoginForm()
    {
        // Jika sudah login
        if (Auth::check()) {
            $user = Auth::user();

            // jika user punya role admin/superadmin, redirect ke dashboard admin
            if (in_array($user->role, ['admin', 'superadmin'])) {
                return redirect()->route('admin.dashboard');
            }

            // Jika login tapi bukan admin, logout supaya tidak tetap berada di session
            Auth::logout();
        }

        return view('admin.login');
    }

    /**
     * Proses login admin.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        // Coba login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // <- PENTING: regenerate session setelah login

            $user = Auth::user();

            // Cek role, hanya izinkan admin / superadmin
            if (in_array($user->role, ['admin', 'superadmin'])) {
                // Simpan data ke session (opsional — Auth::user() juga tersedia)
                session([
                    'user_id'    => $user->id,
                    'user_name'  => $user->name,
                    'user_email' => $user->email,
                    'user_role'  => $user->role,
                ]);

                return redirect()->route('admin.dashboard')->with('success', 'Selamat datang, ' . $user->name);
            }

            // kalau bukan admin, langsung logout dan kasih pesan
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors(['email' => 'Akses ditolak.'])->withInput();
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
    }

    /**
     * Logout admin.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // invalidate session & regen CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('backend.login')->with('success', 'Anda telah logout.');
    }
}
