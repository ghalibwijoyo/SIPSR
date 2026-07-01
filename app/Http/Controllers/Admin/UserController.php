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
            'nik' => ['required', 'string', 'max:255', 'unique:users,nik'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'regex:/[a-zA-Z]/', 'regex:/[0-9]/'],
            'role' => ['required', Rule::in(['ADMIN', 'STAFF'])],
        ], [
            'password.regex' => 'Password harus mengandung kombinasi huruf dan angka.',
            'nik.unique' => 'NIK sudah digunakan, silakan pilih yang lain.',
        ]);

        $user = User::create([
            'nik' => $validated['nik'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        ActivityLog::log('TAMBAH_USER', 'Menambahkan pengguna baru: '.$user->nik);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(['ADMIN', 'STAFF'])],
            'is_active' => ['required', 'boolean'],
        ], [
            'nik.unique' => 'NIK sudah digunakan, silakan pilih yang lain.',
        ]);

        $user->update([
            'nik' => $validated['nik'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'role' => $validated['role'],
            'is_active' => $validated['is_active'],
        ]);

        ActivityLog::log('EDIT_USER', 'Memperbarui data pengguna: '.$user->nik);

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function toggleActive(User $user)
    {
        // Don't allow an admin to deactivate themselves
        if (request()->user()->id === $user->id) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->is_active = ! $user->is_active;
        if (!$user->is_active) {
            $user->deactivated_at = now();
        } else {
            $user->deactivated_at = null;
        }
        $user->save();

        $action = $user->is_active ? 'AKTIFKAN_USER' : 'NONAKTIFKAN_USER';
        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::log($action, ucfirst($statusText).' pengguna: '.$user->nik);

        return redirect()->route('users.index')->with('success', "Pengguna {$user->nik} berhasil $statusText.");
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

        ActivityLog::log('RESET_PASSWORD', 'Mereset password pengguna: '.$user->nik);

        return redirect()->route('users.index')
            ->with('success', 'Password berhasil direset.')
            ->with('new_password_info', [
                'nik' => $user->nik,
                'password' => $validated['new_password'],
            ]);
    }

    public function destroy(User $user)
    {
        if (request()->user()->id === $user->id) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->is_active) {
            return redirect()->route('users.index')->with('error', 'Akun harus dinonaktifkan terlebih dahulu sebelum dapat dihapus.');
        }

        if (!$user->deactivated_at || $user->deactivated_at->copy()->addMonths(3)->isFuture()) {
            return redirect()->route('users.index')->with('error', 'Akun hanya dapat dihapus jika telah nonaktif selama minimal 3 bulan.');
        }

        if ($user->documents()->exists()) {
            return redirect()->route('users.index')->with('error', 'Akun tidak dapat dihapus karena pengguna ini telah mengunggah dokumen.');
        }

        $nik = $user->nik;
        $user->delete();

        ActivityLog::log('HAPUS_USER', 'Menghapus pengguna: '.$nik);

        return redirect()->route('users.index')->with('success', "Pengguna {$nik} berhasil dihapus.");
    }
}
