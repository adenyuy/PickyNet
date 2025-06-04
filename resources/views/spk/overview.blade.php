@extends('layouts.spk_flow')

@section('title', 'Overview Pilihan')
@section('progress_width', '25%')
@section('back_link_route', route('spk.pick'))
@section('page_header_title', 'Overview Pilihan Anda')

@section('content')
    {{-- Kontainer utama untuk menengahkan dan membatasi lebar konten halaman overview --}}
    <div class="w-full max-w-4xl mx-auto flex flex-col items-center">

        {{-- Grid untuk kartu Alternatif dan Kriteria --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full mb-10"> {{-- Tambahkan mb-10 untuk spasi sebelum tombol --}}
            {{-- Alternatif Card --}}
            <div class="bg-white rounded-xl shadow-lg p-6 relative">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">Alternatif Dipilih</h2>
                    <a href="{{ route('spk.pick') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-4 rounded-full text-sm transition duration-200">
                        Edit Pilihan
                    </a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @if($selectedAlternativeDetails->isNotEmpty())
                        @foreach($selectedAlternativeDetails as $alternative)
                            <div class="border border-gray-300 rounded-lg p-3 flex flex-col items-center justify-center h-28">
                                <img src="{{ asset('storage/' . $alternative->image_path) }}" alt="{{ $alternative->name }}" class="max-h-12 object-contain mb-1">
                                <span class="text-xs text-gray-700 mt-1 font-medium text-center leading-tight">{{ $alternative->name }}</span>
                            </div>
                        @endforeach

                        @php
                            $selectedCount = $selectedAlternativeDetails->count();
                            $minDisplaySlots = $selectedCount < 2 ? 2 : ($selectedCount <= 3 ? 3 : ($selectedCount <= 4 ? 4 : 6) );
                            $maxDisplaySlots = $minDisplaySlots; // Default sama dengan min
                            if ($selectedCount > 0 && $selectedCount < 6) { // Tampilkan hingga 6 slot jika ada yg dipilih
                                $maxDisplaySlots = ($selectedCount % 2 != 0 && $selectedCount < 3) ? $selectedCount +1 : 6 ; // Jika ganjil & <3, buat genap, selain itu 6
                                if ($selectedCount == 1) $maxDisplaySlots = 2; // khusus jika 1
                                if ($selectedCount == 3 && $selectedCount < 4) $maxDisplaySlots = 4; // jika 3, tampilkan 4
                                if ($selectedCount > 3 && $selectedCount < 6) $maxDisplaySlots = 6; // jika >3 & <6, tampilkan 6

                            }
                        @endphp
                        {{-- Logika placeholder disederhanakan untuk selalu mencoba mengisi hingga kelipatan 2 atau 3 yang masuk akal, maks 6 --}}
                        @for ($i = $selectedCount; $i < $maxDisplaySlots && $selectedCount > 0 && $selectedCount < 6; $i++)
                            <div class="border border-dashed border-gray-300 rounded-lg p-3 flex items-center justify-center h-28">
                                <span class="text-gray-400 text-sm">Pilihan</span>
                            </div>
                        @endfor
                    @else
                        <p class="col-span-full text-gray-600 text-center py-10">Tidak ada alternatif yang dipilih.</p>
                    @endif
                </div>
            </div>

            {{-- Kriteria Card --}}
            <div class="bg-white rounded-xl shadow-lg p-6 relative">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">Urutan Kriteria</h2>
                    <a href="{{ route('spk.pick') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-4 rounded-full text-sm transition duration-200">
                        Edit Urutan
                    </a>
                </div>
                @if($rankedCriteriaDetails->isNotEmpty())
                    <ul class="space-y-3">
                        @foreach($rankedCriteriaDetails as $criteria)
                            <li class="flex items-center space-x-3 text-lg text-gray-700">
                                <span class="font-bold text-blue-600 w-6 text-right">{{ $criteria['rank'] }}.</span>
                                <span class="flex-1">{{ $criteria['name'] }}</span>
                                <span class="text-xs text-gray-500">(Bobot: {{ number_format($criteria['weight'], 4) }})</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-600 text-center py-10">Tidak ada kriteria yang diurutkan.</p>
                @endif
            </div>
        </div>

        {{-- Tombol Lanjut ke Penilaian atau Pesan Error --}}
        <div class="w-full text-center"> {{-- Dibuat full-width agar text-center bekerja --}}
            @if($selectedAlternativeDetails->isNotEmpty() && $rankedCriteriaDetails->isNotEmpty())
                <a href="{{ route('spk.assessment.show') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-lg text-lg transition duration-300 inline-block">
                    Lanjut ke Penilaian Alternatif
                </a>
            @else
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md" role="alert">
                    <p class="font-bold">Pilihan Belum Lengkap</p>
                    <p>Harap pilih minimal 2 alternatif dan urutkan kriteria di halaman <a href="{{ route('spk.pick') }}" class="font-semibold underline hover:text-yellow-800">Pemilihan Awal</a> untuk melanjutkan.</p>
                </div>
            @endif
        </div>

    </div>
@endsection