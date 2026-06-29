<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('nama_lengkap')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'regex:/[a-zA-Z]/', 'regex:/[0-9]/'],
            'role' => ['required', Rule::in(['ADMIN', 'STAFF'])],
        ], [
            'password.regex' => 'Password harus mengandung kombinasi huruf dan angka.',
            'username.unique' => 'Username sudah digunakan, silakan pilih yang lain.',
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        ActivityLog::create([
            'user_id' => request()->user()->id,
            'role_saat_itu' => request()->user()->role,
            'jenis_aktivitas' => 'TAMBAH_USER',
            'detail' => 'Menambahkan pengguna baru: '.$user->username,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(['ADMIN', 'STAFF'])],
            'is_active' => ['required', 'boolean'],
        ]);

        $user->update([
            'nama_lengkap' => $validated['nama_lengkap'],
            'role' => $validated['role'],
            'is_active' => $validated['is_active'],
        ]);

        ActivityLog::create([
            'user_id' => request()->user()->id,
            'role_saat_itu' => request()->user()->role,
            'jenis_aktivitas' => 'EDIT_USER',
            'detail' => 'Memperbarui data pengguna: '.$user->username,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function toggleActive(User $user)
    {
        // Don't allow an admin to deactivate themselves
        if (request()->user()->id === $user->id) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $action = $user->is_active ? 'AKTIFKAN_USER' : 'NONAKTIFKAN_USER';
        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::create([
            'user_id' => request()->user()->id,
            'role_saat_itu' => request()->user()->role,
            'jenis_aktivitas' => $action,
            'detail' => ucfirst($statusText).' pengguna: '.$user->username,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('users.index')->with('success', "Pengguna {$user->username} berhasil $statusText.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'new_password' => ['required', 'string', 'min:8', 'regex:/[a-zA-Z]/', 'regex:/[0-9]/'],
        ], [
            'new_password.regex' => 'Password harus mengandung kombinasi huruf dan angka.',
        ]);

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        ActivityLog::create([
            'user_id' => request()->user()->id,
            'role_saat_itu' => request()->user()->role,
            'jenis_aktivitas' => 'RESET_PASSWORD',
            'detail' => 'Mereset password pengguna: '.$user->username,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Password berhasil direset.')
            ->with('new_password_info', [
                'username' => $user->username,
                'password' => $validated['new_password'],
            ]);
    }
}
