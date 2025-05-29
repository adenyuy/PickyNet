<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - PickyNet</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body class="bg-gray-100 min-h-screen"> {{-- Menggunakan bg-gray-100 untuk background body --}}

    {{-- Header Profil (Biru) --}}
    <div class="relative bg-blue-500 h-64 flex flex-col items-center justify-center pt-10 mb-24"> {{-- bg-blue-500 adalah #00BFFF --}}

        {{-- Tombol Kembali (panah kiri) --}}
        <a href="{{ route('home') }}" class="absolute top-5 left-5 p-2 rounded-full text-white">
            <img src="{{ asset('storage/images/back.png') }}" alt="Back to Home" class="w-8 h-8">
        </a>

        {{-- Tombol Logout --}}
        <form method="POST" action="{{ route('logout') }}" class="absolute top-5 right-5 text-white font-bold">
            @csrf
            <button type="submit">Logout</button>
        </form>

        {{-- Avatar Profil (Placeholder) --}}
        <div class="mb-2">
            {{-- Ganti dengan avatar asli jika ada, contoh: '' --}}
            <img src="{{ Auth::user()->avatar}}" alt="User Avatar"
                 class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">
                 
        </div>

        {{-- Nama Pengguna --}}
        <h1 class="text-white text-2xl font-bold mb-1">{{ Auth::user()->name }}</h1>

        {{-- Email Pengguna --}}
        <p class="text-white text-md">{{ Auth::user()->email }}</p>
    </div>

    {{-- Section History --}}
    <div class="container mx-auto px-4 -mt-16 relative z-10"> {{-- -mt-16 untuk membuat overlap dengan header --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold text-blue-500 mb-4">History</h2>

            <div class="space-y-3">
                {{-- Contoh Log History (untuk nanti, bisa di-loop dari $logs) --}}
                @forelse ($logs as $log)
                    <div class="bg-blue-500 text-white p-4 rounded-lg shadow-sm flex justify-between items-center">
                        <div>
                            <p class="text-lg font-semibold">{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y') }}</p>
                            {{-- Sesuaikan $log->payload dengan data yang ingin ditampilkan --}}
                            <p class="text-sm">{{ $log->payload ?? 'N/A' }}</p>
                        </div>
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                @empty
                    <div class="bg-blue-100 text-blue-800 p-4 rounded-lg shadow-sm text-center">
                        <p>No history found yet.</p>
                    </div>
                @endforelse

                {{-- Contoh item history statis jika ingin melihat tampilannya tanpa data --}}
                <div class="bg-blue-500 text-white p-4 rounded-lg shadow-sm flex justify-between items-center">
                    <div>
                        <p class="text-lg font-semibold">25/12/2025</p>
                        <p class="text-sm">Indihome</p>
                    </div>
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
                <div class="bg-blue-500 text-white p-4 rounded-lg shadow-sm flex justify-between items-center">
                    <div>
                        <p class="text-lg font-semibold">20/11/2025</p>
                        <p class="text-sm">First Media</p>
                    </div>
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</body>
</html>