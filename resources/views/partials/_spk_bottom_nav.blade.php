{{-- Parameter $currentPageName akan digunakan untuk menandai halaman aktif --}}
<div class="w-full bg-white shadow-md flex items-center justify-around py-3 border-t-2 border-gray-200">
    <a href="{{ route('spk.overview') }}" class="flex flex-col items-center text-sm
        {{ $currentPageName === 'overview' ? 'text-blue-600 font-semibold' : 'text-gray-500 hover:text-blue-600 transition' }}">
        <div class="h-2 w-full {{ $currentPageName === 'overview' ? 'bg-blue-600' : 'bg-gray-300' }} mb-1"></div>
        Overview
    </a>
    <a href="{{ route('spk.assessment') }}" class="flex flex-col items-center text-sm
        {{ $currentPageName === 'assessment' ? 'text-blue-600 font-semibold' : 'text-gray-500 hover:text-blue-600 transition' }}">
        <div class="h-2 w-full {{ $currentPageName === 'assessment' ? 'bg-blue-600' : 'bg-gray-300' }} mb-1"></div>
        Penilaian
    </a>
    <a href="{{ route('spk.calculation') }}" class="flex flex-col items-center text-sm
        {{ $currentPageName === 'calculation' ? 'text-blue-600 font-semibold' : 'text-gray-500 hover:text-blue-600 transition' }}">
        <div class="h-2 w-full {{ $currentPageName === 'calculation' ? 'bg-blue-600' : 'bg-gray-300' }} mb-1"></div>
        Perhitungan
    </a>
    <a href="{{ route('spk.rank') }}" class="flex flex-col items-center text-sm
        {{ $currentPageName === 'rank' ? 'text-blue-600 font-semibold' : 'text-gray-500 hover:text-blue-600 transition' }}">
        <div class="h-2 w-full {{ $currentPageName === 'rank' ? 'bg-blue-600' : 'bg-gray-300' }} mb-1"></div>
        Ranking
    </a>
</div>