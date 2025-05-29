<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PickController;
use App\Http\Controllers\SpkController; // Pastikan controller Anda diimpor

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman Welcome (landing page awal)
// Jika user sudah login, arahkan ke home. Ini mencegah user yang sudah login melihat welcome lagi.
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home'); // Atau ke 'description' jika itu onboarding pertama kali
    }
    return view('welcome');
})->name('welcome');


// --- Routes Otentikasi (Guest Middleware) ---
// Rute ini hanya bisa diakses oleh user yang BELUM login
Route::middleware('guest')->group(function () {
    // Login Tradisional
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Registrasi Tradisional
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Google Auth
    Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
});


// --- Routes yang Membutuhkan Otentikasi (Auth Middleware) ---
// Rute ini hanya bisa diakses oleh user yang SUDAH login
Route::middleware('auth')->group(function () {

    // Deskripsi Page (Setelah Register/Login pertama kali)
    Route::get('/description', function () {
        return view('description');
    })->name('description');

    // Home Page (dengan 4 navigasi)
    Route::get('/home', function () {
        return view('home');
    })->name('home');

    Route::get('/developer', function () {
        return view('developer'); // Ini akan mencari file resources/views/developer.blade.php
    })->name('developer');

    // Navigasi Bar:
    Route::get('/provider', function () {
        return view('provider.index');
    })->name('provider');
    Route::get('/guide', function () {
        return view('guide.index');
    })->name('guide');

    Route::get('/pick', [PickController::class, 'index'])->name('pick');

    Route::get('/profile', function () {
        $user = Auth::user();
        $logs = $user->logs()->latest()->get(); // Pastikan relasi logs ada di model User
        return view('profile.index', compact('user', 'logs'));
    })->name('profile');

    // SPK Flow (Tahap-tahap perhitungan WASPAS)
    Route::prefix('spk')->group(function () {
        Route::get('/overview', [SpkController::class, 'overview'])->name('spk.overview'); // Pindahkan definisi overview ke controller
        Route::post('/process-data', [SpkController::class, 'processData'])->name('spk.process-data'); // <--- PERBAIKAN DI SINI
        Route::get('/assessment', [SpkController::class, 'assessment'])->name('spk.assessment');
        Route::post('/save-assessment', [SpkController::class, 'saveAssessment'])->name('spk.save-assessment');
        Route::get('/calculation', [SpkController::class, 'calculation'])->name('spk.calculation'); // Render halaman kosong/skeleton
        Route::get('/get-calculation-data', [SpkController::class, 'getCalculationData'])->name('spk.get-calculation-data');
        Route::get('/rank', [SpkController::class, 'rank'])->name('spk.rank');
    });

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});