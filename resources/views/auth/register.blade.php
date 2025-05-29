<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun PickyNet</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-[#00BCF1] min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-xl bg-transparent text-center flex flex-col items-center space-y-6">

        {{-- Judul dan Deskripsi --}}
        <h1 class="text-white text-3xl font-bold mt-12">Daftar Akun</h1>
        <p class="text-white text-base font-normal">
            Masukkan informasi dibawah ini untuk mendaftarkan akun anda
        </p>

        {{-- Gambar Logo Register --}}
        <img src="{{ asset('storage/images/logo-register.png') }}" alt="Register Image" class="w-[150px] h-auto">

        {{-- Card Form + Footer Login --}}
        <div class="bg-white rounded-xl w-full overflow-hidden shadow-lg text-left mb-12">

            {{-- Form Section --}}
            <div class="px-6 py-8 space-y-4">
                {{-- Error Message --}}
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <ul class="text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf
                    <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required autofocus class="w-full border-b-2 border-gray-300 focus:outline-none focus:border-[#00BCF1] py-2">
                    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required class="w-full border-b-2 border-gray-300 focus:outline-none focus:border-[#00BCF1] py-2">
                    <input type="password" name="password" placeholder="Password" required autocomplete="new-password" class="w-full border-b-2 border-gray-300 focus:outline-none focus:border-[#00BCF1] py-2">
                    <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required autocomplete="new-password" class="w-full border-b-2 border-gray-300 focus:outline-none focus:border-[#00BCF1] py-2">

                    <button type="submit" class="hidden">Daftar</button>
                </form>
            </div>

            {{-- Footer Login Section (menempel dengan card) --}}
            <a href="{{ route('login') }}" class="flex items-center justify-between bg-[#1B1E23] text-white px-6 py-4">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('storage/images/enter.png') }}" alt="Enter Icon" class="w-5 h-5">
                    <span class="text-sm">Sudah punya akun? klik <strong>Disini</strong></span>
                </div>
                <span class="text-lg font-semibold">&gt;</span>
            </a>
        </div>
    </div>
</body>
</html>
