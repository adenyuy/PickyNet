@extends('layouts.spk_flow') {{-- Extend layout baru --}}

@section('title', 'Overview') {{-- Judul halaman --}}

@section('progress_width', '25%') {{-- Lebar progress bar atas untuk Overview --}}

@section('back_link_route', route('pick')) {{-- Rute tombol back --}}

@section('page_header_title', 'Overview') {{-- Judul di header --}}

@section('content') {{-- Konten utama halaman --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full max-w-4xl">
        {{-- Alternatif Card --}}
        <div class="bg-white rounded-xl shadow-lg p-6 relative">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-800">Alternatif</h2>
                <button class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-4 rounded-full text-sm transition duration-200">
                    Edit
                </button>
            </div>
            <div class="grid grid-cols-2 gap-4">
                @forelse($selectedAlternativeDetails as $alternative)
                    <div class="border border-gray-300 rounded-lg p-3 flex flex-col items-center justify-center h-24">
                        <img src="{{ asset('storage/' . $alternative->image_path) }}" alt="{{ $alternative->name }}" class="max-h-12 object-contain">
                        <span class="text-xs text-gray-700 mt-1 font-medium text-center">{{ $alternative->name }}</span>
                    </div>
                @empty
                    <p class="col-span-2 text-gray-600 text-center">Tidak ada alternatif yang dipilih.</p>
                @endforelse

                @php
                    $selectedCount = count($selectedAlternativeDetails);
                    $maxSlots = 6;
                @endphp
                @for ($i = $selectedCount; $i < $maxSlots; $i++)
                    <div class="border border-dashed border-gray-300 rounded-lg p-3 flex items-center justify-center h-24">
                        <span class="text-gray-400 text-sm">Pilih</span>
                    </div>
                @endfor
            </div>
        </div>

        {{-- Kriteria Card --}}
        <div class="bg-white rounded-xl shadow-lg p-6 relative">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-800">Kriteria</h2>
                <button class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-4 rounded-full text-sm transition duration-200">
                    Edit
                </button>
            </div>
            <ul class="space-y-3">
                @forelse($rankedCriteriaDetails as $index => $criteria)
                    <li class="flex items-center space-x-3 text-lg text-gray-700">
                        <span class="font-bold text-blue-600 w-6 text-right">{{ $index + 1 }}.</span>
                        <span>{{ $criteria->name }}</span>
                    </li>
                @empty
                    <p class="text-gray-600 text-center">Tidak ada kriteria yang diurutkan.</p>
                @endforelse
            </ul>
        </div>
    </div>
@endsection