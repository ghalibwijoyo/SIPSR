<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nik' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            // Cek apakah akun aktif
            if (! Auth::user()->is_active) {
                ActivityLog::log('LOGIN_DITOLAK', 'Mencoba login namun akun berstatus nonaktif');
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->wantsJson()) {
                    return response()->json([
                        'message' => 'Akun Anda dinonaktifkan. Hubungi Admin.',
                        'errors' => ['nik' => ['Akun Anda dinonaktifkan. Hubungi Admin.']]
                    ], 422);
                }

                return back()->withErrors([
                    'nik' => 'Akun Anda dinonaktifkan. Hubungi Admin.',
                ])->onlyInput('nik');
            }

            // Invalidate other sessions for this user
            Auth::logoutOtherDevices($request->password);

            $request->session()->regenerate();

            // Activity log
            ActivityLog::log('LOGIN_BERHASIL', 'User berhasil login ke sistem');

            if ($request->wantsJson()) {
                return response()->json(['redirect' => session()->pull('url.intended', '/dashboard')]);
            }

            return redirect()->intended('/dashboard')->with('login_success', true);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'NIK atau password salah.',
                'errors' => ['nik' => ['NIK atau password salah.']]
            ], 422);
        }

        return back()->withErrors([
            'nik' => 'NIK atau password salah.',
        ])->onlyInput('nik');
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            ActivityLog::log('LOGOUT_BERHASIL', 'User berhasil logout dari sistem');
        }
        
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
