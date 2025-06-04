<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\Criteria;
use App\Models\SpkSession; // Model baru kita
use App\Models\SubCriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SpkController extends Controller
{
    /**
     * Menampilkan halaman 'pick' dimana user memilih alternatif dan meranking kriteria.
     */
    public function showPickPage()
    {
        // Ambil semua alternatif yang aktif/tersedia
        $alternatives = Alternative::orderBy('name')->get();

        // Ambil semua kriteria yang akan diranking oleh user
        // Anda mungkin ingin mengurutkannya berdasarkan urutan default jika ada, atau nama
        $criterias = Criteria::orderBy('name')->get();

        return view('pick.index', compact('alternatives', 'criterias'));
    }

    /**
     * Memproses pilihan awal pengguna (alternatif & kriteria dari pick page)
     * dan memulai/membuat record SpkSession baru.
     */
    public function startNewSpkSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'selected_alternatives' => 'required|array|min:2', // Minimal 2 alternatif
            'selected_alternatives.*' => 'string|exists:alternatives,alternative_id',
            'ranked_criterias' => 'required|array|min:1', // Minimal 1 kriteria
            'ranked_criterias.*' => 'string|exists:criterias,kriteria_id',
        ]);

        if ($validator->fails()) {
            // Jika request dari AJAX (misalnya dari modal di pick page)
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            // Jika dari form biasa, kembali dengan error dan input lama
            return back()->withErrors($validator)->withInput();
        }

        $selectedAlternativeIds = $request->input('selected_alternatives');
        $rankedCriteriaIds = $request->input('ranked_criterias'); // Ini adalah array ID kriteria yang sudah diurutkan

        // Ambil detail kriteria (termasuk nama dan tipe) berdasarkan urutan ranking dari user
        $criteriaDetailsOrdered = Criteria::whereIn('kriteria_id', $rankedCriteriaIds)
            ->orderByRaw('FIELD(kriteria_id, "' . implode('","', array_map('addslashes', $rankedCriteriaIds)) . '")')
            ->get(['kriteria_id', 'name', 'type', 'input_method']); // Ambil juga input_method

        if ($criteriaDetailsOrdered->count() !== count($rankedCriteriaIds)) {
            $message = 'Satu atau lebih kriteria yang dipilih tidak valid atau tidak ditemukan.';
            return $request->expectsJson() ? response()->json(['error' => $message], 422) : back()->with('error', $message)->withInput();
        }

        // Hitung bobot kriteria berdasarkan ranking (contoh: Rank Reciprocal)
        $criteriaRankingAndWeights = [];
        $sumOfReciprocals = 0;
        foreach ($rankedCriteriaIds as $index => $kriteriaId) {
            $rank = $index + 1;
            $sumOfReciprocals += 1 / $rank;
        }

        foreach ($criteriaDetailsOrdered as $index => $criterion) {
            $rank = $index + 1;
            $weight = 0;
            if ($sumOfReciprocals > 0) {
                $weight = round((1 / $rank) / $sumOfReciprocals, 4); // Pembulatan 4 angka desimal
            }
            $criteriaRankingAndWeights[] = [
                'criteria_id' => $criterion->kriteria_id,
                'name' => $criterion->name,
                'type' => $criterion->type,
                'input_method' => $criterion->input_method, // Simpan input_method
                'rank' => $rank,
                'weight' => $weight,
            ];
        }

        // Buat record SpkSession baru
        try {
            $spkSession = SpkSession::create([
                'user_id' => Auth::id(),
                'session_name' => 'SPK Sesi - ' . Auth::user()->username . ' - ' . now()->format('d M Y H:i'), // Nama sesi default
                'selected_alternatives' => $selectedAlternativeIds,
                'criteria_ranking_and_weights' => $criteriaRankingAndWeights,
                // Inisialisasi field JSON lain sebagai array kosong agar tidak null
                'user_scores' => [],
                'normalized_matrix' => [],
                'q1_values' => [],
                'q2_values' => [],
                'final_qi_ranking' => [],
            ]);

            // Simpan ID sesi SPK ini ke dalam session PHP untuk langkah selanjutnya
            $request->session()->put('current_spk_session_id', $spkSession->id);

            Log::info('SPK Session created:', ['session_id' => $spkSession->id, 'user_id' => Auth::id()]);

            // Arahkan ke halaman overview (sesuai kesepakatan kita)
            if ($request->expectsJson()) { // Jika ini dari AJAX call di pick page
                return response()->json([
                    'message' => 'Sesi SPK berhasil dimulai. Mengarahkan ke Overview...',
                    'redirect_url' => route('spk.overview')
                ]);
            }
            return redirect()->route('spk.overview');

        } catch (\Exception $e) {
            Log::error('Gagal membuat SPK Session: ' . $e->getMessage(), ['exception' => $e]);
            $message = 'Terjadi kesalahan internal saat memulai sesi SPK. Silakan coba lagi.';
            return $request->expectsJson() ? response()->json(['error' => $message], 500) : back()->with('error', $message)->withInput();
        }
    }


    /**
     * Menampilkan halaman overview SPK berdasarkan sesi aktif.
     */
    public function showOverview(Request $request)
    {
        $sessionId = $request->session()->get('current_spk_session_id');

        if (!$sessionId) {
            return redirect()->route('spk.pick')->with('error', 'Tidak ada sesi SPK yang aktif. Silakan mulai pemilihan dari awal.');
        }

        $spkSession = SpkSession::find($sessionId); // Tidak perlu with('user') jika tidak menampilkan info user di sini

        if (!$spkSession) {
            // Jika karena alasan tertentu sesi tidak ditemukan, hapus ID dari session dan arahkan kembali
            $request->session()->forget('current_spk_session_id');
            return redirect()->route('spk.pick')->with('error', 'Sesi SPK tidak valid. Silakan mulai pemilihan dari awal.');
        }

        // Ambil detail alternatif dari ID yang tersimpan di JSON selected_alternatives
        $selectedAlternativeDetails = Alternative::whereIn('alternative_id', $spkSession->selected_alternatives ?: [])->get();

        // criteria_ranking_and_weights sudah berisi detail kriteria yang terurut dan memiliki bobot
        // Ini sudah dalam bentuk array PHP karena di-cast di model SpkSession
        $rankedCriteriaDetails = collect($spkSession->criteria_ranking_and_weights ?: []);

        return view('spk.overview', [
            'spkSession' => $spkSession, // Kirim seluruh objek sesi jika perlu IDnya di view
            'selectedAlternativeDetails' => $selectedAlternativeDetails,
            'rankedCriteriaDetails' => $rankedCriteriaDetails,
        ]);
    }

    // ... (lanjutan SpkController.php) ...

    /**
     * Menampilkan halaman penilaian (assessment) untuk alternatif yang dipilih dalam sesi SPK aktif.
     * Menggantikan 'assessment()' lama.
     */
    public function showAssessmentPage(Request $request)
    {
        $sessionId = $request->session()->get('current_spk_session_id');
        if (!$sessionId) {
            return redirect()->route('spk.pick')->with('error', 'Sesi SPK tidak aktif. Silakan mulai pemilihan dari awal.');
        }

        $spkSession = SpkSession::findOrFail($sessionId);

        // 1. Ambil detail alternatif yang dipilih dari database
        $alternatives = Alternative::whereIn('alternative_id', $spkSession->selected_alternatives)->get();

        // 2. Ambil kriteria yang akan dinilai dari data sesi
        // $spkSession->criteria_ranking_and_weights sudah berisi:
        // [{ "criteria_id", "name", "type", "input_method", "rank", "weight" }, ...]
        $rankedCriteriasFromSession = $spkSession->criteria_ranking_and_weights;
        $criteriaIdsForSelectInput = [];
        foreach ($rankedCriteriasFromSession as $criterion) {
            if ($criterion['input_method'] === 'select') {
                $criteriaIdsForSelectInput[] = $criterion['criteria_id'];
            }
        }

        // 3. Load sub-kriteria HANYA untuk kriteria dengan input_method 'select'
        $subCriteriasForView = [];
        if (!empty($criteriaIdsForSelectInput)) {
            $criteriaWithSubCriterias = Criteria::whereIn('kriteria_id', $criteriaIdsForSelectInput)
                                                ->with('subCriterias') // Eager load relasi
                                                ->get()
                                                ->keyBy('kriteria_id');

            foreach ($criteriaIdsForSelectInput as $criteriaId) {
                if (isset($criteriaWithSubCriterias[$criteriaId]) && $criteriaWithSubCriterias[$criteriaId]->subCriterias) {
                    $subCriteriasForView[$criteriaId] = $criteriaWithSubCriterias[$criteriaId]->subCriterias->map(fn($sub) => [
                        'subkriteria_id' => $sub->subkriteria_id,
                        'name' => $sub->name,
                        // 'value' dari subkriteria akan digunakan saat penyimpanan, tidak perlu di-pass ke option value di HTML jika ID yg dikirim
                    ])->all();
                } else {
                    $subCriteriasForView[$criteriaId] = []; // Tidak ada sub-kriteria
                }
            }
        }

        // 4. Siapkan data skor yang sudah ada untuk pre-fill form di Alpine.js
        // Struktur ini disesuaikan dengan yang dibutuhkan oleh Alpine.js Anda:
        // existingSelections: { 'alternative_id-kriteria_id': { subkriteria_id: X, direct_input_value: Y, final_value: Z } }
        $alpineExistingSelections = [];
        if (!empty($spkSession->user_scores)) {
            foreach ($spkSession->user_scores as $altScoreEntry) { // Loop per alternatif
                $altId = $altScoreEntry['alternative_id'];
                if(isset($altScoreEntry['scores']) && is_array($altScoreEntry['scores'])){
                    foreach ($altScoreEntry['scores'] as $critScoreEntry) { // Loop per skor kriteria
                        $critId = $critScoreEntry['criteria_id'];
                        $key = $altId . '-' . $critId;
                        
                        $alpineExistingSelections[$key] = [
                            // Jika input 'select', selected_sub_criterion_id akan ada
                            'subkriteria_id' => $critScoreEntry['selected_sub_criterion_id'] ?? null,
                            // Jika input 'direct_value', direct_input_value akan ada
                            'direct_input_value' => $critScoreEntry['direct_input_value'] ?? null,
                            // 'value' adalah skor akhir, mungkin tidak perlu di pre-fill jika UI hanya butuh input awal
                        ];
                    }
                }
            }
        }

        return view('spk.assessment', [
            'spkSessionId' => $spkSession->id, // Kirim ID sesi untuk AJAX call
            'selectedAlternatives' => $alternatives,
            'rankedCriterias' => $rankedCriteriasFromSession, // Ini adalah array dari spkSession, sudah ada 'input_method'
            'subCriteriasForSelect' => $subCriteriasForView, // Hanya subkriteria untuk tipe 'select'
            'existingSelections' => $alpineExistingSelections,
        ]);
    }

     public function storeAlternativeScore(Request $request)
    {
        $sessionId = $request->session()->get('current_spk_session_id');
        if (!$sessionId) {
            return response()->json(['error' => 'Sesi SPK tidak aktif atau telah berakhir. Harap mulai ulang dari halaman pick.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'alternative_id' => 'required|string|exists:alternatives,alternative_id',
            'assessments' => 'required|array', // Ini adalah array objek dari Alpine.js
            // Validasi lebih detail untuk 'assessments.*. ...' bisa dilakukan di dalam loop jika kompleks
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $spkSession = SpkSession::findOrFail($sessionId);
        $alternativeIdToScore = $request->input('alternative_id');
        $submittedCriteriaScores = $request->input('assessments'); // Ini adalah array seperti [{criteria_id, selectedSubkriteriaId?, directValueInput?}, ...]

        // Pastikan alternatif ini ada dalam selected_alternatives sesi ini
        if (!in_array($alternativeIdToScore, $spkSession->selected_alternatives)) {
            return response()->json(['error' => 'Alternatif tidak valid untuk sesi SPK ini.'], 422);
        }

        // Ambil semua kriteria yang relevan untuk sesi ini (termasuk input_method)
        $sessionCriteriaInfo = collect($spkSession->criteria_ranking_and_weights)->keyBy('criteria_id');

        $processedScoresForThisAlternative = [];

        foreach ($submittedCriteriaScores as $submittedScoreItem) {
            $criteriaId = $submittedScoreItem['kriteria_id'] ?? null;

            if (!$criteriaId || !$sessionCriteriaInfo->has($criteriaId)) {
                Log::warning("Data penilaian diterima untuk kriteria_id '{$criteriaId}' yang tidak ada dalam sesi SPK.", ['session_id' => $sessionId, 'alternative_id' => $alternativeIdToScore]);
                continue; // Lewati kriteria yang tidak valid
            }

            $criterionInfo = $sessionCriteriaInfo->get($criteriaId);
            $finalScoreValue = 0;
            $selectedSubCriterionId = null;
            $selectedSubCriterionName = 'N/A';
            $directInputValue = null;

            if ($criterionInfo['input_method'] === 'select') {
                $selectedSubCriterionId = $submittedScoreItem['selectedSubkriteriaId'] ?? null;
                if ($selectedSubCriterionId) {
                    $subCriterion = SubCriteria::find($selectedSubCriterionId);
                    if ($subCriterion && $subCriterion->kriteria_id === $criteriaId) {
                        $finalScoreValue = $subCriterion->value;
                        $selectedSubCriterionName = $subCriterion->name;
                    } else {
                        Log::warning("Subkriteria '{$selectedSubCriterionId}' tidak valid atau tidak cocok untuk kriteria '{$criteriaId}'.", ['session_id' => $sessionId]);
                        // Biarkan $finalScoreValue = 0 atau handle error sesuai kebutuhan
                    }
                }
            } elseif ($criterionInfo['input_method'] === 'direct_value') {
                $directInputValue = $submittedScoreItem['directValueInput'] ?? null;
                if (is_numeric($directInputValue)) {
                    $directInputValue = (float) $directInputValue; // Konversi ke float/numeric
                    // Cari sub-kriteria (rentang) yang cocok
                    $matchingSubCriterion = SubCriteria::where('kriteria_id', $criteriaId)
                        ->where(function ($query) use ($directInputValue) {
                            $query->where(function ($q) use ($directInputValue) { // Kondisi untuk range_min DAN range_max diisi
                                $q->whereNotNull('range_min')
                                  ->whereNotNull('range_max')
                                  ->where('range_min', '<=', $directInputValue)
                                  ->where('range_max', '>=', $directInputValue);
                            })->orWhere(function ($q) use ($directInputValue) { // Kondisi untuk range_min diisi, range_max null (lebih besar dari)
                                $q->whereNotNull('range_min')
                                  ->whereNull('range_max')
                                  ->where('range_min', '<=', $directInputValue);
                            })->orWhere(function ($q) use ($directInputValue) { // Kondisi untuk range_max diisi, range_min null (lebih kecil dari)
                                $q->whereNotNull('range_max')
                                  ->whereNull('range_min')
                                  ->where('range_max', '>=', $directInputValue);
                            })->orWhere(function ($q) { // Kondisi jika keduanya null (sangat tidak mungkin untuk range, tapi sebagai fallback)
                                $q->whereNull('range_min')
                                  ->whereNull('range_max');
                            });
                        })
                        ->orderBy('value', $criterionInfo['type'] === 'cost' ? 'asc' : 'desc') // Prioritaskan yg memberi skor lebih baik jika ada overlap (jarang)
                        ->first();

                    if ($matchingSubCriterion) {
                        $finalScoreValue = $matchingSubCriterion->value;
                        $selectedSubCriterionId = $matchingSubCriterion->subkriteria_id;
                        $selectedSubCriterionName = $matchingSubCriterion->name;
                    } else {
                        Log::warning("Tidak ada rentang sub-kriteria yang cocok untuk input '{$directInputValue}' pada kriteria '{$criteriaId}'.", ['session_id' => $sessionId]);
                        // Biarkan $finalScoreValue = 0 atau handle error (misal, beri skor terendah)
                    }
                } else if ($directInputValue !== null) { // Input ada tapi tidak numerik
                     Log::warning("Input '{$directInputValue}' tidak numerik untuk kriteria direct_value '{$criteriaId}'.", ['session_id' => $sessionId]);
                }
            }

            $processedScoresForThisAlternative[] = [
                'criteria_id' => $criteriaId,
                'criteria_name' => $criterionInfo['name'],
                'selected_sub_criterion_id' => $selectedSubCriterionId,
                'selected_sub_criterion_name' => $selectedSubCriterionName,
                'direct_input_value' => ($criterionInfo['input_method'] === 'direct_value') ? $directInputValue : null,
                'value' => $finalScoreValue,
            ];
        }

        // Update atau tambahkan skor untuk alternatif ini di spkSession->user_scores
        $currentUserScores = $spkSession->user_scores ?: []; // Ambil array user_scores yang ada, atau array kosong jika null
        $alternativeEntryFound = false;
        $alternativeDetail = Alternative::find($alternativeIdToScore);

        foreach ($currentUserScores as $key => $altScoreEntry) {
            if ($altScoreEntry['alternative_id'] === $alternativeIdToScore) {
                $currentUserScores[$key]['scores'] = $processedScoresForThisAlternative;
                // Jika nama alternatif belum ada atau ingin diupdate
                $currentUserScores[$key]['alternative_name'] = $alternativeDetail ? $alternativeDetail->name : 'Unknown Alternative';
                $alternativeEntryFound = true;
                break;
            }
        }

        if (!$alternativeEntryFound) {
            $currentUserScores[] = [
                'alternative_id' => $alternativeIdToScore,
                'alternative_name' => $alternativeDetail ? $alternativeDetail->name : 'Unknown Alternative',
                'scores' => $processedScoresForThisAlternative,
            ];
        }

        $spkSession->user_scores = $currentUserScores; // Update field JSON
        $spkSession->save();

        Log::info('Scores updated for alternative in SPK Session:', [
            'session_id' => $sessionId,
            'alternative_id' => $alternativeIdToScore
        ]);

        return response()->json([
            'message' => 'Penilaian untuk ' . ($alternativeDetail->name ?? $alternativeIdToScore) . ' berhasil disimpan.',
            'processed_scores' => $processedScoresForThisAlternative // Kirim kembali skor yang diproses untuk update UI jika perlu
        ]);
    }

        public function finalizeAndProcessSpk(Request $request)
    {
        $sessionId = $request->session()->get('current_spk_session_id');
        if (!$sessionId) {
            $errorMessage = 'Sesi SPK tidak aktif atau telah berakhir. Harap mulai ulang dari halaman pemilihan.';
            if ($request->expectsJson()) {
                return response()->json(['error' => $errorMessage, 'redirect_url' => route('spk.pick')], 401);
            }
            return redirect()->route('spk.pick')->with('error', $errorMessage);
        }

        $spkSession = SpkSession::findOrFail($sessionId);

        // 1. Validasi: Cek apakah semua alternatif yang dipilih sudah memiliki skor
        $selectedAlternativeIds = $spkSession->selected_alternatives;
        $userScoresArray = $spkSession->user_scores ?: [];
        $alternativesWithScores = array_column($userScoresArray, 'alternative_id');

        $missingScoresFor = [];
        foreach ($selectedAlternativeIds as $selectedAltId) {
            if (!in_array($selectedAltId, $alternativesWithScores)) {
                $missingScoresFor[] = $selectedAltId;
            }
        }

        if (!empty($missingScoresFor)) {
            $missingNames = Alternative::whereIn('alternative_id', $missingScoresFor)->pluck('name')->join(', ');
            $errorMessage = 'Penilaian belum lengkap untuk alternatif berikut: ' . $missingNames . '. Harap lengkapi semua penilaian sebelum melanjutkan.';
            if ($request->expectsJson()) {
                return response()->json(['error' => $errorMessage, 'missing_alternatives' => $missingScoresFor], 422);
            }
            return redirect()->route('spk.assessment')->with('error', $errorMessage);
        }

        // 2. Panggil metode untuk melakukan perhitungan WASPAS.
        // Metode ini akan mengisi field hasil (normalized_matrix, q1_values, dll.) pada objek $spkSession.
        try {
            $this->performWaspasCalculation($spkSession);

            // 3. Simpan semua perubahan pada SpkSession (termasuk hasil kalkulasi)
            $spkSession->save();

            // 4. Hapus ID sesi SPK dari session PHP karena proses ini sudah selesai.
            $request->session()->forget('current_spk_session_id');

            Log::info('SPK Calculation successfully completed and results saved for session:', ['session_id' => $spkSession->id]);

            // 5. Arahkan ke halaman yang menampilkan hasil perhitungan atau ranking
            // Kita akan arahkan ke halaman detail perhitungan dulu, lalu dari sana bisa ke ranking.
            $successMessage = 'Perhitungan SPK berhasil diselesaikan.';
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $successMessage,
                    'session_id' => $spkSession->id, // Kirim ID sesi jika frontend perlu
                    'redirect_url' => route('spk.calculation.show', ['sessionId' => $spkSession->id])
                ]);
            }
            return redirect()->route('spk.calculation.show', ['sessionId' => $spkSession->id])->with('success', $successMessage);

        } catch (\Exception $e) {
            Log::error('Error during WASPAS calculation or saving results for session ' . $sessionId . ': ' . $e->getMessage(), ['exception' => $e]);
            $errorMessage = 'Terjadi kesalahan saat melakukan perhitungan SPK. Silakan coba lagi atau hubungi administrator.';
             if ($request->expectsJson()) {
                return response()->json(['error' => $errorMessage], 500);
            }
            return redirect()->route('spk.assessment')->with('error', $errorMessage);
        }
    }

    private function performWaspasCalculation(SpkSession $spkSession): void
    {
        // 1. Ambil data yang dibutuhkan dari SpkSession
        //    Properti JSON di SpkSession sudah otomatis di-cast ke array/object oleh Eloquent.
        $selectedAlternativeIds = $spkSession->selected_alternatives;
        $criteriaDetails = collect($spkSession->criteria_ranking_and_weights)->keyBy('criteria_id'); // Kolom ini sudah berisi 'criteria_id', 'name', 'type', 'rank', 'weight'
        $userScoresByAlternative = collect($spkSession->user_scores)->keyBy('alternative_id');

        // Ambil detail nama alternatif untuk hasil akhir
        $alternativesFromDb = Alternative::whereIn('alternative_id', $selectedAlternativeIds)->get()->keyBy('alternative_id');

        // Inisialisasi array untuk menyimpan hasil langkah per langkah
        $decisionMatrix = [];       // Matriks Xij (skor mentah dari user)
        $normalizedMatrix = [];     // Matriks Rij (setelah normalisasi)
        $weights = [];              // Bobot wj
        $q1Values = [];             // Nilai Q_i^(1) (Weighted Sum Model)
        $q2Values = [];             // Nilai Q_i^(2) (Weighted Product Model)
        $finalQiScores = [];        // Nilai Q_i (Skor Akhir WASPAS)
        $finalRankedResults = [];   // Hasil akhir yang sudah diranking

        // 2. Bangun Decision Matrix (Xij) dari user_scores
        foreach ($selectedAlternativeIds as $altId) {
            if (isset($userScoresByAlternative[$altId]) && isset($userScoresByAlternative[$altId]['scores'])) {
                $scoresForCurrentAlt = collect($userScoresByAlternative[$altId]['scores'])->keyBy('criteria_id');
                foreach ($criteriaDetails as $critId => $criterion) {
                    $decisionMatrix[$altId][$critId] = $scoresForCurrentAlt[$critId]['value'] ?? 0; // Ambil 'value' skor
                }
            } else {
                // Jika ada alternatif terpilih tapi tidak ada skornya (seharusnya sudah divalidasi di finalizeAndProcessSpk)
                // Isi dengan 0 atau handle error
                foreach ($criteriaDetails as $critId => $criterion) {
                    $decisionMatrix[$altId][$critId] = 0;
                }
                Log::warning("Tidak ada skor ditemukan untuk alternatif '$altId' di sesi '{$spkSession->id}' saat membangun decision matrix.");
            }
        }

        // 3. Normalisasi Decision Matrix (Rij)
        //    Temukan nilai max (untuk benefit) dan min (untuk cost) per kriteria
        $maxCritValues = [];
        $minCritValues = [];
        foreach ($criteriaDetails as $critId => $criterion) {
            $columnValues = [];
            foreach ($selectedAlternativeIds as $altId) {
                $columnValues[] = $decisionMatrix[$altId][$critId];
            }

            if (empty($columnValues)) continue; // Lewati jika tidak ada nilai untuk kriteria ini

            if ($criterion['type'] === 'benefit') {
                $maxCritValues[$critId] = max($columnValues);
            } else { // type === 'cost'
                $minCritValues[$critId] = min($columnValues);
            }
        }

        foreach ($selectedAlternativeIds as $altId) {
            foreach ($criteriaDetails as $critId => $criterion) {
                $rij = $decisionMatrix[$altId][$critId];
                if ($criterion['type'] === 'benefit') {
                    $maxVal = $maxCritValues[$critId] ?? 0;
                    $normalizedMatrix[$altId][$critId] = ($maxVal != 0) ? round($rij / $maxVal, 4) : 0;
                } else { // type === 'cost'
                    $minVal = $minCritValues[$critId] ?? 0;
                    $normalizedMatrix[$altId][$critId] = ($rij != 0) ? round($minVal / $rij, 4) : 0;
                }
            }
        }

        // 4. Ambil Bobot Kriteria (wj)
        //    Bobot sudah dihitung dan disimpan di $spkSession->criteria_ranking_and_weights
        foreach ($criteriaDetails as $critId => $criterion) {
            $weights[$critId] = $criterion['weight'];
        }

        // 5. Hitung Q_i^(1) (Weighted Sum Model - Additive)
        foreach ($selectedAlternativeIds as $altId) {
            $sum = 0;
            foreach ($criteriaDetails as $critId => $criterion) {
                $sum += ($normalizedMatrix[$altId][$critId] * $weights[$critId]);
            }
            $q1Values[$altId] = round($sum, 4);
        }

        // 6. Hitung Q_i^(2) (Weighted Product Model - Multiplicative)
        foreach ($selectedAlternativeIds as $altId) {
            $product = 1.0; // Gunakan float untuk perkalian
            foreach ($criteriaDetails as $critId => $criterion) {
                $base = $normalizedMatrix[$altId][$critId];
                $exponent = $weights[$critId];

                if ($base == 0 && $exponent == 0) { // Konvensi 0^0 = 1 dalam beberapa konteks SPK
                    $term = 1.0;
                } elseif ($base == 0 && $exponent > 0) {
                    $term = 0.0;
                } elseif ($base > 0) { // Hanya hitung pow jika base positif untuk menghindari error domain dengan eksponen pecahan
                    $term = pow((float)$base, (float)$exponent);
                } else { // Jika base negatif atau nol dengan eksponen non-nol lainnya, bisa jadi 0 atau perlu penanganan khusus
                    $term = 0.0; // Default aman, atau sesuaikan jika diperlukan
                }
                $product *= $term;
            }
            $q2Values[$altId] = round($product, 4);
        }

        // 7. Hitung Nilai Akhir Qi (Joint Generalized Criterion)
        $lambda = 0.5; // Koefisien lambda, bisa disesuaikan atau dibuat dinamis
        foreach ($selectedAlternativeIds as $altId) {
            $finalQiScores[$altId] = round(($lambda * $q1Values[$altId]) + ((1 - $lambda) * $q2Values[$altId]), 4);
        }

        // 8. Buat Hasil Akhir yang Sudah Diranking
        arsort($finalQiScores); // Urutkan Qi dari tertinggi ke terendah, mempertahankan kunci (alternative_id)
        $rank = 1;
        foreach ($finalQiScores as $altId => $score) {
            $finalRankedResults[] = [
                'alternative_id' => $altId,
                'alternative_name' => $alternativesFromDb[$altId]->name ?? 'Unknown Alternative',
                // Anda bisa tambahkan detail Q1 dan Q2 jika ingin ditampilkan di tabel ranking akhir
                'q1_value' => $q1Values[$altId] ?? 0,
                'q2_value' => $q2Values[$altId] ?? 0,
                'final_qi' => $score,
                'rank' => $rank++,
            ];
        }

        // 9. Simpan semua hasil perhitungan ke objek SpkSession
        $spkSession->normalized_matrix = $normalizedMatrix;
        $spkSession->q1_values = $q1Values; // Ini adalah array [alternative_id => q1_score]
        $spkSession->q2_values = $q2Values; // Ini adalah array [alternative_id => q2_score]
        $spkSession->final_qi_ranking = $finalRankedResults; // Ini adalah array hasil akhir yang sudah dirangking
        // Bobot dan decision matrix mentah juga bisa disimpan jika diperlukan untuk ditampilkan
        // $spkSession->decision_matrix_raw = $decisionMatrix; // Opsional
        // $spkSession->calculated_weights = $weights; // Opsional, karena sudah ada di criteria_ranking_and_weights
    }

    public function showCalculationPage(Request $request, $sessionId)
    {
        $spkSession = SpkSession::find($sessionId);

        if (!$spkSession) {
            return redirect()->route('spk.pick')->with('error', 'Sesi SPK tidak ditemukan.');
        }

        // Opsional: Pastikan hanya pemilik sesi yang bisa melihat hasilnya
        if (Auth::id() !== $spkSession->user_id) {
            // Anda bisa memilih untuk redirect atau tampilkan error 403 (Forbidden)
            // return redirect()->route('home')->with('error', 'Anda tidak berhak melihat sesi SPK ini.');
            abort(403, 'ANDA TIDAK BERHAK MENGAKSES SESI SPK INI.');
        }

        // Pastikan semua data perhitungan sudah ada
        if (empty($spkSession->normalized_matrix) || empty($spkSession->final_qi_ranking)) {
            return redirect()->route('spk.pick')->with('warning', 'Perhitungan untuk sesi ini belum selesai atau data tidak lengkap.');
        }

        // Siapkan data dalam format yang mungkin diharapkan oleh view calculation.blade.php lama Anda
        // (jika view tersebut mengharapkan struktur $calculationResults tertentu)
        $calculationResults = [
            'decision_matrix' => [], // Matriks skor mentah
            'normalized_matrix' => $spkSession->normalized_matrix,
            'weights' => [],
            'additive_importance' => $spkSession->q1_values,       // [alternative_id => q1_score]
            'multiplicative_importance' => $spkSession->q2_values, // [alternative_id => q2_score]
            'joint_criterion' => [],                              // [alternative_id => final_qi_score]
            'alternative_details' => Alternative::whereIn('alternative_id', $spkSession->selected_alternatives)
                                                ->get()->keyBy('alternative_id')->all(), // Objek, bukan array toArray() agar bisa akses properti model
            'criteria_details' => collect($spkSession->criteria_ranking_and_weights)
                                                ->keyBy('criteria_id')->all(), // Objek, bukan array toArray()
        ];

        // Rekonstruksi decision_matrix dari spkSession->user_scores
        if (!empty($spkSession->user_scores)) {
            foreach ($spkSession->user_scores as $altScoreEntry) {
                $altId = $altScoreEntry['alternative_id'];
                if (isset($altScoreEntry['scores']) && is_array($altScoreEntry['scores'])) {
                    foreach ($altScoreEntry['scores'] as $critScoreEntry) {
                        $critId = $critScoreEntry['criteria_id'];
                        $calculationResults['decision_matrix'][$altId][$critId] = $critScoreEntry['value'];
                    }
                }
            }
        }

        // Rekonstruksi weights dari spkSession->criteria_ranking_and_weights
        foreach ($spkSession->criteria_ranking_and_weights as $criterion) {
            $calculationResults['weights'][$criterion['criteria_id']] = $criterion['weight'];
        }

        // Rekonstruksi joint_criterion (final_qi) dari spkSession->final_qi_ranking
        if (!empty($spkSession->final_qi_ranking)) {
            foreach ($spkSession->final_qi_ranking as $rankedItem) {
                $calculationResults['joint_criterion'][$rankedItem['alternative_id']] = $rankedItem['final_qi'];
            }
        }
        
        return view('spk.calculation', [
            'spkSession' => $spkSession, // Kirim seluruh objek SpkSession jika view membutuhkannya
            'calculationResults' => $calculationResults // Kirim array yang sudah diformat agar cocok dengan view lama
        ]);
    }

        public function showRankPage(Request $request, $sessionId)
    {
        $spkSession = SpkSession::find($sessionId);

        if (!$spkSession) {
            return redirect()->route('spk.pick')->with('error', 'Sesi SPK tidak ditemukan.');
        }

        // Opsional: Cek kepemilikan sesi
        if (Auth::id() !== $spkSession->user_id) {
            abort(403, 'ANDA TIDAK BERHAK MENGAKSES SESI SPK INI.');
        }

        // Ambil hasil ranking dari SpkSession
        // final_qi_ranking sudah berisi array terurut dengan nama alternatif, skor, dan rank.
        $rankingResults = $spkSession->final_qi_ranking;

        if (empty($rankingResults)) {
            // Jika hasil ranking kosong (mungkin perhitungan belum selesai atau ada masalah)
            return redirect()->route('spk.calculation.show', ['sessionId' => $sessionId])
                             ->with('warning', 'Hasil ranking belum tersedia. Silakan periksa detail perhitungan.');
        }

        return view('spk.rank', [
            'rankingResults' => $rankingResults,
            'spkSession' => $spkSession // Opsional, jika view rank perlu info sesi lain
        ]);
    }
}