@extends('layouts.spk_flow')

@section('title', 'Penilaian')

@section('progress_width', '50%')

@section('back_link_route', route('spk.overview'))

@section('page_header_title', 'Penilaian')

@section('content')
<div x-data="assessmentPage" class="w-full h-full flex flex-col items-center justify-start py-6">

    {{-- Daftar Alternatif --}}
    <div class="space-y-4 w-full max-w-2xl mx-auto">
        @forelse($selectedAlternatives as $alternative)
            <div @click="openAssessmentModal('{{ $alternative->alternative_id }}', '{{ $alternative->name }}')"
                 class="bg-white rounded-lg shadow-md p-4 flex items-center justify-between cursor-pointer hover:shadow-lg transition duration-200">
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('storage/' . $alternative->image_path) }}" alt="{{ $alternative->name }}" class="h-12 w-12 object-contain">
                    <span class="text-xl font-semibold text-gray-800">{{ $alternative->name }}</span>
                </div>
                <template x-if="allCriteriaAssessed('{{ $alternative->alternative_id }}')">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </template>
                <template x-else>
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </template>
            </div>
        @empty
            <p class="text-center text-gray-600">Tidak ada alternatif yang dipilih untuk penilaian.</p>
        @endforelse
    </div>

    {{-- Modal Penilaian (menggunakan Alpine.js) --}}
    <div x-show="showAssessmentModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50" x-cloak>
        <div @click.away="closeAssessmentModal()" class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            {{-- Modal Header --}}
            <div class="p-6 border-b border-gray-200 relative">
                <h2 class="text-2xl font-bold text-gray-800 text-center" x-text="`Penilaian untuk ${selectedAlternativeName}`"></h2>
                <button @click="closeAssessmentModal()" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Modal Body - Form Penilaian (Semua Kriteria Menjadi Dropdown) --}}
            <div class="p-6 space-y-4">
                <template x-if="Object.keys(tempAssessments).length === rankedCriterias.length && rankedCriterias.length > 0">
                    <template x-for="criteria in rankedCriterias" :key="criteria.kriteria_id">
                        <div>
                            <label x-text="criteria.name" class="block text-lg font-medium text-gray-700 mb-2"></label>
                            {{-- SEMUA KRITERIA MENGGUNAKAN DROPDOWN --}}
                            <select x-model="tempAssessments[criteria.kriteria_id].selectedSubkriteriaId"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">Pilih Subkriteria</option>
                                <template x-if="subCriterias[criteria.kriteria_id] && subCriterias[criteria.kriteria_id].length > 0">
                                    <template x-for="subCriteriaItem in subCriterias[criteria.kriteria_id]" :key="subCriteriaItem.subkriteria_id">
                                        <option :value="subCriteriaItem.subkriteria_id" x-text="subCriteriaItem.name"></option>
                                    </template>
                                </template>
                                <template x-else>
                                    <option value="" disabled>Tidak ada subkriteria tersedia</option>
                                </template>
                            </select>
                        </div>
                    </template>
                </template>
                <template x-else>
                    <div class="p-6 text-center text-gray-500">Loading penilaian...</div>
                </template>
            </div>

            {{-- Modal Footer --}}
            <div class="p-6 border-t border-gray-200 flex justify-end">
                <button @click="saveAssessments"
                        :disabled="!isFormValid()"
                        class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                    Simpan Penilaian
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('assessmentPage', () => ({
        showAssessmentModal: false,
        selectedAlternativeId: null,
        selectedAlternativeName: '',
        rankedCriterias: @json($rankedCriterias),
        subCriterias: @json($subCriterias),
        existingSelections: @json($existingSelections),
        tempAssessments: {},

        init() {
            console.log('Alpine.js component initialized.');
            console.log('Initial rankedCriterias:', this.rankedCriterias);
            console.log('Initial subCriterias (from PHP):', this.subCriterias);
            console.log('Initial existingSelections:', this.existingSelections);

            if (Object.keys(this.subCriterias).length === 0) {
                console.warn('Warning: subCriterias object is empty. Check your controller query.');
            }
        },

        openAssessmentModal(alternativeId, alternativeName) {
            this.selectedAlternativeId = alternativeId;
            this.selectedAlternativeName = alternativeName;

            let newTempAssessments = {};

            this.rankedCriterias.forEach(criteria => {
                // Untuk semua kriteria, inisialisasi hanya selectedSubkriteriaId
                newTempAssessments[criteria.kriteria_id] = {
                    selectedSubkriteriaId: ''
                };

                const key = `${this.selectedAlternativeId}-${criteria.kriteria_id}`;
                const existingSelection = this.existingSelections[key];

                if (existingSelection) {
                    newTempAssessments[criteria.kriteria_id].selectedSubkriteriaId = existingSelection.subkriteria_id;
                }

                console.log(`Processing criteria: ${criteria.name} (ID: ${criteria.kriteria_id})`);
                if (this.subCriterias[criteria.kriteria_id]) {
                    console.log(`  Subcriterias for ${criteria.name}:`, this.subCriterias[criteria.kriteria_id]);
                } else {
                    console.log(`  No subcriterias found in 'this.subCriterias' for ${criteria.name} (ID: ${criteria.kriteria_id}).`);
                }
            });

            this.tempAssessments = newTempAssessments;
            this.showAssessmentModal = true;
            console.log('tempAssessments after initialization and before modal show:', this.tempAssessments);
        },

        closeAssessmentModal() {
            this.showAssessmentModal = false;
            this.selectedAlternativeId = null;
            this.selectedAlternativeName = '';
            this.tempAssessments = {};
        },

        isFormValid() {
            if (Object.keys(this.tempAssessments).length !== this.rankedCriterias.length) {
                return false;
            }

            return this.rankedCriterias.every(criteria => {
                const temp = this.tempAssessments[criteria.kriteria_id];
                // Sekarang hanya perlu memeriksa selectedSubkriteriaId
                return temp && temp.selectedSubkriteriaId !== '';
            });
        },

        async saveAssessments() {
            if (!this.isFormValid()) {
                alert('Harap lengkapi semua penilaian sebelum menyimpan.');
                return;
            }

            const submissions = [];
            for (const criteria of this.rankedCriterias) { // Menggunakan for...of untuk async/await compatibility jika diperlukan, tapi forEach juga OK
                const temp = this.tempAssessments[criteria.kriteria_id];

                const submissionData = {
                    kriteria_id: criteria.kriteria_id,
                    value: null, // Ini akan diisi di backend
                    selectedSubkriteriaId: temp.selectedSubkriteriaId // Selalu ambil dari selectedSubkriteriaId
                };
                submissions.push(submissionData);
            }

            const dataToSubmit = {
                user_id: '{{ Auth::id() }}',
                alternative_id: this.selectedAlternativeId,
                assessments: submissions,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };

            console.log('Data yang akan disimpan:', dataToSubmit);

            try {
                const response = await fetch('{{ route('spk.save-assessment') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': dataToSubmit._token
                    },
                    body: JSON.stringify(dataToSubmit)
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Gagal menyimpan penilaian.');
                }

                const result = await response.json();
                console.log('Success:', result);

                result.updatedAssessments.forEach(assessment => {
                    const key = `${this.selectedAlternativeId}-${assessment.kriteria_id}`;
                    this.existingSelections[key] = {
                        subkriteria_id: assessment.subkriteria_id,
                        value: assessment.value
                    };
                });

                alert(result.message);
                this.closeAssessmentModal();

            } catch (error) {
                console.error('Error menyimpan penilaian:', error);
                alert('Terjadi kesalahan saat menyimpan penilaian: ' + error.message);
            }
        },

        allCriteriaAssessed(alternativeId) {
            if (!this.rankedCriterias || this.rankedCriterias.length === 0) {
                return false;
            }

            return this.rankedCriterias.every(criteria => {
                const key = `${alternativeId}-${criteria.kriteria_id}`;
                const existing = this.existingSelections[key];
                // Semua kriteria sekarang dinilai dengan subkriteria_id
                return existing && existing.subkriteria_id !== null && existing.subkriteria_id !== '';
            });
        }
    }));
});
</script>
@endsection