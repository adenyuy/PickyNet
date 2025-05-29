@extends('layouts.app') {{-- Menggunakan layout utama untuk header navigasi --}}

@section('content')
{{-- Kontainer utama halaman dengan background putih --}}
<div class="bg-white min-h-screen">
    <div class="container mx-auto px-4 py-8">
        {{-- Bagian Informasi Utama --}}
        <div class="text-center mb-10">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 text-gray-900">Informasi untuk Kamu yang bingung!</h1>
            <p class="text-lg md:text-xl text-gray-600">Berikut adalah beberapa paket dari provider</p>
        </div>

        {{-- Grid Provider --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Indihome Card --}}
            <div class="bg-white-600 rounded-lg shadow-xl overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <div class="p-6 text-center">
                    <img src="{{ asset('storage/providers/indihome.png') }}" alt="IndiHome" class="h-16 mx-auto mb-4 object-contain">
                </div>
                <a href="#" class="block bg-red-700 py-3 text-center text-white font-semibold flex items-center justify-center space-x-2">
                    <span>Lebih Detail</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            {{-- Biznet Card --}}
            <div class="bg-white-800 rounded-lg shadow-xl overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <div class="p-6 text-center">
                    <img src="{{ asset('storage/providers/biznet.png') }}" alt="Biznet" class="h-16 mx-auto mb-4 object-contain">
                </div>
                <a href="#" class="block bg-blue-900 py-3 text-center text-white font-semibold flex items-center justify-center space-x-2">
                    <span>Lebih Detail</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            {{-- MNC Play Card --}}
            <div class="bg-white-800 rounded-lg shadow-xl overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <div class="p-6 text-center">
                    <img src="{{ asset('storage/providers/mncplay.png') }}" alt="MNC Play" class="h-16 mx-auto mb-4 object-contain">
                </div>
                <a href="#" class="block bg-blue-900 py-3 text-center text-white font-semibold flex items-center justify-center space-x-2">
                    <span>Lebih Detail</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            {{-- First Media Card --}}
            <div class="bg-white-800 rounded-lg shadow-xl overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <div class="p-6 text-center">
                    <img src="{{ asset('storage/providers/firstmedia.png') }}" alt="First Media" class="h-16 mx-auto mb-4 object-contain">
                </div>
                <a href="#" class="block bg-blue-900 py-3 text-center text-white font-semibold flex items-center justify-center space-x-2">
                    <span>Lebih Detail</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            {{-- Oxygen.id Card --}}
            <div class="bg-white-600 rounded-lg shadow-xl overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <div class="p-6 text-center">
                    <img src="{{ asset('storage/providers/oxygen.png') }}" alt="Oxygen.id" class="h-16 mx-auto mb-4 object-contain">
                </div>
                <a href="#" class="block bg-green-700 py-3 text-center text-white font-semibold flex items-center justify-center space-x-2">
                    <span>Lebih Detail</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            {{-- MyRepublic Card --}}
            <div class="bg-white-600 rounded-lg shadow-xl overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <div class="p-6 text-center">
                    <img src="{{ asset('storage/providers/myrepublic.png') }}" alt="MyRepublic" class="h-16 mx-auto mb-4 object-contain">
                </div>
                <a href="#" class="block bg-purple-700 py-3 text-center text-white font-semibold flex items-center justify-center space-x-2">
                    <span>Lebih Detail</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            {{-- Iconnet Card --}}
            <div class="bg-white-600 rounded-lg shadow-xl overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <div class="p-6 text-center">
                    <img src="{{ asset('storage/providers/iconnet.png') }}" alt="Iconnet" class="h-16 mx-auto mb-4 object-contain">
                </div>
                <a href="#" class="block bg-teal-700 py-3 text-center text-white font-semibold flex items-center justify-center space-x-2">
                    <span>Lebih Detail</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

        </div>
    </div>
</div>
@endsection