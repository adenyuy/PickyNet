<!DOCTYPE html>
<html lang="id"> {{-- Mengganti lang ke 'id' --}}

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Penting untuk form logout --}}
    <title>Profil Pengguna - PickyNet</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <style>
        /* Tambahan style jika diperlukan, misalnya untuk font */
        body {
            font-family: 'Poppins', sans-serif;
            /* Contoh jika Anda ingin menggunakan Poppins */
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">

    {{-- Header Profil (Biru) --}}
    <div class="relative bg-blue-500 h-60 sm:h-64 flex flex-col items-center justify-end pb-10 pt-16 sm:pt-10"> {{--
        Penyesuaian padding dan tinggi --}}

        {{-- Tombol Kembali (panah kiri) --}}
        <a href="{{ route('home') }}" title="Kembali ke Home"
            class="absolute top-4 left-4 text-white p-2 rounded-full hover:bg-blue-600 transition-colors duration-200">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
        </a>

        {{-- Tombol Logout --}}
        <form method="POST" action="{{ route('logout') }}" class="absolute top-4 right-4">
            @csrf
            <button type="submit" title="Logout"
                class="text-white p-2 rounded-full hover:bg-blue-600 transition-colors duration-200">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
            </button>
        </form>

        {{-- Avatar Profil --}}
        <div class="mb-3">
            @if(Auth::user()->avatar)
                {{-- Jika avatar dari Google atau path lengkap --}}
                @if(filter_var(Auth::user()->avatar, FILTER_VALIDATE_URL))
                    <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}"
                        class="w-28 h-28 sm:w-32 sm:h-32 rounded-full object-cover border-4 border-white shadow-lg">
                @else
                    {{-- Jika avatar adalah path relatif dari storage --}}
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}"
                        class="w-28 h-28 sm:w-32 sm:h-32 rounded-full object-cover border-4 border-white shadow-lg">
                @endif
            @else
                {{-- Placeholder avatar jika tidak ada --}}
                <div
                    class="w-28 h-28 sm:w-32 sm:h-32 rounded-full bg-blue-700 border-4 border-white shadow-lg flex items-center justify-center">
                    <span
                        class="text-4xl sm:text-5xl text-white font-semibold">{{ strtoupper(substr(Auth::user()->name ?? Auth::user()->username ?? 'U', 0, 1)) }}</span>
                </div>
            @endif
        </div>

        {{-- Nama Pengguna --}}
        <h1 class="text-white text-xl sm:text-2xl font-bold mb-0.5">{{ Auth::user()->name ?? Auth::user()->username }}
        </h1>

        {{-- Email Pengguna --}}
        <p class="text-blue-100 text-sm sm:text-md">{{ Auth::user()->email }}</p>
    </div>

    {{-- Section History --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 -mt-6 sm:-mt-8 relative z-10 pb-10">
        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-xl">
            <h2 class="text-xl font-bold text-blue-600 mb-4 border-b pb-2">Riwayat Perbandingan (SPK)</h2>
            <div class="space-y-4">
                @forelse ($spkSessions as $session) {{-- Variabel ini akan sudah difilter dari controller --}}
                    <a href="{{ route('spk.rank.show', ['sessionId' => $session->id]) }}"
                        class="block hover:shadow-md transition-shadow duration-200">
                        {{-- ... (kode tampilan item riwayat tetap sama) ... --}}
                        <div
                            class="bg-blue-50 hover:bg-blue-100 border border-blue-200 text-gray-700 p-4 rounded-lg shadow-sm flex justify-between items-center">
                            <div>
                                <p class="text-md sm:text-lg font-semibold text-blue-700">
                                    {{ $session->session_name ?: 'Sesi SPK pada ' . $session->created_at->translatedFormat('d F Y, H:i') }}
                                </p>
                                @if(!empty($session->final_qi_ranking) && isset($session->final_qi_ranking[0]))
                                    <p class="text-sm text-gray-600">
                                        Rekomendasi utama: <span
                                            class="font-medium">{{ $session->final_qi_ranking[0]['alternative_name'] }}</span>
                                        (Skor: {{ number_format($session->final_qi_ranking[0]['final_qi'], 4) }})
                                    </p>
                                @else
                                    {{-- Ini seharusnya tidak muncul jika kita filter di controller --}}
                                    <p class="text-sm text-gray-500">Hasil perhitungan tidak lengkap.</p>
                                @endif
                            </div>
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </div>
                    </a>
                @empty
                    <div class="bg-gray-50 text-gray-600 p-6 rounded-lg shadow-sm text-center">
                        <p>Anda belum memiliki riwayat perbandingan SPK yang telah selesai.</p>
                        <a href="{{ route('spk.pick') }}" class="mt-2 inline-block text-blue-600 hover:underline">Mulai
                            perbandingan baru?</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</body>

</html>