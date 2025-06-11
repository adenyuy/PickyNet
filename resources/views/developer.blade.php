<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dev Page - PickyNet</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body class="bg-white grid items-start justify-center min-h-screen relative overflow"> {{-- Menggunakan bg-white untuk background --}}

    {{-- Header Area with Back Button and "MEET OUR TEAM" Text --}}
    <div class="absolute top-0 left-0 w-full flex justify-around items-center py-4 z-10 mb-5">
        {{-- Tombol Kembali --}}
        <a href="{{ route('home') }}" class="mr-10 p-2 rounded-full bg-blue-500 text-white shadow-lg">
            <img src="{{ asset('storage/images/back.png') }}" class="w-5 h-5">
        </a>

        {{-- Teks "MEET OUR TEAM" --}}
        <h1 class="text-black text-4xl font-extrabold text-right">MEET<br>OUR TEAM</h1>
    </div>

    {{-- Gambar UI Developer --}}
    {{-- Memastikan gambar mengisi area layar, tetapi tetap mematuhi aspek rasio --}}
    <div class="relative w-full max-w-4xl px-4 pt-12 grid items-center justify-center"> {{-- Menambahkan padding vertikal agar tidak tertutup header --}}
        <img src="{{ asset('storage/images/dev-all.png') }}" alt="Meet Our Team - PickyNet Developers" class="w-full h-auto object-contain z-0">
        
    </div>
    

</body>
</html>