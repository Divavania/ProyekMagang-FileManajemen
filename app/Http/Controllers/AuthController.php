<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input form
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cek apakah email ada di database
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email tidak ditemukan!');
        }

        // Cek status user
        if ($user->status !== 'aktif') {
            return back()->with('error', 'Akun Anda nonaktif. Hubungi admin.');
        }

        // Cek apakah password cocok
        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Password salah!');
        }

        // Jika semua benar, login user
        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Berhasil logout.');
    }
}
