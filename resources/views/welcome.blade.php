<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di PickyNet</title>
    @vite('resources/css/app.css') {{-- Pastikan Tailwind + Vite sudah di-setup --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-[#00BCF1] min-h-screen flex items-center justify-center p-4">
    <div class="relative w-full max-w-6xl h-[600px] bg-[#00BCF1] rounded-lg overflow-hidden flex flex-col md:flex-row items-center justify-center">

        {{-- Gambar Ilustrasi Kiri --}}
        <div class="w-full md:w-1/2 flex items-center justify-center p-4">
            <img src="{{ asset('storage/images/welcome.webp') }}" alt="PickyNet Illustration" class="max-w-full h-auto object-contain">
        </div>

        {{-- Konten Login --}}
        <div class="w-full md:w-1/2 flex flex-col items-center justify-center p-4 text-white text-center space-y-6">

            {{-- Judul --}}
            <h1 class="text-[20px] md:text-[40px] font-bold leading-tight">Selamat Datang<span class="text-white"> di PickyNet!</span></h1>

            {{-- Deskripsi --}}
            <p class="text-[20px] md:text-[15px] font-normal max-w-md">
                Masuk/daftar ke akun PickyNet untuk bisa menggunakan sistem ini!
            </p>

            {{-- Tombol Google Login --}}
            <a href="{{ route('auth.google') }}" class="w-full max-w-sm bg-white text-gray-800 py-4 px-6 rounded-full flex items-center justify-center shadow-md hover:bg-gray-100 transition duration-200">
                <img src="{{ asset('storage/images/google-logo.png') }}" alt="Google Logo" class="w-6 h-6 mr-4">
                <span class="text-lg font-semibold">Gunakan Gmail</span>
            </a>

            {{-- Tombol Login Tradisional --}}
            <a href="{{ route('login') }}" class="w-full max-w-sm bg-[#1B1E23] text-white py-4 px-6 rounded-full flex items-center justify-center shadow-md hover:bg-gray-700 transition duration-200">
                <img src="{{ asset('storage/images/logo-login.png') }}" alt="Login Logo" class="w-6 h-6 mr-4">
                <span class="text-lg font-semibold">Login</span>
            </a>

            {{-- Link Registrasi --}}
            <p class="text-[20px] md:text-[15px] font-medium">
                Belum punya akun? segera <a href="{{ route('register') }}" class="underline font-semibold">daftarkan</a> akun anda
            </p>
        </div>
    </div>
</body>
</html>
