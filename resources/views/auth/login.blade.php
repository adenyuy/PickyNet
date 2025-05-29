<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login ke PickyNet</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-[#00BCF1] min-h-screen flex items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col md:flex-row">

        {{-- Left Side - Image --}}
        <div class="md:w-1/2 bg-[#26619C] flex items-center justify-center p-10">
            <img src="{{ asset('storage/images/welcome-login.png') }}" alt="Welcome" class="w-72 md:w-80">
        </div>

        {{-- Right Side - Login Form --}}
        <div class="md:w-1/2 p-8 md:p-10 space-y-6">
            {{-- Title --}}
            <div class="text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-[#1B1E23]">Selamat Datang</h2>
                <p class="text-[#2B4F81] font-medium text-sm mt-1">Masuk ke akun PickyNet Anda</p>
            </div>

            {{-- Google Login --}}
            <a href="{{ route('auth.google') }}"
               class="w-full flex items-center justify-center border border-gray-300 rounded-full py-3 px-6 bg-white hover:bg-gray-100 shadow transition duration-200">
                <img src="{{ asset('storage/images/google-logo.png') }}" alt="Google Logo" class="w-5 h-5 mr-3">
                <span class="text-gray-700 font-medium text-base">Masuk dengan Gmail</span>
            </a>

            {{-- Divider --}}
            <div class="flex items-center space-x-4 text-sm text-gray-500">
                <div class="flex-1 border-t border-gray-300"></div>
                <span>atau</span>
                <div class="flex-1 border-t border-gray-300"></div>
            </div>

            {{-- Error Message --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul class="text-sm list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           placeholder="Email atau Username"
                           class="w-full border-b-2 border-gray-300 focus:outline-none focus:border-[#00BCF1] py-2 placeholder-gray-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <input type="password" name="password" id="password" required
                           placeholder="Password"
                           class="w-full border-b-2 border-gray-300 focus:outline-none focus:border-[#00BCF1] py-2 placeholder-gray-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me & Forgot Password --}}
                <div class="flex items-center justify-between text-sm">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="remember" class="form-checkbox text-[#00BCF1] mr-2">
                        Remember me
                    </label>
                    <a href="#" class="text-[#00BCF1] hover:underline">Lupa Password?</a>
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                        class="w-full bg-[#00BCF1] hover:bg-gray-900 text-white font-semibold py-3 rounded-full flex items-center justify-center transition duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Login
                </button>
            </form>

            {{-- Footer --}}
            <p class="text-center text-sm text-gray-600">Belum punya akun? 
                <a href="{{ route('register') }}" class="text-[#00BCF1] font-semibold hover:underline">Daftar disini</a>
            </p>
        </div>
    </div>
</body>
</html>
