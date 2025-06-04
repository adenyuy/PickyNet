@extends('layouts.spk_flow')

@section('title', 'Ranking Alternatif')
@section('progress_width', '100%') {{-- Tahap terakhir --}}

{{-- Sesuaikan back link untuk kembali ke detail perhitungan sesi ini --}}
@if(isset($spkSession) && $spkSession)
    @section('back_link_route', route('spk.calculation.show', ['sessionId' => $spkSession->id]))
@else
    @section('back_link_route', route('spk.pick')) {{-- Fallback jika $spkSession tidak ada --}}
@endif

@section('page_header_title', 'Hasil Ranking Rekomendasi')

@section('content')
<div x-data="rankingPage()" x-cloak
    class="w-full max-w-xl mx-auto flex flex-col py-6 px-4 bg-white rounded-lg shadow-lg">

    <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Hasil Rekomendasi Terbaik</h2>

    @if (empty($rankingResults))
        <p class="text-center text-gray-600 py-10">Tidak ada hasil ranking yang tersedia untuk sesi ini.</p>
    @else
        <ul class="divide-y divide-gray-200">
            {{-- $rankingResults adalah array yang sudah diurutkan dari controller --}}
            @foreach($rankingResults as $result) {{-- $index tidak lagi dari loop, tapi dari $result['rank'] --}}
                <li class="py-4 px-2 sm:px-6 flex items-center justify-between {{ $result['rank'] == 1 ? 'bg-blue-50 border-2 border-blue-500 rounded-lg transform scale-105 shadow-md' : 'border-b border-gray-200' }} transition-all duration-300 mb-2">
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <span class="text-xl sm:text-2xl font-bold w-8 text-center {{ $result['rank'] == 1 ? 'text-blue-600' : 'text-gray-500' }}">#{{ $result['rank'] }}</span>
                        <div class="flex-shrink-0">
                             {{-- Anda bisa tambahkan gambar alternatif di sini jika mau --}}
                             @php
                                 // Cari detail alternatif untuk gambar, ini asumsi $spkSession->selected_alternatives
                                 // dan $alternativesFromDb ada dan dikirim ke view, atau embed image_path di $rankingResults
                                 // Cara sederhana: jika $result punya 'alternative_image_path'
                                 // Jika tidak, Anda perlu memuat detail alternatif berdasarkan $result['alternative_id']
                                 // $alternativeDetail = \App\Models\Alternative::find($result['alternative_id']);
                             @endphp
                             {{-- <img class="h-10 w-10 sm:h-12 sm:w-12 rounded-full object-contain border p-0.5" src="{{ asset('storage/' . ($alternativeDetail->image_path ?? 'images/default.png')) }}" alt="{{ $result['alternative_name'] }}"> --}}
                        </div>
                        <div>
                            <p class="text-lg sm:text-xl font-semibold text-gray-800">{{ $result['alternative_name'] }}</p>
                            <p class="text-xs sm:text-sm text-gray-600">
                                Skor Akhir (Q<sub>i</sub>): {{ number_format($result['final_qi'], 4) }}
                            </p>
                            {{-- Opsional: tampilkan skor Q1 dan Q2 jika ada di $result --}}
                            @if(isset($result['q1_value']) && isset($result['q2_value']))
                            <p class="text-xs text-gray-500">
                                (Q<sub>1</sub>: {{ number_format($result['q1_value'], 4) }},
                                Q<sub>2</sub>: {{ number_format($result['q2_value'], 4) }})
                            </p>
                            @endif
                        </div>
                    </div>
                    @if ($result['rank'] == 1)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs sm:text-sm font-medium bg-blue-100 text-blue-800">
                            Pilihan Terbaik!
                        </span>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif

    <div class="w-full text-center mt-10 space-x-4">
        {{-- Tombol untuk memulai sesi SPK baru --}}
        <a href="{{ route('spk.pick') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-200">
            Mulai Perbandingan Baru
        </a>
        <a href="{{ route('home') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition duration-200">
            Kembali ke Beranda
        </a>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('rankingPage', () => ({
        init() {
            console.log('Ranking page initialized.');
            // Data sudah di-pass dari PHP, tidak ada logika JS kompleks di sini.
        }
    }));
});
</script>
@endpush
@endsection