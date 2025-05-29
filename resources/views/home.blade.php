@extends('layouts.app') {{-- Meng-extend layout utama 'app.blade.php' --}}

@section('content')
    <div class="relative bg-blue-500 min-h-[calc(100vh-64px)] flex items-center justify-center text-white py-16 px-4 md:px-0">
        <div class="container mx-auto text-center"> {{-- Container utama untuk seluruh konten tengah --}}
            <div class="flex flex-col md:flex-row items-center justify-center md:justify-between space-y-12 md:space-y-0 md:space-x-12 mb-16">
                <div class="w-full md:w-1/2 text-center md:text-left">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                        Stop Tanya Grup WA, <br> Pakai Pickynet Aja!
                    </h1>
                    <p class="text-lg md:text-xl mb-6 max-w-2xl mx-auto md:mx-0">
                        Tersedia banyak pilihan provider
                    </p>
                    <img src="{{ asset('storage/providers/all-provider.png') }}" alt="IndiHome" class="max-h-xs max-w-xs object-contain">
                </div>

                <div class="w-full md:w-1/2 flex justify-center md:justify-end">
                    <img src="{{ asset('storage/images/deskripsi1.png') }}" alt="PickyNet Illustration" class="max-w-xs md:max-w-sm lg:max-w-md h-auto">
                </div>
            </div>
        </div>
    </div>
@endsection