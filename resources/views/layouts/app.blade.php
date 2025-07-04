<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PickyNet</title>
    {{-- Vite directive untuk mengompilasi CSS dan JS Anda --}}
    @vite('resources/css/app.css')
    @vite('resources/js/app.js') {{-- Pastikan Alpine.js diimpor di app.js Anda --}}
</head>
<body class="bg-gray-100">
    {{-- Header Section --}}
    <nav class="bg-blue-500 p-4 shadow-md sticky top-0 w-full z-50">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-white text-2xl font-bold tracking-tight">PickyNet</a>

            <div class="hidden md:flex space-x-8 text-white font-semibold">
                <a href="{{ route('home') }}" class="hover:text-blue-200 transition duration-300 ease-in-out
                    {{ request()->routeIs('home') ? 'underline underline-offset-4' : '' }}">Home</a>
                <a href="{{ route('provider') }}" class="hover:text-blue-200 transition duration-300 ease-in-out
                    {{ request()->routeIs('provider') ? 'underline underline-offset-4' : '' }}">Provider</a>
                <a href="{{ route('spk.pick') }}" class="hover:text-blue-200 transition duration-300 ease-in-out
                    {{ request()->routeIs('spk.pick') ? 'underline underline-offset-4' : '' }}">Pick</a>
            </div>

            <div class="flex items-center space-x-4 relative">
                @auth
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center text-white px-4 py-2 rounded-full border border-white hover:bg-white hover:text-blue-500 transition duration-300 ease-in-out">
                            <span class="mr-2 font-semibold">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-20"
                             style="display: none;"> {{-- AlpineJS hides this initially --}}
                            <a href="{{ route('profile') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-100
                                {{ request()->routeIs('profile') ? 'bg-gray-100 font-medium' : '' }}">Profile</a>
                            <a href="{{ route('description') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-100
                                {{ request()->routeIs('description') ? 'bg-gray-100 font-medium' : '' }}">Deskripsi</a>
                            <a href="{{ route('developer') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-100
                                {{ request()->routeIs('developer') ? 'bg-gray-100 font-medium' : '' }}">Pengembang</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-white px-4 py-2 rounded-full border border-white hover:bg-white hover:text-blue-500 transition duration-300 ease-in-out
                        {{ request()->routeIs('login') ? 'bg-white text-blue-500' : '' }}">Login</a>
                    <a href="{{ route('register') }}" class="text-blue-500 bg-white px-4 py-2 rounded-full hover:bg-blue-100 transition duration-300 ease-in-out
                        {{ request()->routeIs('register') ? 'ring ring-blue-500 bg-blue-100' : '' }}">Daftar</a>
                @endauth

                {{-- Wrap mobile menu toggle button and mobile menu in a new x-data scope --}}
                <div x-data="{ mobileMenuOpen: false }">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-white focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            {{-- Icon untuk hamburger (menu tertutup) --}}
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                            {{-- Icon untuk 'x' (menu terbuka) --}}
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    {{-- Mobile Menu (Hidden by default, toggled by button above) --}}
                    {{-- Ganti 'hidden' statis dengan ':class' dinamis dan x-show untuk transisi --}}
                    <div x-show="mobileMenuOpen"
                         @click.away="mobileMenuOpen = false" {{-- Tutup menu saat klik di luar --}}
                         class="md:hidden absolute right-0 mt-4 text-blue-600 font-semibold space-y-1 px-1 py-1 rounded-md bg-white w-full"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         style="display: none;"> {{-- AlpineJS hides this initially --}}
                        <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md hover:bg-blue-100
                            {{ request()->routeIs('home') ? 'bg-blue-100' : '' }}">Home</a>
                        <a href="{{ route('provider') }}" class="block px-3 py-2 rounded-md hover:bg-blue-100
                            {{ request()->routeIs('provider') ? 'bg-blue-100' : '' }}">Provider</a>
                        <a href="{{ route('spk.pick') }}" class="block px-3 py-2 rounded-md hover:bg-blue-100
                            {{ request()->routeIs('spk.pick') ? 'bg-blue-100' : '' }}">Pick</a>
                        @auth
                            
                        @else
                            <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md hover:bg-blue-100">Login</a>
                            <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md hover:bg-blue-100">Daftar</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content Section --}}
    <main>
        @yield('content') {{-- Di sinilah konten dari view lain akan disuntikkan --}}
    </main>
</body>
</html>