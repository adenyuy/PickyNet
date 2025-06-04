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

    Route::get('/pick', [SpkController::class, 'showPickPage'])->name('spk.pick');

// routes/web.php
    Route::get('/profile', function () {
        $user = Auth::user();
        $allSpkSessions = $user->spkSessions()->latest()->get(); // Ambil semua sesi dulu

        // Filter sesi yang memiliki hasil ranking (final_qi_ranking tidak kosong)
        $completedSpkSessions = $allSpkSessions->filter(function ($session) {
            // $session->final_qi_ranking di-cast sebagai array di model SpkSession
            // Jadi kita cek apakah array tersebut tidak kosong
            return !empty($session->final_qi_ranking);
        });

        return view('profile.index', [
            'user' => $user,
            'spkSessions' => $completedSpkSessions // Kirim data yang sudah difilter
        ]);
    })->name('profile');

    // SPK Flow (Tahap-tahap perhitungan WASPAS)
    Route::prefix('spk')->name('spk.')->group(function () {
        // Menggantikan spk.process-data. Ini memulai sesi baru.
        Route::post('/session/start', [SpkController::class, 'startNewSpkSession'])->name('session.start');

        // Menampilkan overview dari sesi SPK yang aktif (dari PHP session)
        Route::get('/overview', [SpkController::class, 'showOverview'])->name('overview');

        // Menampilkan halaman penilaian untuk sesi SPK yang aktif
        Route::get('/assessment', [SpkController::class, 'showAssessmentPage'])->name('assessment.show');

        // Menyimpan skor untuk SATU alternatif via AJAX
        Route::post('/assessment/store-alternative-score', [SpkController::class, 'storeAlternativeScore'])->name('assessment.store.alternative');

        // Memfinalisasi semua penilaian & menjalankan kalkulasi WASPAS
        // Dipanggil setelah semua alternatif dinilai (misal, tombol "Selesai & Hitung")
        Route::post('/assessment/finalize', [SpkController::class, 'finalizeAndProcessSpk'])->name('assessment.finalize');

        // Menampilkan halaman detail hasil perhitungan untuk sesi SPK tertentu
        Route::get('/calculation/{sessionId}', [SpkController::class, 'showCalculationPage'])->name('calculation.show');

        // Menampilkan halaman ranking akhir untuk sesi SPK tertentu
        Route::get('/rank/{sessionId}', [SpkController::class, 'showRankPage'])->name('rank.show');

        // Rute lama yang mungkin tidak diperlukan lagi dengan SpkSession:
        // Route::get('/get-calculation-data', [SpkController::class, 'getCalculationData'])->name('get-calculation-data');
    });

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});