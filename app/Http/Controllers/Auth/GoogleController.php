<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('email', $googleUser->getEmail())->first();

            $isNewUser = false;
            if (!$user) {
                $user = User::create([
                    'user_id' => Str::uuid(),
                    'username' => $googleUser->nickname ?? explode('@', $googleUser->getEmail())[0],
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'password' => Hash::make(Str::random(16)),
                ]);
                $isNewUser = true;
            }

            Auth::login($user);


            if ($isNewUser) {
                return redirect()->route('description');
            } else {
                return redirect()->route('home');
            }

        } catch (\Exception $e) {
            Log::error('Google Auth Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Login dengan Google gagal: ' . $e->getMessage());
        }
    }
}