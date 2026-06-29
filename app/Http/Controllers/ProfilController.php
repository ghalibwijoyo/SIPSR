<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function show()
    {
        $user = request()->user();

        return view('profil.index', compact('user'));
    }

    public function updateNama(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $oldName = $user->nama_lengkap;
        $user->nama_lengkap = $validated['nama_lengkap'];
        $user->save();

        if ($oldName !== $user->nama_lengkap) {
            ActivityLog::create([
                'user_id' => $user->id,
                'role_saat_itu' => $user->role,
                'jenis_aktivitas' => 'EDIT_USER',
                'detail' => "Memperbarui nama profil dari $oldName menjadi ".$user->nama_lengkap,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        }

        return redirect()->route('profil.show')->with('success', 'Nama profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'password_lama' => ['required', 'string'],
            'password_baru' => ['required', 'string', 'min:8', 'regex:/[a-zA-Z]/', 'regex:/[0-9]/', 'confirmed'],
        ], [
            'password_baru.regex' => 'Password baru harus mengandung kombinasi huruf dan angka.',
            'password_baru.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = $request->user();

        if (! Hash::check($validated['password_lama'], $user->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        $user->password = Hash::make($validated['password_baru']);
        $user->save();

        ActivityLog::create([
            'user_id' => $user->id,
            'role_saat_itu' => $user->role,
            'jenis_aktivitas' => 'EDIT_USER',
            'detail' => 'Memperbarui password akun sendiri.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('profil.show')->with('success', 'Password berhasil diperbarui.');
    }
}
