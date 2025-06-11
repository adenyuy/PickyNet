@extends('layouts.app') {{-- Menggunakan layout utama Anda --}}

@section('content')
<div x-data="pickModal" x-cloak>
    {{-- Main content section - Konten utama halaman pick Anda (ilustrasi, teks, tombol "Mulai Sekarang") --}}
    {{-- Saya asumsikan ini sudah ada dan benar sesuai kode lama Anda --}}
    <div class="bg-blue-500 min-h-screen flex items-center justify-center py-5">
        <div class="bg-white rounded-xl shadow-2xl p-8 md:p-12 flex flex-col md:flex-row items-center max-w-4xl mx-auto">
            {{-- Ilustrasi --}}
            <div class="md:w-1/2 flex justify-center mb-8 md:mb-0 md:pr-8">
                <img src="{{ asset('storage/images/deskripsi3.png') }}" alt="Ilustrasi Cari Solusi" class="w-full max-w-xs md:max-w-md h-auto">
            </div>
            {{-- Konten teks dan tombol --}}
            <div class="md:w-1/2 text-center md:text-left">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4 leading-tight">
                    Bukan Hanya Pasangan yang Harus Langgeng, WiFi Juga!
                </h1>
                <p class="text-lg text-gray-600 mb-8">
                    Temukan provider terbaik versi Anda – pakai data, bukan perasaan.
                </p>
                <div class="flex flex-col sm:flex-row items-center md:justify-start justify-center space-y-4 sm:space-y-0 sm:space-x-4 mb-6">
                    <button type="button" @click="showModal = true" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-lg flex items-center space-x-2 transition duration-300 transform hover:scale-105">
                        <span>Mulai Sekarang</span>
                        <img src="{{ asset('storage/images/wifi.png') }}" alt="WiFi Icon" class="w-6 h-6">
                    </button>
                    <div class="flex items-center space-x-2 text-gray-500">
                        <img src="{{ asset('storage/images/info.png') }}" alt="Info Icon" class="w-6 h-6">
                        <span class="text-sm">Powered By <span class="font-semibold text-gray-700">WASPAS</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Section --}}
    {{-- HTML Modal Anda (bagian <div x-show="showModal" ...> hingga penutupnya) --}}
    {{-- Sebagian besar HTML Modal Anda sudah baik. Perubahan utama ada di skrip. --}}
    <div x-show="showModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50">
        <div @click.away="closeModalAndReset" class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">
            {{-- Modal Header (dengan title dinamis dan tombol close) --}}
            <div class="relative p-6 border-b border-gray-200 flex-shrink-0">
                <div class="flex items-center justify-start space-x-2 absolute top-6 left-6">
                    <span class="text-sm text-gray-500">Data bersifat editable</span>
                </div>
                <button @click="closeModalAndReset" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                {{-- Header Halaman 1 --}}
                <div x-show="currentPage === 1" class="text-center">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Pilih Provider untuk dibandingkan</h2>
                    <p class="text-red-500 text-sm">*minimal 2 provider</p>
                    <span class="absolute top-6 right-16 text-blue-600 font-semibold">1 of 2</span>
                </div>
                {{-- Header Halaman 2 --}}
                <div x-show="currentPage === 2" class="text-center">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Susun Kriteria Terbaik Versi Anda</h2>
                    <span class="absolute top-6 right-16 text-blue-600 font-semibold">2 of 2</span>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 flex-grow overflow-y-auto">
                {{-- Page 1: Provider Selection --}}
                <div x-show="currentPage === 1" class="grid grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($alternatives as $alternative)
                    <label :for="'provider_' + '{{ $alternative->alternative_id }}'"
                           class="relative flex flex-col items-center p-4 border border-gray-300 rounded-lg cursor-pointer transition duration-200"
                           :class="{
                                'border-blue-500 ring-2 ring-blue-200': selectedAlternatives['{{ $alternative->alternative_id }}'],
                                'hover:border-blue-500': !selectedAlternatives['{{ $alternative->alternative_id }}']
                           }">
                        <input type="checkbox"
                               :id="'provider_' + '{{ $alternative->alternative_id }}'"
                               value="{{ $alternative->alternative_id }}"
                               x-model="selectedAlternatives['{{ $alternative->alternative_id }}']"
                               class="hidden">
                        <div :class="selectedAlternatives['{{ $alternative->alternative_id }}'] ? 'bg-blue-600 border-blue-600' : 'bg-white border-gray-400'"
                             class="absolute top-3 left-3 h-4 w-4 rounded-full border-2 flex items-center justify-center transition-all duration-200">
                            <svg x-show="selectedAlternatives['{{ $alternative->alternative_id }}']" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <img src="{{ asset('storage/' . $alternative->image_path) }}" alt="{{ $alternative->name }}" class="h-16 object-contain mb-2">
                        <span class="text-sm font-medium text-gray-700">{{ $alternative->name }}</span>
                    </label>
                    @endforeach
                </div>

                {{-- Page 2: Criteria Ranking --}}
                <div x-show="currentPage === 2" class="space-y-4">
                    <template x-for="(criteria, index) in rankedCriterias" :key="criteria.kriteria_id">
                        <div class="flex items-center border border-gray-300 rounded-lg p-3 relative bg-gray-50">
                            <span x-text="criteria.name" class="flex-grow text-gray-800 font-medium"></span>
                            <div class="flex items-center space-x-1">
                                <button @click="moveCriteriaUp(criteria.kriteria_id)" type="button" class="p-1 rounded-full hover:bg-gray-200 text-blue-600 disabled:opacity-50" :disabled="index === 0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                </button>
                                <button @click="moveCriteriaDown(criteria.kriteria_id)" type="button" class="p-1 rounded-full hover:bg-gray-200 text-blue-600 disabled:opacity-50" :disabled="index === rankedCriterias.length - 1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                            </div>
                            <span class="ml-4 w-8 h-8 flex items-center justify-center border border-gray-300 rounded-md bg-white text-gray-700 font-semibold" x-text="index + 1"></span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="p-6 border-t border-gray-200 flex justify-between flex-shrink-0">
                <button x-show="currentPage === 2" @click="currentPage = 1" type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg transition duration-200">Previous</button>
                {{-- Tombol Next hanya muncul di halaman 1 --}}
                <button x-show="currentPage === 1" @click="goToNextPage" :disabled="Object.values(selectedAlternatives).filter(Boolean).length < 2" type="button" class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-bold py-2 px-6 rounded-lg transition duration-200 ml-auto">Next</button>
                {{-- Tombol GO! hanya muncul di halaman 2 --}}
                <button x-show="currentPage === 2" @click="submitSpkSession" type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 ml-auto">GO!</button>
            </div>

            {{-- Progress Bar --}}
            <div class="relative h-2 bg-gray-200 w-full">
                <div class="absolute inset-y-0 left-0 bg-blue-600 transition-all duration-300"
                     :style="`width: ${currentPage === 1 ? '50%' : '100%'};`"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('pickModal', () => ({
        showModal: !! @json(session('showModalAfterEdit')), // Menggunakan session untuk menentukan apakah modal harus ditampilkan
        currentPage: 1,
        selectedAlternatives: {}, // { alternative_id: true/false }
        // Ambil data kriteria awal dari Blade (sudah diurutkan sesuai keinginan Anda dari controller)
        initialCriterias: @json($criterias->map(fn($c) => ['kriteria_id' => $c->kriteria_id, 'name' => $c->name])),
        rankedCriterias: [], // Ini akan diisi dengan urutan yang bisa diubah pengguna

        // CSRF token
        csrfToken: document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '',

        init() {
            // Saat komponen diinisialisasi, salin initialCriterias ke rankedCriterias
            this.rankedCriterias = JSON.parse(JSON.stringify(this.initialCriterias));

            this.$watch('showModal', value => {
                if (value) {
                    document.body.classList.add('overflow-hidden');
                    // Reset state modal setiap kali dibuka
                    this.currentPage = 1;
                    this.selectedAlternatives = {};
                    this.rankedCriterias = JSON.parse(JSON.stringify(this.initialCriterias)); // Reset urutan kriteria
                } else {
                    document.body.classList.remove('overflow-hidden');
                }
            });
        },

        closeModalAndReset() {
            this.showModal = false;
            // State akan direset oleh watcher 'showModal' atau di init() saat modal dibuka lagi
        },

        isChecked(alternativeId) {
            return this.selectedAlternatives[alternativeId] || false;
        },

        moveCriteriaUp(id) {
            const index = this.rankedCriterias.findIndex(c => c.kriteria_id === id);
            if (index > 0) {
                const newCriterias = [...this.rankedCriterias];
                const [movedCriteria] = newCriterias.splice(index, 1);
                newCriterias.splice(index - 1, 0, movedCriteria);
                this.rankedCriterias = newCriterias;
            }
        },

        moveCriteriaDown(id) {
            const index = this.rankedCriterias.findIndex(c => c.kriteria_id === id);
            if (index < this.rankedCriterias.length - 1) {
                const newCriterias = [...this.rankedCriterias];
                const [movedCriteria] = newCriterias.splice(index, 1);
                newCriterias.splice(index + 1, 0, movedCriteria);
                this.rankedCriterias = newCriterias;
            }
        },

        goToNextPage() {
            if (Object.values(this.selectedAlternatives).filter(Boolean).length >= 2) {
                this.currentPage = 2;
            } else {
                swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih minimal 2 provider untuk melanjutkan.',
                    confirmButtonText: 'OK'
                });
            }
        },

        // Mengganti nama submitCriteria menjadi submitSpkSession
        async submitSpkSession() {
            if (!this.csrfToken) {
                console.error('CSRF token tidak ditemukan.');
                swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'CSRF token tidak ditemukan. Silakan muat ulang halaman.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const finalSelectedAlternatives = Object.keys(this.selectedAlternatives).filter(key => this.selectedAlternatives[key]);
            const finalRankedCriteriaIds = this.rankedCriterias.map(c => c.kriteria_id);

            if (finalSelectedAlternatives.length < 2) {
                 swal.fire({
                 icon: 'warning',
                 title: 'Peringatan',
                 text: 'Pilih minimal 2 provider untuk melanjutkan.',
                 confirmButtonText: 'OK'
                 });
                 this.currentPage = 1; // Kembali ke halaman pemilihan alternatif
                 return;
            }
            if (finalRankedCriteriaIds.length === 0) { // Atau validasi lain untuk kriteria
                swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan urutkan kriteria sebelum melanjutkan.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const dataToSubmit = {
                selected_alternatives: finalSelectedAlternatives,
                ranked_criterias: finalRankedCriteriaIds,
            };

            console.log('Data yang akan dikirim ke startNewSpkSession:', dataToSubmit);

            try {
                const response = await fetch('{{ route('spk.session.start') }}', { // <-- PERUBAHAN RUTE
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify(dataToSubmit)
                });

                const result = await response.json(); // Selalu coba parse JSON dulu

                if (!response.ok) {
                    // Tangani error validasi dari server (422) atau error lainnya
                    if (response.status === 422 && result.errors) {
                        let errorMessages = "Validasi gagal:\n";
                        for (const field in result.errors) {
                            errorMessages += `- ${result.errors[field].join(', ')}\n`;
                        }
                        swal.fire({
                            icon: 'error',
                            text: errorMessages,
                            confirmButtonText: 'OK'
                        });
                    } else {
                        throw new Error(result.message || result.error || 'Gagal mengirim data ke server.');
                    }
                    return; // Hentikan eksekusi jika ada error
                }

                console.log('Sukses dari startNewSpkSession:', result);

                if (result.redirect_url) {
                    window.location.href = result.redirect_url; // <-- GUNAKAN URL REDIRECT DARI SERVER
                } else {
                    // Fallback jika redirect_url tidak ada, meskipun seharusnya ada
                    alert(result.message || 'Proses berhasil, namun tidak ada arahan halaman.');
                    this.closeModalAndReset();
                }

            } catch (error) {
                console.error('Error saat submitSpkSession:', error);
                swal.fire({
                    icon: 'error',
                    text: 'Terjadi kesalahan saat memproses data: ' + error.message,
                    confirmButtonText: 'OK'
                });
                // this.showModal = false; // Opsional: tutup modal jika error parah
            }
        }
    }));
});
</script>
@endsection