@extends('layouts.spk_flow')

@section('title', 'Perhitungan WASPAS')

@section('progress_width', '75%')

@section('back_link_route', route('spk.assessment'))

@section('page_header_title', 'Perhitungan')

@section('content')
<div x-data="calculationPage()" x-cloak
    class="w-full max-w-4xl mx-auto
           flex flex-col py-6 px-4 bg-white rounded-lg shadow-lg
           max-h-[calc(100vh-220px)] overflow-y-auto"> 
    
    {{-- Tombol Mulai Hitung --}}
    <div class="mb-8 w-full text-center flex-shrink-0"> 
        <button @click="startCalculation()"
                x-show="!calculationStarted"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition duration-200">
            Mulai Perhitungan WASPAS
        </button>
    </div>

    {{-- Loading Indicator --}}
    <div x-show="loading" class="text-gray-600 text-center w-full flex-shrink-0">Loading data...</div> 

    <div x-show="calculationStarted && !loading" class="w-full space-y-8 flex-grow"> 
        {{-- Konten tabel dan daftar Anda akan berada di sini --}}
        
        {{-- 1. The Normalized Decision Matrix ($R^*$) --}}
        <div class="bg-white rounded-lg shadow-lg p-6"> 
            <h3 class="text-xl font-semibold text-gray-800 mb-4">1. The Normalized Decision Matrix ($R^*$)</h3>
            <div class="overflow-x-auto"> 
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alternatif</th>
                            <template x-if="calculationData.criteria_details && Object.keys(calculationData.criteria_details).length > 0">
                                <template x-for="criteriaId in Object.keys(calculationData.criteria_details)" :key="criteriaId">
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" x-text="calculationData.criteria_details[criteriaId].name"></th>
                                </template>
                            </template>
                             <template x-else>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loading Kriteria...</th>
                            </template>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-if="calculationData.alternative_details && Object.keys(calculationData.alternative_details).length > 0">
                            <template x-for="alternativeId in Object.keys(calculationData.alternative_details)" :key="alternativeId">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="calculationData.alternative_details[alternativeId].name"></td>
                                    <template x-if="calculationData.criteria_details && Object.keys(calculationData.criteria_details).length > 0">
                                        <template x-for="criteriaId in Object.keys(calculationData.criteria_details)" :key="criteriaId">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="calculationData.normalized_matrix[alternativeId][criteriaId]"></td>
                                        </template>
                                    </template>
                                </tr>
                            </template>
                        </template>
                        <template x-else>
                            <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Loading Alternatif...</td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Bobot Kriteria ($w_j$) --}}
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
                        <template x-if="calculationData.criteria_details && Object.keys(calculationData.criteria_details).length > 0">
                            <template x-for="criteriaId in Object.keys(calculationData.criteria_details)" :key="criteriaId">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="calculationData.criteria_details[criteriaId].name"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="calculationData.weights[criteriaId]"></td>
                                </tr>
                            </template>
                        </template>
                        <template x-else>
                            <tr><td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">Loading Bobot Kriteria...</td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 2. The Additive Relative Importance ($Q^{(1)}$) --}}
        <div class="bg-white rounded-lg shadow-lg p-6"> 
            <h3 class="text-xl font-semibold text-gray-800 mb-4">2. The Additive Relative Importance ($Q^{(1)}$)</h3>
            <ul class="divide-y divide-gray-200">
                <template x-if="calculationData.alternative_details && Object.keys(calculationData.alternative_details).length > 0">
                    <template x-for="alternativeId in Object.keys(calculationData.alternative_details)" :key="alternativeId">
                        <li class="py-3 flex justify-between items-center text-sm">
                            <span class="font-medium text-gray-900" x-text="calculationData.alternative_details[alternativeId].name"></span>
                            <span class="text-gray-600" x-text="calculationData.additive_importance[alternativeId]"></span>
                        </li>
                    </template>
                </template>
                <template x-else>
                    <li class="py-3 text-center text-sm text-gray-500">Loading Additive Importance...</li>
                </template>
            </ul>
        </div>

        {{-- 3. The Multiplicative Relative Importance ($Q^{(2)}$) --}}
        <div class="bg-white rounded-lg shadow-lg p-6"> 
            <h3 class="text-xl font-semibold text-gray-800 mb-4">3. The Multiplicative Relative Importance ($Q^{(2)}$)</h3>
            <ul class="divide-y divide-gray-200">
                <template x-if="calculationData.alternative_details && Object.keys(calculationData.alternative_details).length > 0">
                    <template x-for="alternativeId in Object.keys(calculationData.alternative_details)" :key="alternativeId">
                        <li class="py-3 flex justify-between items-center text-sm">
                            <span class="font-medium text-gray-900" x-text="calculationData.alternative_details[alternativeId].name"></span>
                            <span class="text-gray-600" x-text="calculationData.multiplicative_importance[alternativeId]"></span>
                        </li>
                    </template>
                </template>
                 <template x-else>
                    <li class="py-3 text-center text-sm text-gray-500">Loading Multiplicative Importance...</li>
                </template>
            </ul>
        </div>

        {{-- 4. The Joint Generalized Criterion ($Q$) --}}
        <div class="bg-white rounded-lg shadow-lg p-6"> 
            <h3 class="text-xl font-semibold text-gray-800 mb-4">4. The Joint Generalized Criterion ($Q$)</h3>
            <ul class="divide-y divide-gray-200">
                <template x-if="calculationData.alternative_details && Object.keys(calculationData.alternative_details).length > 0">
                    <template x-for="alternativeId in Object.keys(calculationData.alternative_details)" :key="alternativeId">
                        <li class="py-3 flex justify-between items-center text-sm font-semibold">
                            <span class="text-gray-900" x-text="calculationData.alternative_details[alternativeId].name"></span>
                            <span class="text-blue-600 text-lg" x-text="calculationData.joint_criterion[alternativeId]"></span>
                        </li>
                    </template>
                </template>
                <template x-else>
                    <li class="py-3 text-center text-sm text-gray-500">Loading Joint Criterion...</li>
                </template>
            </ul>
        </div>

        {{-- Tombol Lanjut ke Ranking --}}
        <div class="w-full text-center mt-8"> 
            <a href="{{ route('spk.rank') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition duration-200">
                Lanjut ke Ranking
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('calculationPage', () => ({
            calculationStarted: false,
            loading: false, 
            calculationData: {},

            init() {
                console.log('Alpine component initialized. Ready to fetch data.');
            },

            async startCalculation() {
                if (this.loading) return; 
                this.loading = true; 

                console.log('Start button clicked. Fetching calculationData via AJAX...');

                try {
                    const response = await fetch('{{ route('spk.get-calculation-data') }}');

                    if (!response.ok) {
                        const errorBody = await response.text();
                        console.error('Network response was not ok:', response.status, response.statusText, errorBody);
                        throw new Error(`Failed to fetch calculation data. Server responded with status ${response.status}.`);
                    }

                    const data = await response.json(); 
                    this.calculationData = data; 

                    console.log('Calculation Data fetched via AJAX:', this.calculationData);

                    if (!this.calculationData || Object.keys(this.calculationData).length === 0 || 
                        !this.calculationData.alternative_details || Object.keys(this.calculationData.alternative_details).length === 0 ||
                        !this.calculationData.criteria_details || Object.keys(this.calculationData.criteria_details).length === 0) {
                        console.error('Validation failed: calculationData is empty or incomplete after AJAX!');
                        alert('Data perhitungan kosong atau tidak lengkap. Silakan kembali ke tahap sebelumnya.');
                        return;
                    }

                    this.calculationStarted = true;
                    console.log('Validation passed. calculationStarted set to true.');

                } catch (error) {
                    console.error('Error fetching calculation data:', error);
                    alert('Terjadi kesalahan saat mengambil data perhitungan: ' + error.message + '. Silakan kembali ke tahap sebelumnya.');
                } finally {
                    this.loading = false;
                }
            },
        }));
    });
</script>
@endpush
@endsection