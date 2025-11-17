<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    public function index()
    {
        $data = User::where('id', '!=', auth()->id())->latest()->paginate(10);
        return view('admin.user-management.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:superadmin,admin',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('user-management.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:superadmin,admin',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('user-management.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // Cegah menghapus diri sendiri
        if ($id == auth()->id()) {
            return redirect()->route('user-management.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user-management.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        // Cegah menonaktifkan diri sendiri
        if ($id == auth()->id()) {
            return redirect()->route('user-management.index')
                ->with('error', 'Tidak dapat menonaktifkan akun sendiri.');
        }

        $user = User::findOrFail($id);
        // Jika ingin menambahkan status aktif/nonaktif, tambahkan field 'is_active' di migration
        // $user->update(['is_active' => !$user->is_active]);

        return redirect()->route('user-management.index')
            ->with('success', 'Status user berhasil diubah.');
    }
}