{{-- resources/views/partials/_spk_bottom_nav.blade.php --}}
{{-- Parameter $currentPageName dan $currentSpkSessionId (opsional) akan di-pass dari view utama --}}
@php
    // Ambil ID sesi SPK saat ini dari session PHP jika ada,
    // atau dari variabel $spkSession yang mungkin di-pass ke view utama.
    // Ini penting untuk link Calculation dan Rank.
    $activeSessionId = session('current_spk_session_id') ?? ($spkSession->id ?? null);
@endphp

<div class="w-full bg-white shadow-md flex items-center justify-around py-3 border-t-2 border-gray-200 fixed bottom-0 left-0 right-0 z-40">
    {{-- Tombol Overview --}}
    <a href="{{ route('spk.overview') }}" class="flex flex-col items-center text-sm
        {{ $currentPageName === 'overview' ? 'text-blue-600 font-semibold' : 'text-gray-500 hover:text-blue-600 transition' }}">
        <div class="h-1 w-full {{ $currentPageName === 'overview' ? 'bg-blue-600' : 'bg-gray-300' }} mb-1 rounded-full"></div>
        Overview
    </a>

    {{-- Tombol Penilaian --}}
    <a href="{{ route('spk.assessment.show') }}" class="flex flex-col items-center text-sm
        {{ $currentPageName === 'assessment' ? 'text-blue-600 font-semibold' : 'text-gray-500 hover:text-blue-600 transition' }}">
        <div class="h-1 w-full {{ $currentPageName === 'assessment' ? 'bg-blue-600' : 'bg-gray-300' }} mb-1 rounded-full"></div>
        Penilaian
    </a>

    {{-- Tombol Perhitungan --}}
    {{-- Link ini hanya aktif jika ada $activeSessionId (setelah perhitungan selesai) --}}
    <a href="{{ $activeSessionId ? route('spk.calculation.show', ['sessionId' => $activeSessionId]) : '#' }}"
       class="flex flex-col items-center text-sm
        {{ $currentPageName === 'calculation' ? 'text-blue-600 font-semibold' : 'text-gray-500' }}
        {{ $activeSessionId ? 'hover:text-blue-600 transition' : 'opacity-50 cursor-not-allowed' }}"
       @if(!$activeSessionId) onclick="event.preventDefault(); alert('Selesaikan penilaian dan perhitungan terlebih dahulu untuk melihat halaman ini.');" @endif>
        <div class="h-1 w-full {{ $currentPageName === 'calculation' ? 'bg-blue-600' : ($activeSessionId ? 'bg-gray-300' : 'bg-gray-200') }} mb-1 rounded-full"></div>
        Perhitungan
    </a>

    {{-- Tombol Ranking --}}
    {{-- Link ini hanya aktif jika ada $activeSessionId (setelah perhitungan selesai) --}}
    <a href="{{ $activeSessionId ? route('spk.rank.show', ['sessionId' => $activeSessionId]) : '#' }}"
       class="flex flex-col items-center text-sm
        {{ $currentPageName === 'rank' ? 'text-blue-600 font-semibold' : 'text-gray-500' }}
        {{ $activeSessionId ? 'hover:text-blue-600 transition' : 'opacity-50 cursor-not-allowed' }}"
       @if(!$activeSessionId) onclick="event.preventDefault(); alert('Selesaikan penilaian dan perhitungan terlebih dahulu untuk melihat halaman ini.');" @endif>
        <div class="h-1 w-full {{ $currentPageName === 'rank' ? 'bg-blue-600' : ($activeSessionId ? 'bg-gray-300' : 'bg-gray-200') }} mb-1 rounded-full"></div>
        Ranking
    </a>
</div>

{{-- Padding tambahan di body untuk menghindari konten tertutup oleh fixed bottom nav --}}
{{-- Anda mungkin perlu menambahkan ini di layout utama Anda (misal spk_flow.blade.php) di dalam tag body --}}
{{-- <div class="pb-20"></div> --}}