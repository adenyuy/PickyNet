@extends('layouts.spk_flow')

@section('title', 'Ranking Alternatif')

@section('progress_width', '100%') {{-- Ini adalah tahap terakhir, jadi 100% --}}

@section('back_link_route', route('spk.calculation')) {{-- Kembali ke halaman perhitungan --}}

@section('page_header_title', 'Ranking')

@section('content')
<div x-data="rankingPage()" x-cloak
    class="w-full max-w-xl mx-auto flex flex-col py-6 px-4 bg-white rounded-lg shadow-lg">

    <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Hasil Rekomendasi Terbaik</h2>

    @if (empty($rankingResults))
        <p class="text-center text-gray-600">Tidak ada hasil ranking yang tersedia.</p>
    @else
        <ul class="divide-y divide-gray-200 space-y-4">
            @foreach($rankingResults as $index => $result)
                <li class="py-4 px-6 flex items-center justify-between bg-white rounded-lg shadow-sm {{ $index == 0 ? 'border-4 border-blue-500 transform scale-105' : 'border border-gray-200' }} transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <span class="text-2xl font-bold {{ $index == 0 ? 'text-blue-600' : 'text-gray-500' }}">#{{ $index + 1 }}</span>
                        <div>
                            <p class="text-xl font-semibold text-gray-800">{{ $result['alternative_name'] }}</p>
                            <p class="text-sm text-gray-600">Score: {{ number_format($result['final_qi'], 4) }}</p>
                        </div>
                    </div>
                    @if ($index == 0)
                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            Terbaik!
                        </span>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif

    <div class="w-full text-center mt-8">
        <a href="{{ route('home') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition duration-200">
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
                // Tidak ada AJAX atau logika kompleks di sini, data sudah di-pass dari PHP
            }
        }));
    });
</script>
@endpush
@endsection