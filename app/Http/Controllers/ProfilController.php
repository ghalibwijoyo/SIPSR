<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    public function show()
    {
        $user = request()->user();

        return view('profil.index', compact('user'));
    }

    public function updateNama(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
        ]);

        $oldName = $user->nama_lengkap;
        
        $user->nama_lengkap = $validated['nama_lengkap'];
        $user->save();

        if ($oldName !== $user->nama_lengkap) {
            ActivityLog::log('EDIT_USER', 'Memperbarui nama profil.');
        }

        return redirect()->route('profil.show')->with('success', 'Profil berhasil diperbarui.');
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

        ActivityLog::log('EDIT_USER', 'Memperbarui password akun sendiri.');

        return redirect()->route('profil.show')->with('success', 'Password berhasil diperbarui.');
    }
}
