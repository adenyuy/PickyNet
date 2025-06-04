@extends('layouts.spk_flow')

@section('title', 'Penilaian Alternatif')
@section('progress_width', '50%')
@section('back_link_route', route('spk.overview')) {{-- Kembali ke overview sesi saat ini --}}
@section('page_header_title', 'Penilaian Alternatif')

@section('content')
{{-- $selectedAlternatives, $rankedCriterias, $subCriteriasForSelect, $existingSelections, $spkSessionId dipass dari controller --}}
<div x-data="assessmentPage(
    '{{ $spkSessionId }}',
    {{ json_encode($selectedAlternatives->map->only(['alternative_id', 'name', 'image_path'])) }},
    {{ json_encode($rankedCriterias) }},
    {{ json_encode($subCriteriasForSelect) }},
    {{ json_encode($existingSelections) }}
)" class="w-full h-full flex flex-col items-center justify-start py-6">

    {{-- Daftar Alternatif --}}
    <div class="space-y-4 w-full max-w-2xl mx-auto mb-8">
        @forelse($selectedAlternatives as $alternative)
            <div @click="openAssessmentModal('{{ $alternative->alternative_id }}', '{{ $alternative->name }}')"
                 class="bg-white rounded-lg shadow-md p-4 flex items-center justify-between cursor-pointer hover:shadow-lg transition duration-200">
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('storage/' . $alternative->image_path) }}" alt="{{ $alternative->name }}" class="h-12 w-12 object-contain">
                    <span class="text-xl font-semibold text-gray-800">{{ $alternative->name }}</span>
                </div>
                {{-- Indikator status penilaian --}}
                <template x-if="isAlternativeFullyAssessed('{{ $alternative->alternative_id }}')">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </template>
                <template x-else>
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </template>
            </div>
        @empty
            <p class="text-center text-gray-600">Tidak ada alternatif yang dipilih untuk penilaian.</p>
        @endforelse
    </div>

    {{-- Tombol Finalisasi Penilaian (muncul jika semua alternatif sudah dinilai) --}}
    <div class="w-full max-w-2xl mx-auto text-center" x-show="selectedAlternatives.length > 0">
        <button @click="finalizeAssessments"
                :disabled="!areAllAlternativesFullyAssessed()"
                class="bg-green-500 hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-3 px-8 rounded-lg text-lg transition duration-300">
            <span x-show="!finalizing">Selesai & Lanjutkan ke Perhitungan</span>
            <span x-show="finalizing">Memproses...</span>
        </button>
        <p x-show="!areAllAlternativesFullyAssessed()" class="text-sm text-red-500 mt-2">
            Harap lengkapi penilaian untuk semua alternatif.
        </p>
    </div>


    {{-- Modal Penilaian (Alpine.js) --}}
    <div x-show="showAssessmentModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50" x-cloak @keydown.escape.window="closeAssessmentModal()">
        <div @click.away="closeAssessmentModal()" class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-[90vh] flex flex-col">
            {{-- Modal Header --}}
            <div class="p-6 border-b border-gray-200 relative">
                <h2 class="text-2xl font-bold text-gray-800 text-center" x-text="`Penilaian untuk ${selectedAlternativeName}`"></h2>
                <button @click="closeAssessmentModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Modal Body - Form Penilaian --}}
            <div class="p-6 space-y-4 overflow-y-auto">
                <template x-if="rankedCriterias.length > 0">
                    <template x-for="criteria in rankedCriterias" :key="criteria.criteria_id">
                        <div>
                            <label :for="`assess_${selectedAlternativeId}_${criteria.criteria_id}`" x-text="criteria.name" class="block text-lg font-medium text-gray-700 mb-1"></label>
                            
                            {{-- Input untuk Tipe 'select' --}}
                            <template x-if="criteria.input_method === 'select'">
                                <select :id="`assess_${selectedAlternativeId}_${criteria.criteria_id}`"
                                        x-model="tempAssessments[criteria.criteria_id].selectedSubkriteriaId"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="">Pilih Subkriteria</option>
                                    <template x-if="subCriteriasForSelect[criteria.criteria_id] && subCriteriasForSelect[criteria.criteria_id].length > 0">
                                        <template x-for="subItem in subCriteriasForSelect[criteria.criteria_id]" :key="subItem.subkriteria_id">
                                            <option :value="subItem.subkriteria_id" x-text="subItem.name"></option>
                                        </template>
                                    </template>
                                    <template x-else>
                                        <option value="" disabled>Tidak ada subkriteria (tipe select)</option>
                                    </template>
                                </select>
                            </template>

                            {{-- Input untuk Tipe 'direct_value' --}}
                            <template x-if="criteria.input_method === 'direct_value'">
                                <input type="number" :id="`assess_${selectedAlternativeId}_${criteria.criteria_id}`"
                                       x-model.number="tempAssessments[criteria.criteria_id].directValueInput"
                                       placeholder="Masukkan nilai numerik"
                                       class="mt-1 block w-full pl-3 pr-3 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                {{-- Anda bisa tambahkan validasi atau petunjuk rentang di sini jika perlu --}}
                            </template>
                        </div>
                    </template>
                </template>
                <template x-else>
                    <div class="p-6 text-center text-gray-500">Tidak ada kriteria untuk dinilai.</div>
                </template>
            </div>

            {{-- Modal Footer --}}
            <div class="p-6 border-t border-gray-200 flex justify-end">
                <button @click="saveCurrentAlternativeAssessment"
                        :disabled="!isModalFormValid() || savingAlternative"
                        class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                    <span x-show="!savingAlternative">Simpan Penilaian</span>
                    <span x-show="savingAlternative">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('assessmentPage', (spkSessionId, alternatives, criterias, subCriteriasOptions, initialExistingSelections) => ({
        showAssessmentModal: false,
        selectedAlternativeId: null,
        selectedAlternativeName: '',
        spkSessionId: spkSessionId,
        
        // Data dari PHP Controller
        selectedAlternatives: alternatives, // Ini adalah array objek {alternative_id, name, image_path}
        rankedCriterias: criterias,         // Ini adalah array objek {criteria_id, name, type, input_method, rank, weight}
        subCriteriasForSelect: subCriteriasOptions, // { criteria_id: [ {subkriteria_id, name, value}, ... ] }
        
        // Menyimpan skor yang sudah ada dari server, format: { 'alt_id-crit_id': { subkriteria_id: X, direct_input_value: Y } }
        // Akan diupdate setelah setiap penyimpanan sukses
        liveExistingSelections: initialExistingSelections,

        tempAssessments: {}, // Untuk form di modal: { criteria_id: { selectedSubkriteriaId: '', directValueInput: '' } }
        savingAlternative: false,
        finalizing: false,

        init() {
            console.log('Assessment Page Alpine Data Initialized');
            console.log('Alternatives:', this.selectedAlternatives);
            console.log('Ranked Criterias:', this.rankedCriterias);
            console.log('SubCriterias for Select:', this.subCriteriasForSelect);
            console.log('Initial Existing Selections:', this.liveExistingSelections);
        },

        openAssessmentModal(alternativeId, alternativeName) {
            this.selectedAlternativeId = alternativeId;
            this.selectedAlternativeName = alternativeName;
            this.tempAssessments = {}; // Reset

            this.rankedCriterias.forEach(criteria => {
                const key = `${alternativeId}-${criteria.criteria_id}`;
                const existing = this.liveExistingSelections[key];
                
                this.tempAssessments[criteria.criteria_id] = {
                    selectedSubkriteriaId: (criteria.input_method === 'select' && existing) ? (existing.subkriteria_id || '') : '',
                    directValueInput: (criteria.input_method === 'direct_value' && existing) ? (existing.direct_input_value || '') : ''
                };
            });
            this.showAssessmentModal = true;
        },

        closeAssessmentModal() {
            this.showAssessmentModal = false;
            // Tidak perlu reset selectedAlternativeId dll di sini karena akan di-set ulang saat openAssessmentModal
        },

        isModalFormValid() {
            if (Object.keys(this.tempAssessments).length === 0) return false;
            return this.rankedCriterias.every(criteria => {
                const assessment = this.tempAssessments[criteria.criteria_id];
                if (!assessment) return false;
                if (criteria.input_method === 'select') {
                    return assessment.selectedSubkriteriaId && assessment.selectedSubkriteriaId !== '';
                } else if (criteria.input_method === 'direct_value') {
                    return assessment.directValueInput !== '' && assessment.directValueInput !== null && !isNaN(parseFloat(assessment.directValueInput));
                }
                return false; // Kriteria tanpa input_method yang dikenali
            });
        },

        async saveCurrentAlternativeAssessment() {
            if (!this.isModalFormValid()) {
                alert('Harap lengkapi semua field penilaian di modal ini.');
                return;
            }
            this.savingAlternative = true;

            const submissions = [];
            this.rankedCriterias.forEach(criteria => {
                const assessmentData = this.tempAssessments[criteria.criteria_id];
                submissions.push({
                    kriteria_id: criteria.criteria_id,
                    // Kirim keduanya, backend akan memilih berdasarkan input_method kriteria
                    selectedSubkriteriaId: assessmentData.selectedSubkriteriaId || null,
                    directValueInput: assessmentData.directValueInput !== '' ? assessmentData.directValueInput : null
                });
            });

            const dataToSubmit = {
                alternative_id: this.selectedAlternativeId,
                assessments: submissions,
                _token: '{{ csrf_token() }}' // Ambil CSRF dari Blade
            };

            try {
                const response = await fetch('{{ route('spk.assessment.store.alternative') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': dataToSubmit._token
                    },
                    body: JSON.stringify(dataToSubmit)
                });
                const result = await response.json();

                if (!response.ok) {
                    let errorMsg = result.message || 'Gagal menyimpan penilaian.';
                    if (result.errors) {
                        errorMsg += "\nDetails:\n";
                        for (const field in result.errors) {
                            errorMsg += `- ${result.errors[field].join(', ')}\n`;
                        }
                    }
                    throw new Error(errorMsg);
                }
                
                alert(result.message);
                // Update liveExistingSelections dengan data yang baru disimpan/diproses dari backend
                if (result.processed_scores && Array.isArray(result.processed_scores)) {
                    result.processed_scores.forEach(ps => {
                        const key = `${this.selectedAlternativeId}-${ps.criteria_id}`;
                        this.liveExistingSelections[key] = {
                            subkriteria_id: ps.selected_sub_criterion_id, // Ini ID subkriteria (bisa dari 'select' atau hasil mapping 'direct_value')
                            direct_input_value: ps.direct_input_value, // Input mentah jika ada
                            value: ps.value // Skor akhir
                        };
                    });
                }
                this.closeAssessmentModal();
            } catch (error) {
                console.error('Error menyimpan penilaian:', error);
                alert('Terjadi kesalahan: ' + error.message);
            } finally {
                this.savingAlternative = false;
            }
        },
        
        // Untuk ikon ceklis/silang di daftar alternatif utama
        isAlternativeFullyAssessed(alternativeId) {
            if (!this.rankedCriterias || this.rankedCriterias.length === 0) return true; // Anggap terisi jika tidak ada kriteria
            return this.rankedCriterias.every(criteria => {
                const key = `${alternativeId}-${criteria.criteria_id}`;
                const existing = this.liveExistingSelections[key];
                // Cukup cek apakah ada entri 'value' (skor akhir) yang valid.
                // Backend akan memastikan 'value' terisi jika input valid.
                return existing && (existing.value !== null && existing.value !== undefined);
            });
        },

        // Untuk mengaktifkan/menonaktifkan tombol finalisasi
        areAllAlternativesFullyAssessed() {
            if (!this.selectedAlternatives || this.selectedAlternatives.length === 0) return false;
            return this.selectedAlternatives.every(alt => this.isAlternativeFullyAssessed(alt.alternative_id));
        },

        async finalizeAssessments() {
            if (!this.areAllAlternativesFullyAssessed()) {
                alert('Harap lengkapi penilaian untuk semua alternatif yang dipilih.');
                return;
            }
            if (!confirm('Apakah Anda yakin ingin menyelesaikan semua penilaian dan melanjutkan ke perhitungan? Anda tidak dapat mengubah penilaian setelah ini untuk sesi ini.')) {
                return;
            }
            this.finalizing = true;

            try {
                const response = await fetch('{{ route('spk.assessment.finalize') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ spk_session_id: this.spkSessionId }) // Kirim ID sesi jika diperlukan backend
                });
                const result = await response.json();

                if (!response.ok) {
                     let errorMsg = result.message || result.error || 'Gagal memfinalisasi penilaian.';
                    if (result.errors) { // Untuk error validasi (misal, jika backend cek ulang)
                        errorMsg += "\nDetails:\n";
                        for (const field in result.errors) {
                            errorMsg += `- ${result.errors[field].join(', ')}\n`;
                        }
                    }
                    throw new Error(errorMsg);
                }

                alert(result.message);
                if (result.redirect_url) {
                    window.location.href = result.redirect_url;
                }

            } catch (error) {
                console.error('Error memfinalisasi penilaian:', error);
                alert('Terjadi kesalahan: ' + error.message);
            } finally {
                this.finalizing = false;
            }
        }
    }));
});
</script>
@endsection