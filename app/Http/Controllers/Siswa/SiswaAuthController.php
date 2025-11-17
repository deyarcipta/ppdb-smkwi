<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\UserSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SiswaAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:siswa')->except('logout');
    }

    public function showLoginForm()
    {
        return view('siswa.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cari user berdasarkan username
        $user = UserSiswa::where('username', $request->username)->first();

        // Cek jika user exists dan password cocok
        if ($user && Hash::check($request->password, $user->password)) {
            // Cek jika akun aktif
            if ($user->status_akun != 'aktif') {
                return back()
                    ->with('account_inactive', 'Akun Anda tidak aktif. Silahkan hubungi administrator.')
                    ->onlyInput('username');
            }

            // Login user
            Auth::guard('siswa')->login($user, $request->filled('remember'));
            
            $request->session()->regenerate();
            
            return redirect()->intended(route('siswa.dashboard'))
                ->with('success', 'Login berhasil! Selamat datang di dashboard siswa.');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::guard('siswa')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('siswa.login')
            ->with('success', 'Anda telah berhasil logout.');
    }
}