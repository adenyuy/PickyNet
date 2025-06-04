@extends('layouts.spk_flow')

@section('title', 'Detail Perhitungan WASPAS')
@section('progress_width', '75%')
{{-- Tombol back sekarang bisa mengarah ke halaman assessment dari sesi ini jika ada,
    atau ke halaman overview jika ingin meninjau ulang pilihan awal.
    Namun, karena sesi SPK sudah selesai dihitung, mungkin lebih baik kembali ke daftar sesi SPK (jika ada)
    atau ke halaman pick untuk memulai yang baru. Untuk saat ini, kita bisa arahkan ke overview sesi ini.
    Perlu $spkSession->id di sini.
--}}
@if(isset($spkSession))
    @section('back_link_route', route('spk.overview', ['sessionId' => $spkSession->id]))
    {{-- Atau jika overview tidak pakai sessionId dan bergantung PHP session (yang sudah di-clear): --}}
    {{-- @section('back_link_route', route('spk.pick')) --}}
@else
    @section('back_link_route', route('spk.pick')) {{-- Fallback jika $spkSession tidak ada --}}
@endif

@section('page_header_title', 'Detail Perhitungan')

@section('content')
{{-- $calculationResults dan $spkSession (opsional) akan di-pass dari SpkController@showCalculationPage --}}
<div x-data="calculationPage(JSON.parse(decodeURIComponent('{{ rawurlencode(json_encode($calculationResults)) }}')))" x-cloak
     class="w-full max-w-4xl mx-auto
            flex flex-col py-6 px-4 bg-white rounded-lg shadow-lg
            max-h-[calc(100vh-200px)] overflow-y-auto">

    {{-- Tombol "Mulai Perhitungan" DIHAPUS karena hasil langsung ditampilkan --}}

    {{-- Indikator loading bisa dihapus atau diubah untuk menampilkan jika data kosong --}}
    <template x-if="!calculationData || Object.keys(calculationData).length === 0">
        <div class="text-gray-600 text-center w-full flex-shrink-0 p-10">
            Data perhitungan tidak tersedia atau sesi tidak valid.
            <a href="{{ route('spk.pick') }}" class="text-blue-600 hover:underline">Mulai sesi baru?</a>
        </div>
    </template>

    {{-- Bagian utama yang menampilkan hasil perhitungan --}}
    <div x-show="calculationData && Object.keys(calculationData).length > 0" class="w-full space-y-8 mb-12">

        {{-- 0. Decision Matrix (Xij) - SKOR MENTAH (Opsional untuk ditampilkan) --}}
        <template x-if="calculationData.decision_matrix && Object.keys(calculationData.decision_matrix).length > 0">
            <div class="bg-gray-50 rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">0. Matriks Keputusan (Skor Awal Pengguna)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alternatif</th>
                                <template x-for="criteriaId in Object.keys(calculationData.criteria_details)" :key="criteriaId">
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" x-text="calculationData.criteria_details[criteriaId].name"></th>
                                </template>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="alternativeId in Object.keys(calculationData.alternative_details)" :key="alternativeId">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="calculationData.alternative_details[alternativeId].name"></td>
                                    <template x-for="criteriaId in Object.keys(calculationData.criteria_details)" :key="criteriaId">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="calculationData.decision_matrix[alternativeId]?.[criteriaId] || 'N/A'"></td>
                                    </template>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        {{-- 1. The Normalized Decision Matrix ($R^*$) --}}
        {{-- HTML untuk tabel ini sama seperti yang sudah Anda punya, menggunakan calculationData.normalized_matrix --}}
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">1. Matriks Keputusan Ternormalisasi ($R_{ij}$)</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alternatif</th>
                            <template x-for="criteriaId in Object.keys(calculationData.criteria_details)" :key="criteriaId">
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" x-text="calculationData.criteria_details[criteriaId].name"></th>
                            </template>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="alternativeId in Object.keys(calculationData.alternative_details)" :key="alternativeId">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="calculationData.alternative_details[alternativeId].name"></td>
                                <template x-for="criteriaId in Object.keys(calculationData.criteria_details)" :key="criteriaId">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="calculationData.normalized_matrix[alternativeId]?.[criteriaId] || 'N/A'"></td>
                                </template>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Bobot Kriteria ($w_j$) --}}
        {{-- HTML untuk tabel ini sama seperti yang sudah Anda punya, menggunakan calculationData.weights --}}
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Bobot Kriteria ($w_j$)</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kriteria</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bobot ($w_j$)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="criteriaId in Object.keys(calculationData.criteria_details)" :key="criteriaId">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="calculationData.criteria_details[criteriaId].name"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="calculationData.weights[criteriaId] || 'N/A'"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 2. The Additive Relative Importance ($Q^{(1)}$) --}}
        {{-- HTML untuk daftar ini sama seperti yang sudah Anda punya, menggunakan calculationData.additive_importance --}}
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">2. Weighted Sum Model ($Q_i^{(1)}$)</h3>
            <ul class="divide-y divide-gray-200">
                <template x-for="alternativeId in Object.keys(calculationData.alternative_details)" :key="alternativeId">
                    <li class="py-3 flex justify-between items-center text-sm">
                        <span class="font-medium text-gray-900" x-text="calculationData.alternative_details[alternativeId].name"></span>
                        <span class="text-gray-600" x-text="calculationData.additive_importance[alternativeId] || 'N/A'"></span>
                    </li>
                </template>
            </ul>
        </div>

        {{-- 3. The Multiplicative Relative Importance ($Q^{(2)}$) --}}
        {{-- HTML untuk daftar ini sama seperti yang sudah Anda punya, menggunakan calculationData.multiplicative_importance --}}
         <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">3. Weighted Product Model ($Q_i^{(2)}$)</h3>
            <ul class="divide-y divide-gray-200">
                <template x-for="alternativeId in Object.keys(calculationData.alternative_details)" :key="alternativeId">
                    <li class="py-3 flex justify-between items-center text-sm">
                        <span class="font-medium text-gray-900" x-text="calculationData.alternative_details[alternativeId].name"></span>
                        <span class="text-gray-600" x-text="calculationData.multiplicative_importance[alternativeId] || 'N/A'"></span>
                    </li>
                </template>
            </ul>
        </div>

        {{-- 4. The Joint Generalized Criterion ($Q$) --}}
        {{-- HTML untuk daftar ini sama seperti yang sudah Anda punya, menggunakan calculationData.joint_criterion --}}
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">4. Nilai Akhir Preferensi ($Q_i$)</h3>
            <ul class="divide-y divide-gray-200">
                <template x-for="alternativeId in Object.keys(calculationData.alternative_details)" :key="alternativeId">
                    <li class="py-3 flex justify-between items-center text-sm font-semibold">
                        <span class="text-gray-900" x-text="calculationData.alternative_details[alternativeId].name"></span>
                        <span class="text-blue-600 text-lg" x-text="calculationData.joint_criterion[alternativeId] || 'N/A'"></span>
                    </li>
                </template>
            </ul>
        </div>

        {{-- Tombol Lanjut ke Ranking --}}
        <div class="w-full text-center mt-8 mb-4">
            {{-- Pastikan $spkSession->id tersedia jika rute ini membutuhkannya --}}
            @if(isset($spkSession))
            <a href="{{ route('spk.rank.show', ['sessionId' => $spkSession->id]) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition duration-200">
                Lihat Halaman Ranking
            </a>
            @else
             <a href="#" class="bg-gray-400 text-white font-bold py-3 px-8 rounded-lg shadow-md cursor-not-allowed">
                Ranking Tidak Tersedia
            </a>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    // Menerima initialData (yaitu $calculationResults dari PHP)
    Alpine.data('calculationPage', (initialData) => ({
        calculationStarted: false, // Akan di-set true jika data ada
        loading: true, // Awalnya true, lalu false setelah data dicek
        calculationData: initialData || {}, // Langsung inisialisasi dengan data dari PHP

        init() {
            console.log('Calculation Page Alpine Initialized with data:', this.calculationData);
            if (this.calculationData && Object.keys(this.calculationData).length > 0 && this.calculationData.alternative_details) {
                this.calculationStarted = true;
            } else {
                console.warn('Calculation data is empty or not properly structured.');
                // Biarkan template x-if di atas yang menangani tampilan pesan error
            }
            this.loading = false; // Selesai loading awal
        },

        // Metode startCalculation() yang lama sudah tidak diperlukan lagi
        // karena data sudah di-pass langsung.
    }));
});
</script>
@endpush
@endsection