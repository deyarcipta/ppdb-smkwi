<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use App\Models\UserSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SiswaAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:siswa')->except(['logout', 'updatePassword']);
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

        $user = UserSiswa::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if ($user->status_akun != 'aktif') {
                return back()
                    ->with('account_inactive', 'Akun Anda tidak aktif. Silahkan hubungi administrator.')
                    ->onlyInput('username');
            }

            Auth::guard('siswa')->login($user, $request->filled('remember'));
            $request->session()->regenerate();
            
            return redirect()->intended(route('siswa.dashboard'))
                ->with('success', 'Login berhasil! Selamat datang di dashboard siswa.');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'current_password' => ['required'],
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
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak sesuai.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.regex' => 'Password baru harus mengandung minimal 1 huruf dan 1 angka.',
            'new_password_confirmation.required' => 'Konfirmasi password baru wajib diisi.',
        ]);

        // Custom validation untuk current password
        $validator->after(function ($validator) use ($request) {
            if (!Hash::check($request->current_password, Auth::guard('siswa')->user()->password)) {
                $validator->errors()->add('current_password', 'Password lama tidak sesuai.');
            }
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
            $user = Auth::guard('siswa')->user();
            $user->password = Hash::make($request->new_password);
            $user->save();

            // Return success response
            return redirect()->back()
                ->with('success', 'Password berhasil diperbarui!');

        } catch (\Exception $e) {
            // Log error
            \Log::error('Siswa password update error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan. Silakan coba lagi.')
                ->withInput();
        }
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