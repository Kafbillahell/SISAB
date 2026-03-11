<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan halaman web utama untuk proses login pengguna
    public function showLogin() {
        return view('auth.login');
    }

    // Memproses data kredensial (email dan password) yang dikirimkan saat login
    // Jika valid, akan membuat sesi baru untuk pengguna
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Logika berdasarkan role jika diperlukan nanti
            return redirect()->intended('/siswas');
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak sesuai.',
        ])->onlyInput('email');
    }

    // Menghapus sesi aktif pengguna saat ini dan mengarahkannya kembali ke halaman login
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}