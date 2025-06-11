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
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </template>
            <template x-else>
                <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
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
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
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
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                    :class="{'border-red-500': validationErrors[criteria.criteria_id]}">
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
                                    class="mt-1 block w-full pl-3 pr-3 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                    :class="{'border-red-500': validationErrors[criteria.criteria_id]}">
                            </template>

                            {{-- Tampilkan pesan error --}}
                            <p x-show="validationErrors[criteria.criteria_id]" x-text="validationErrors[criteria.criteria_id]" class="text-red-500 text-sm mt-1"></p>
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

            selectedAlternatives: alternatives,
            rankedCriterias: criterias,
            subCriteriasForSelect: subCriteriasOptions,

            liveExistingSelections: initialExistingSelections,

            tempAssessments: {},
            validationErrors: {}, // Objek untuk menyimpan pesan error validasi per kriteria_id
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
                this.tempAssessments = {}; // Reset penilaian sementara
                this.validationErrors = {}; // Reset error validasi saat modal dibuka

                // Populate tempAssessments with existing data or defaults
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
            },

            isModalFormValid() {
                if (Object.keys(this.tempAssessments).length === 0 && this.rankedCriterias.length > 0) {
                    // If there are criteria but no tempAssessments, something is wrong
                    return false;
                }

                this.validationErrors = {}; // Reset errors on each validation attempt
                let allValid = true;

                this.rankedCriterias.forEach(criteria => {
                    const assessment = this.tempAssessments[criteria.criteria_id];

                    if (!assessment) {
                        this.validationErrors[criteria.criteria_id] = `Penilaian untuk ${criteria.name} tidak ditemukan.`;
                        allValid = false;
                        return;
                    }

                    if (criteria.input_method === 'select') {
                        if (!assessment.selectedSubkriteriaId || assessment.selectedSubkriteriaId === '') {
                            this.validationErrors[criteria.criteria_id] = `Pilih subkriteria untuk ${criteria.name}.`;
                            allValid = false;
                        }
                    } else if (criteria.input_method === 'direct_value') {
                        const inputValue = assessment.directValueInput;
                        const numericValue = parseFloat(inputValue);

                        // 1. Validasi Kosong
                        if (inputValue === '' || inputValue === null || inputValue === undefined) {
                            this.validationErrors[criteria.criteria_id] = `Masukkan nilai untuk ${criteria.name}.`;
                            allValid = false;
                        }
                        // 2. Validasi Angka
                        else if (isNaN(numericValue) || !isFinite(inputValue)) {
                            this.validationErrors[criteria.criteria_id] = `Nilai untuk ${criteria.name} harus berupa angka.`;
                            allValid = false;
                        }
                        // 3. Validasi Nilai Negatif atau Nol
                        else if (numericValue <= 0) {
                            this.validationErrors[criteria.criteria_id] = `Nilai untuk ${criteria.name} harus lebih besar dari 0.`;
                            allValid = false;
                        }
                        // Tambahkan validasi rentang min/max jika ada di objek kriteria
                        // else if (criteria.min_value !== undefined && numericValue < criteria.min_value) {
                        //     this.validationErrors[criteria.criteria_id] = `Nilai minimum untuk ${criteria.name} adalah ${criteria.min_value}.`;
                        //     allValid = false;
                        // } else if (criteria.max_value !== undefined && numericValue > criteria.max_value) {
                        //     this.validationErrors[criteria.criteria_id] = `Nilai maksimum untuk ${criteria.name} adalah ${criteria.max_value}.`;
                        //     allValid = false;
                        // }
                    } else {
                        this.validationErrors[criteria.criteria_id] = `Metode input tidak dikenal untuk ${criteria.name}.`;
                        allValid = false;
                    }
                });

                return allValid;
            },

            async saveCurrentAlternativeAssessment() {
                // Panggil validasi form modal
                if (!this.isModalFormValid()) {
                    // HANYA TAMPILKAN ERROR DI BAWAH FIELD. TIDAK ADA SWEETALERT DI SINI.
                    return; // Hentikan eksekusi jika validasi gagal
                }

                this.savingAlternative = true; // Aktifkan spinner

                const submissions = [];
                this.rankedCriterias.forEach(criteria => {
                    const assessmentData = this.tempAssessments[criteria.criteria_id];
                    submissions.push({
                        kriteria_id: criteria.criteria_id,
                        selectedSubkriteriaId: assessmentData.selectedSubkriteriaId || null,
                        directValueInput: assessmentData.directValueInput !== '' ? assessmentData.directValueInput : null
                    });
                });

                const dataToSubmit = {
                    alternative_id: this.selectedAlternativeId,
                    assessments: submissions,
                    _token: '{{ csrf_token() }}'
                };

                try {
                    const response = await fetch('{{ route("spk.assessment.store.alternative") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': dataToSubmit._token
                        },
                        body: JSON.stringify(dataToSubmit)
                    });
                    const resultData = await response.json();

                    if (!response.ok) {
                        // Tangani error dari backend (termasuk validasi server-side)
                        if (resultData.errors) {
                            // Jika backend mengembalikan error validasi spesifik, tampilkan di bawah field
                            // Pastikan kunci error dari backend sesuai dengan criteria_id Anda
                            Object.keys(resultData.errors).forEach(criteriaId => {
                                this.validationErrors[criteriaId] = resultData.errors[criteriaId].join(', ');
                            });
                        }
                        // Opsional: Jika ada error umum dari backend yang bukan validasi field, tampilkan di SweetAlert
                        const generalErrorMsg = resultData.message || resultData.error || 'Terjadi kesalahan saat menyimpan penilaian.';
                        await Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan!',
                            text: generalErrorMsg,
                            confirmButtonText: 'OK'
                        });
                        return; // Penting: Hentikan eksekusi setelah menampilkan error dari backend
                    }

                    // Jika sukses, tampilkan SweetAlert sukses (ini tetap ada agar ada feedback)
                    await Swal.fire({
                        icon: 'success',
                        title: 'Penilaian Berhasil Disimpan',
                        text: resultData.message || 'Penilaian berhasil disimpan.',
                        confirmButtonText: 'OK',
                        timer: 1500, // Opsional: timer singkat untuk pesan sukses
                        timerProgressBar: true
                    });

                    // Update liveExistingSelections dengan data yang baru disimpan/diproses dari backend
                    if (resultData.processed_scores && Array.isArray(resultData.processed_scores)) {
                        resultData.processed_scores.forEach(ps => {
                            const key = `${this.selectedAlternativeId}-${ps.criteria_id}`;
                            this.liveExistingSelections[key] = {
                                subkriteria_id: ps.selected_sub_criterion_id,
                                direct_input_value: ps.direct_input_value,
                                value: ps.value
                            };
                        });
                    }
                    this.closeAssessmentModal(); // Tutup modal setelah sukses
                } catch (error) {
                    console.error('Error saat melakukan fetch atau memproses respons:', error);
                    // Jika terjadi error jaringan atau error tak terduga lainnya
                    await Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Terjadi kesalahan jaringan atau server tidak merespons. Silakan coba lagi.',
                        confirmButtonText: 'OK'
                    });
                } finally {
                    this.savingAlternative = false; // Nonaktifkan spinner
                }
            },

            // isAlternativeFullyAssessed dan areAllAlternativesFullyAssessed tetap sama
            isAlternativeFullyAssessed(alternativeId) {
                if (!this.rankedCriterias || this.rankedCriterias.length === 0) return true;
                return this.rankedCriterias.every(criteria => {
                    const key = `${alternativeId}-${criteria.criteria_id}`;
                    const existing = this.liveExistingSelections[key];
                    return existing && (existing.value !== null && existing.value !== undefined);
                });
            },

            areAllAlternativesFullyAssessed() {
                if (!this.selectedAlternatives || this.selectedAlternatives.length === 0) return false;
                return this.selectedAlternatives.every(alt => this.isAlternativeFullyAssessed(alt.alternative_id));
            },

            // Finalize Assessments (sesuai diskusi terakhir, ini tetap pakai SweetAlert)
            async finalizeAssessments() {
                if (!this.areAllAlternativesFullyAssessed()) {
                    await Swal.fire({
                        icon: 'warning',
                        text: 'Harap lengkapi penilaian untuk semua alternatif sebelum melanjutkan.',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                try {
                    const resultConfirmation = await Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: 'Anda tidak dapat mengubah penilaian setelah ini untuk sesi ini.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, selesaikan!',
                        cancelButtonText: 'Batal',
                        showLoaderOnConfirm: true,
                        allowOutsideClick: () => !Swal.isLoading()
                    });

                    if (resultConfirmation.isConfirmed) {
                        this.finalizing = true;

                        try {
                            const response = await fetch('{{ route("spk.assessment.finalize") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    spk_session_id: this.spkSessionId
                                })
                            });
                            const resultData = await response.json();

                            if (!response.ok) {
                                let errorMsg = resultData.message || resultData.error || 'Gagal memfinalisasi penilaian.';
                                if (resultData.errors) {
                                    errorMsg += "\nDetails:\n";
                                    for (const field in resultData.errors) {
                                        errorMsg += `- ${resultData.errors[field].join(', ')}\n`;
                                    }
                                }
                                throw new Error(errorMsg);
                            }

                            let timerInterval;
                            await Swal.fire({
                                icon: 'success',
                                title: 'Penilaian Berhasil',
                                text: resultData.message,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: () => {
                                    Swal.showLoading();
                                    const b = Swal.getHtmlContainer().querySelector('b');
                                    timerInterval = setInterval(() => {
                                        if (b) {
                                            b.textContent = Swal.getTimerLeft();
                                        }
                                    }, 100);
                                },
                                willClose: () => {
                                    clearInterval(timerInterval);
                                }
                            });
                            if (resultData.redirect_url) {
                                window.location.href = resultData.redirect_url;
                            }

                        } catch (error) {
                            console.error('Error memfinalisasi penilaian:', error);
                            await Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: error.message,
                                confirmButtonText: 'OK'
                            });
                        } finally {
                            this.finalizing = false;
                        }
                    } else {
                        console.log('Pembatalan oleh pengguna.');
                        this.finalizing = false;
                    }
                } catch (swalError) {
                    console.error('SweetAlert konfirmasi error:', swalError);
                    this.finalizing = false;
                }
            }
        }));
    });
</script>
@endsection