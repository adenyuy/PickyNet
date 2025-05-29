@extends('layouts.app') {{-- Menggunakan layout utama untuk header navigasi --}}

@section('content')
{{-- Membungkus seluruh konten yang berinteraksi dengan Alpine.js dalam satu x-data scope --}}
<div x-data="pickModal" x-cloak>
    {{-- Main content section --}}
    <div class="bg-blue-500 min-h-screen flex items-center justify-center py-5">
        <div class="bg-white rounded-xl shadow-2xl p-8 md:p-12 flex flex-col md:flex-row items-center max-w-4xl mx-auto">
            {{-- Illustration of a man with a lightbulb --}}
            <div class="md:w-1/2 flex justify-center mb-8 md:mb-0 md:pr-8">
                <img src="{{ asset('storage/images/deskripsi3.png') }}" alt="Ilustrasi Cari Solusi" class="w-full max-w-xs md:max-w-md h-auto">
            </div>

            {{-- Text content and buttons --}}
            <div class="md:w-1/2 text-center md:text-left">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4 leading-tight">
                    Bukan Hanya Pasangan yang Harus Langgeng, WiFi Juga!
                </h1>
                <p class="text-lg text-gray-600 mb-8">
                    Temukan provider terbaik versi Anda – pakai data, bukan perasaan.
                </p>

                <div class="flex flex-col sm:flex-row items-center md:justify-start justify-center space-y-4 sm:space-y-0 sm:space-x-4 mb-6">
                    {{-- "Mulai Sekarang" button - will open the modal --}}
                    <button type="button" @click="showModal = true" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-lg flex items-center space-x-2 transition duration-300 transform hover:scale-105">
                        <span>Mulai Sekarang</span>
                        <img src="{{ asset('storage/images/wifi.png') }}" alt="WiFi Icon" class="w-6 h-6">
                    </button>

                    {{-- Powered By WASPAS indicator --}}
                    <div class="flex items-center space-x-2 text-gray-500">
                        <img src="{{ asset('storage/images/info.png') }}" alt="Info Icon" class="w-6 h-6">
                        <span class="text-sm">Powered By <span class="font-semibold text-gray-700">WASPAS</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Section --}}
    <div x-show="showModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50">
        {{-- Add flex-col to make content stack vertically, and overflow-hidden for progress bar corners --}}
        <div @click.away="showModal = false" class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">
            {{-- Modal Header --}}
            <div class="relative p-6 border-b border-gray-200 flex-shrink-0"> {{-- flex-shrink-0 to prevent header from shrinking --}}
                <div class="flex items-center justify-start space-x-2 absolute top-6 left-6">
                    <span class="text-sm text-gray-500">Data bersifat editable</span>
                </div>
                <button @click="showModal = false" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                {{-- Page 1 Header: Choose Providers --}}
                <div x-show="currentPage === 1" class="text-center">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Pilih Provider untuk dibandingkan</h2>
                    <p class="text-red-500 text-sm">*minimal 2 provider</p>
                    <span class="absolute top-6 right-16 text-blue-600 font-semibold">1 of 2</span>
                </div>

                {{-- Page 2 Header: Rank Criteria --}}
                <div x-show="currentPage === 2" class="text-center">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Susun Kriteria Terbaik Versi Anda</h2>
                    <span class="absolute top-6 right-16 text-blue-600 font-semibold">2 of 2</span>
                </div>
            </div>

            {{-- Modal Body - flex-grow to take available space, overflow-y-auto for scrolling --}}
            <div class="p-6 flex-grow overflow-y-auto">
                {{-- Page 1 Content: Provider Selection (Alternatives) --}}
                <div x-show="currentPage === 1" class="grid grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($alternatives as $alternative)
                    <label :for="'provider_' + '{{ $alternative->alternative_id }}'"
                           class="relative flex flex-col items-center p-4 border border-gray-300 rounded-lg cursor-pointer transition duration-200"
                           :class="{
                               'border-blue-500 ring-2 ring-blue-200': isChecked('{{ $alternative->alternative_id }}'),
                               'hover:border-blue-500': !isChecked('{{ $alternative->alternative_id }}')
                           }">
                        <input type="checkbox"
                               :id="'provider_' + '{{ $alternative->alternative_id }}'"
                               value="{{ $alternative->alternative_id }}"
                               x-model="selectedAlternatives['{{ $alternative->alternative_id }}']"
                               class="hidden" {{-- Hide native checkbox --}}
                        >
                        {{-- Custom checkbox indicator --}}
                        <div :class="isChecked('{{ $alternative->alternative_id }}') ? 'bg-blue-600 border-blue-600' : 'bg-white border-gray-400'"
                             class="absolute top-3 left-3 h-4 w-4 rounded-full border-2 flex items-center justify-center transition-all duration-200">
                             <svg x-show="isChecked('{{ $alternative->alternative_id }}')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <img src="{{ asset('storage/' . $alternative->image_path) }}" alt="{{ $alternative->name }}" class="h-16 object-contain mb-2">
                        <span class="text-sm font-medium text-gray-700">{{ $alternative->name }}</span>
                    </label>
                    @endforeach
                </div>

                {{-- Page 2 Content: Criteria Ranking --}}
                <div x-show="currentPage === 2" class="space-y-4">
                    <template x-for="(criteria, index) in criterias" :key="criteria.kriteria_id">
                        <div class="flex items-center border border-gray-300 rounded-lg p-3 relative bg-gray-50">
                            <span x-text="criteria.name" class="flex-grow text-gray-800 font-medium"></span>
                            <div class="flex items-center space-x-1">
                                <button @click="moveCriteriaUp(criteria.kriteria_id)" type="button" class="p-1 rounded-full hover:bg-gray-200 text-blue-600 disabled:opacity-50" :disabled="index === 0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                </button>
                                <button @click="moveCriteriaDown(criteria.kriteria_id)" type="button" class="p-1 rounded-full hover:bg-gray-200 text-blue-600 disabled:opacity-50" :disabled="index === criterias.length - 1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                            </div>
                            <span class="ml-4 w-8 h-8 flex items-center justify-center border border-gray-300 rounded-md bg-white text-gray-700 font-semibold" x-text="index + 1"></span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="p-6 border-t border-gray-200 flex justify-between flex-shrink-0"> {{-- flex-shrink-0 for footer --}}
                <button x-show="currentPage === 2" @click="currentPage = 1" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg transition duration-200">Previous</button>
                <button x-show="currentPage === 1" @click="goToNextPage" :disabled="Object.keys(selectedAlternatives).filter(key => selectedAlternatives[key]).length < 2" class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-bold py-2 px-6 rounded-lg transition duration-200 ml-auto">Next</button>
                <button x-show="currentPage === 2" @click="submitCriteria" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 ml-auto">GO!</button>
            </div>

            {{-- Progress Bar at the bottom of the modal --}}
            <div class="relative h-2 bg-gray-200 w-full"> {{-- w-full ensures it spans the full width --}}
                <div class="absolute inset-y-0 left-0 bg-blue-600 transition-all duration-300"
                     :style="`width: ${currentPage === 1 ? '50%' : '100%'};`"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('pickModal', () => ({
        showModal: false,
        currentPage: 1,
        selectedAlternatives: {}, // { alternative_id: true/false }
        criterias: @json($criterias), // Initial order of criterias from DB

        // CSRF token for Laravel (requires <meta name="csrf-token" content="{{ csrf_token() }}"> in your layout)
        csrfToken: document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '',

        init() { // Lifecycle hook for initialization
            this.$watch('showModal', value => {
                if (value) document.body.classList.add('overflow-hidden');
                else document.body.classList.remove('overflow-hidden');
            });
        },

        // Custom checkbox state (for visual only, x-model handles actual checked state)
        isChecked(alternativeId) {
            return this.selectedAlternatives[alternativeId] || false;
        },

        // Function to move a criteria up in the list
        moveCriteriaUp(id) {
            const index = this.criterias.findIndex(c => c.kriteria_id === id);
            if (index > 0) {
                // Use slice or spread operator to create a new array for reactivity
                const newCriterias = [...this.criterias];
                const [movedCriteria] = newCriterias.splice(index, 1);
                newCriterias.splice(index - 1, 0, movedCriteria);
                this.criterias = newCriterias; // Update the reactive property
            }
        },

        // Function to move a criteria down in the list
        moveCriteriaDown(id) {
            const index = this.criterias.findIndex(c => c.kriteria_id === id);
            if (index < this.criterias.length - 1) {
                // Use slice or spread operator to create a new array for reactivity
                const newCriterias = [...this.criterias];
                const [movedCriteria] = newCriterias.splice(index, 1);
                newCriterias.splice(index + 1, 0, movedCriteria);
                this.criterias = newCriterias; // Update the reactive property
            }
        },

        goToNextPage() {
            if (Object.keys(this.selectedAlternatives).filter(key => this.selectedAlternatives[key]).length >= 2) {
                this.currentPage = 2;
            } else {
                alert('Pilih minimal 2 provider untuk dibandingkan.');
            }
        },

        async submitCriteria() {
            // Check if CSRF token is available
            if (!this.csrfToken) {
                console.error('CSRF token not found. Please ensure <meta name="csrf-token"> is present in your HTML.');
                alert('Terjadi kesalahan: CSRF token tidak ditemukan.');
                return;
            }

            const dataToSubmit = {
                selected_alternatives: Object.keys(this.selectedAlternatives).filter(key => this.selectedAlternatives[key]),
                ranked_criterias: this.criterias.map(c => c.kriteria_id), // Sending criteria IDs in the new order
            };

            console.log('Data yang akan dikirim:', dataToSubmit);

            try {
                // Sending data to the backend using Fetch API
                const response = await fetch('/spk/process-data', { // Adjust to your actual POST route
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json', // Optional, to expect JSON response
                        'X-CSRF-TOKEN': this.csrfToken // Ensure CSRF token is included
                    },
                    body: JSON.stringify(dataToSubmit)
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Gagal mengirim data ke server.');
                }

                const result = await response.json();
                console.log('Success:', result);

                // On success, redirect to the overview page
                window.location.href = '/spk/overview'; // Adjust to your actual overview route

            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memproses data: ' + error.message);
                // Optionally close modal on error, or provide specific error feedback
                this.showModal = false; 
            }
        }
    }));
});
</script>
@endsection