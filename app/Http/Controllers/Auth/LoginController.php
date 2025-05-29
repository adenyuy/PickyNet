<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Tampilkan form login.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login'); // Pastikan Anda memiliki view ini
    }

    /**
     * Tangani proses login user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
// app/Http/Controllers/Auth/LoginController.php

public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        // --- TAMBAHKAN INI UNTUK DEBUGGING ---
        // if (Auth::check()) {
        //     dd('Login Tradisional Berhasil, User terotentikasi!', Auth::user());
        // } else {
        //     dd('Login Tradisional GAGAL, User TIDAK terotentikasi!');
        // }
        // --- HAPUS SETELAH DEBUGGING SELESAI ---

        return redirect()->route('home');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
}

    /**
     * Tangani proses logout user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}