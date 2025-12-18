<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
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
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        // Validasi input yang lebih sederhana
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'current_password'],
            'new_password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-zA-Z]/',    // minimal ada huruf
                'regex:/[0-9]/',       // minimal ada angka
            ],
            'new_password_confirmation' => ['required'],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'current_password.current_password' => 'Password lama tidak sesuai.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak sesuai.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.regex' => 'Password baru harus mengandung minimal 1 huruf dan 1 angka.',
            'new_password_confirmation.required' => 'Konfirmasi password baru wajib diisi.',
        ]);

        // Custom validation message untuk regex
        $validator->sometimes('new_password.regex', function ($input) {
            return preg_match('/[a-zA-Z]/', $input->new_password) && 
                   preg_match('/[0-9]/', $input->new_password);
        }, function ($input) {
            return !(preg_match('/[a-zA-Z]/', $input->new_password) && 
                    preg_match('/[0-9]/', $input->new_password));
        });

        // Jika validasi gagal
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal mengubah password. Silakan periksa kembali data yang Anda masukkan.');
        }

        try {
            // Update password
            $request->user()->update([
                'password' => Hash::make($request->new_password),
            ]);

            // Return success response
            return redirect()->back()
                ->with('success', 'Password berhasil diperbarui!');

        } catch (\Exception $e) {
            // Log error
            \Log::error('Password update error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan. Silakan coba lagi.')
                ->withInput();
        }
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
