<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection; // Penting: Ini harus ada!
use App\Models\Alternative;
use App\Models\Criteria;
use App\Models\Score;
use App\Models\SubCriteria;
use App\Models\Calculation; // Import model Calculation
use App\Models\Log as UserLog;
class SpkController extends Controller
{

    public function processData(Request $request)
    {
        $request->validate([
            'selected_alternatives' => 'required|array|min:2',
            'selected_alternatives.*' => 'string',
            'ranked_criterias' => 'required|array|min:1',
            'ranked_criterias.*' => 'string',
        ]);

        $selectedAlternatives = $request->input('selected_alternatives');
        $rankedCriterias = $request->input('ranked_criterias');

        $request->session()->put('spk_selection_data', [
            'alternatives' => $selectedAlternatives,
            'criterias_rank' => $rankedCriterias,
        ]);

        Log::info('SPK data processed:', [
            'alternatives' => $selectedAlternatives,
            'criterias_rank' => $rankedCriterias
        ]);

        return response()->json(['message' => 'Data successfully processed and stored in session.']);
    }

    /**
     * Menampilkan halaman overview dengan alternatif dan kriteria terpilih.
     */
    public function overview(Request $request)
    {
        $spkData = $request->session()->get('spk_selection_data', [
            'alternatives' => [],
            'criterias_rank' => []
        ]);

        if (empty($spkData['alternatives']) || empty($spkData['criterias_rank'])) {
            return redirect()->route('pick')->with('error', 'Silakan pilih alternatif dan kriteria terlebih dahulu.');
        }

        $selectedAlternativeDetails = Alternative::whereIn('alternative_id', $spkData['alternatives'])->get();
        $rankedCriteriaDetails = Criteria::whereIn('kriteria_id', $spkData['criterias_rank'])
            ->orderByRaw('FIELD(kriteria_id, "' . implode('","', $spkData['criterias_rank']) . '")')
            ->get();

        return view('spk.overview', [
            'selectedAlternativeDetails' => $selectedAlternativeDetails,
            'rankedCriteriaDetails' => $rankedCriteriaDetails,
        ]);
    }

    /**
     * Menampilkan halaman penilaian (assessment) untuk alternatif yang dipilih.
     */
    public function assessment(Request $request)
    {
        $spkData = $request->session()->get('spk_selection_data', [
            'alternatives' => [],
            'criterias_rank' => []
        ]);


        if (empty($spkData['alternatives']) || empty($spkData['criterias_rank'])) {
            return redirect()->route('pick')->with('error', 'Silakan pilih alternatif dan kriteria terlebih dahulu.');
        }

        $selectedAlternatives = Alternative::whereIn('alternative_id', $spkData['alternatives'])->get();

        $rankedCriterias = Criteria::whereIn('kriteria_id', $spkData['criterias_rank'])
            ->orderByRaw('FIELD(kriteria_id, "' . implode('","', $spkData['criterias_rank']) . '")')
            ->get();

        $subCriterias = SubCriteria::whereIn('kriteria_id', $rankedCriterias->pluck('kriteria_id'))
            ->get()
            ->groupBy('kriteria_id');


        $existingSelections = Score::where('user_id', Auth::id())
            ->whereIn('alternative_id', $selectedAlternatives->pluck('alternative_id'))
            ->get()
            ->keyBy(function ($item) {
                return $item->alternative_id . '-' . $item->kriteria_id;
            });

        return view('spk.assessment', [
            'selectedAlternatives' => $selectedAlternatives,
            'rankedCriterias' => $rankedCriterias,
            'subCriterias' => $subCriterias,
            'existingSelections' => $existingSelections,
        ]);
    }

    /**
     * Menyimpan penilaian user untuk setiap alternatif dan kriteria.
     */
    public function saveAssessment(Request $request)
    {
        $request->validate([
            'user_id' => 'required|uuid',
            'alternative_id' => 'required|uuid',
            'assessments' => 'required|array',
            'assessments.*.kriteria_id' => 'required|uuid',
            // 'assessments.*.value' => 'nullable|numeric', // Hapus ini karena tidak ada lagi input numerik langsung dari user
            'assessments.*.selectedSubkriteriaId' => 'required|uuid', // Sekarang ini selalu wajib
        ]);

        $userId = $request->input('user_id');
        $alternativeId = $request->input('alternative_id');
        $assessments = $request->input('assessments');

        $updatedAssessments = [];

        foreach ($assessments as $assessment) {
            $kriteriaId = $assessment['kriteria_id'];
            // $inputValue = $assessment['value']; // Hapus ini
            $selectedSubkriteriaId = $assessment['selectedSubkriteriaId'];

            $finalValue = null;
            $finalSubkriteriaId = $selectedSubkriteriaId; // Ambil langsung dari yang dipilih user

            $criteria = Criteria::find($kriteriaId);
            if (!$criteria) {
                Log::warning("Kriteria dengan ID {$kriteriaId} tidak ditemukan saat menyimpan penilaian.");
                continue;
            }

            // AMBIL NILAI (VALUE) DARI SUBKRITERIA YANG DIPILIH
            $chosenSub = SubCriteria::find($finalSubkriteriaId);
            if (!$chosenSub) {
                Log::warning("Subkriteria dengan ID {$finalSubkriteriaId} tidak ditemukan.");
                // Anda bisa melempar error di sini jika Anda ingin validasi yang lebih ketat
                continue;
            }
            $finalValue = (float) $chosenSub->value; // Nilai ini yang akan digunakan dalam perhitungan WASPAS

            // Simpan atau update ke tabel scores
            Score::updateOrCreate(
                [
                    'user_id' => $userId,
                    'alternative_id' => $alternativeId,
                    'kriteria_id' => $kriteriaId,
                ],
                [
                    'subkriteria_id' => $finalSubkriteriaId,
                    'value' => $finalValue,
                ]
            );

            $updatedAssessments[] = [
                'kriteria_id' => $kriteriaId,
                'subkriteria_id' => $finalSubkriteriaId,
                'value' => $finalValue,
            ];
        }

        return response()->json([
            'message' => 'Penilaian berhasil disimpan.',
            'updatedAssessments' => $updatedAssessments
        ]);
    }
    public function calculation(Request $request)
    {
        // Tetap lakukan validasi dasar untuk memastikan user sudah memilih alternatif/kriteria
        $spkData = $request->session()->get('spk_selection_data', [
            'alternatives' => [],
            'criterias_rank' => []
        ]);

        if (empty($spkData['alternatives']) || empty($spkData['criterias_rank'])) {
            return redirect()->route('pick')->with('error', 'Silakan pilih alternatif dan kriteria terlebih dahulu.');
        }
        
        // Tidak perlu menghitung dan menyimpan ke DB di sini lagi.
        // Itu akan dilakukan di getCalculationData atau sudah dilakukan saat user selesai Assessment.
        // Jika Anda ingin memastikan data tersimpan sebelum ke halaman calculation,
        // maka logika penyimpanan di DB harusnya di method `saveAssessment` atau endpoint terpisah yang dipanggil dari Assessment.
        // Untuk saat ini, kita asumsikan data sudah tersimpan di DB atau akan diambil saat tombol diklik.

        // Cukup render view.
        return view('spk.calculation');
    }

    // Metode baru untuk mendapatkan data perhitungan WASPAS dari database via AJAX
    public function getCalculationData(Request $request)
    {
        $spkData = $request->session()->get('spk_selection_data', [
            'alternatives' => [],
            'criterias_rank' => []
        ]);

        if (empty($spkData['alternatives']) || empty($spkData['criterias_rank'])) {
            return response()->json(['error' => 'No alternatives or criterias selected in session.'], 400);
        }

        $userId = Auth::id();
        $selectedAlternativeIds = $spkData['alternatives'];
        $rankedCriteriaIds = $spkData['criterias_rank']; // Ini digunakan untuk bobot

        // 1. Dapatkan hasil akhir Q1, Q2, Q_final, dan Ranking dari tabel `logs`
        // Ambil log terbaru user ini (atau log spesifik jika Anda akan punya parameter ID log)
        $latestUserLog = UserLog::where('user_id', $userId)
                                ->latest() // Ambil yang terbaru
                                ->first();

        if (!$latestUserLog) {
            return response()->json(['error' => 'No calculation history found for this user.'], 404);
        }

        // Ambil detail normalisasi dari tabel `calculations` yang terkait dengan log ini
        $calculationsByLog = Calculation::where('user_id', $userId)
                                       ->where('log_id', $latestUserLog->log_id)
                                       ->get()
                                       ->groupBy(['alternative_id', 'kriteria_id']);

        if ($calculationsByLog->isEmpty()) {
            return response()->json(['error' => 'Detailed calculations not found for this log entry.'], 404);
        }

        // Dapatkan detail alternatif dan kriteria untuk memudahkan frontend
        $alternatives = Alternative::whereIn('alternative_id', $selectedAlternativeIds)->get();
        $criterias = Criteria::whereIn('kriteria_id', $rankedCriteriaIds)
                                ->orderByRaw('FIELD(kriteria_id, "' . implode('","', $rankedCriteriaIds) . '")')
                                ->get();

        // 2. Rekonstruksi data menjadi format $calculationResults yang dibutuhkan frontend
        $reconstructedResults = [
            'decision_matrix' => [],
            'normalized_matrix' => [],
            'weights' => [], // Bobot perlu dihitung ulang karena tidak disimpan di DB `calculations`
            'additive_importance' => [], // Dari `logs.result`
            'multiplicative_importance' => [], // Dari `logs.result`
            'joint_criterion' => [], // Dari `logs.result`
            'alternative_details' => $alternatives->keyBy('alternative_id')->toArray(),
            'criteria_details' => $criterias->keyBy('kriteria_id')->toArray(),
        ];

        // Isi decision_matrix dan normalized_matrix dari `calculationsByLog`
        foreach ($calculationsByLog as $alternativeId => $criteriaData) {
            foreach ($criteriaData as $kriteriaId => $calculationEntry) {
                $reconstructedResults['decision_matrix'][$alternativeId][$kriteriaId] = $calculationEntry->first()->score_raw;
                $reconstructedResults['normalized_matrix'][$alternativeId][$kriteriaId] = $calculationEntry->first()->score_normalized;
            }
        }

        // Isi weights (bobot dari ranked_criterias perlu dihitung ulang)
        $sumOfReciprocals = 0;
        foreach ($rankedCriteriaIds as $index => $kriteriaId) {
            $rank = $index + 1;
            $sumOfReciprocals += 1 / $rank;
        }
        foreach ($rankedCriteriaIds as $index => $kriteriaId) {
            $rank = $index + 1;
            $reconstructedResults['weights'][$kriteriaId] = round((1 / $rank) / $sumOfReciprocals, 4);
        }

        // Isi additive_importance, multiplicative_importance, joint_criterion dari `latestUserLog->result`
        // Perhatikan bahwa $latestUserLog->result sudah merupakan array/objek dari cast 'json'
        foreach ($latestUserLog->result as $item) {
            if (isset($item['alternative_id'])) {
                $reconstructedResults['additive_importance'][$item['alternative_id']] = $item['qi_add'];
                $reconstructedResults['multiplicative_importance'][$item['alternative_id']] = $item['qi_multi'];
                $reconstructedResults['joint_criterion'][$item['alternative_id']] = $item['final_qi'];
            }
        }

        // Jika semua data ditemukan, kembalikan JSON
        return response()->json($reconstructedResults);
    }


    /**
     * Melakukan perhitungan WASPAS.
     * @param Collection $alternatives
     * @param Collection $criterias
     * @param Collection $scores
     * @param array $rankedCriteriaIds
     * @return array
     */
    private function performWaspasCalculation($alternatives, $criterias, $scores, array $rankedCriteriaIds)
    {
        $results = [];

        // --- Step 0: Inisialisasi Decision Matrix (Nilai mentah dari user) ---
        $decisionMatrix = [];
        foreach ($alternatives as $alt) {
            foreach ($criterias as $crit) {
                $decisionMatrix[$alt->alternative_id][$crit->kriteria_id] = $scores[$alt->alternative_id][$crit->kriteria_id]->first()->value;
            }
        }
        $results['decision_matrix'] = $decisionMatrix;

        // --- Step 1: Normalized Decision Matrix ---
        $normalizedMatrix = [];
        $maxMinValues = []; // Menyimpan max/min per kriteria

        foreach ($criterias as $crit) {
            $columnValues = [];
            foreach ($alternatives as $alt) {
                $columnValues[] = $decisionMatrix[$alt->alternative_id][$crit->kriteria_id];
            }

            if ($crit->type === 'benefit') { // Asumsi ada kolom 'type' di tabel 'criterias'
                $maxMinValues[$crit->kriteria_id]['max'] = max($columnValues);
            } else { // cost
                $maxMinValues[$crit->kriteria_id]['min'] = min($columnValues);
            }
        }

        foreach ($alternatives as $alt) {
            foreach ($criterias as $crit) {
                $rij = $decisionMatrix[$alt->alternative_id][$crit->kriteria_id];
                $normalizedValue = 0;

                if ($crit->type === 'benefit') {
                    $maxRij = $maxMinValues[$crit->kriteria_id]['max'];
                    $normalizedValue = ($maxRij != 0) ? ($rij / $maxRij) : 0; // Hindari pembagian nol
                } else { // cost
                    $minRij = $maxMinValues[$crit->kriteria_id]['min'];
                    $normalizedValue = ($rij != 0) ? ($minRij / $rij) : 0; // Hindari pembagian nol
                }
                $normalizedMatrix[$alt->alternative_id][$crit->kriteria_id] = round($normalizedValue, 4); // Bulatkan untuk presisi
            }
        }
        $results['normalized_matrix'] = $normalizedMatrix;


        // --- Step 2: Bobot Kriteria (w_j) berdasarkan Rank Sum Weighting ---
        $weights = [];
        $sumOfReciprocals = 0;
        foreach ($rankedCriteriaIds as $index => $kriteriaId) {
            $rank = $index + 1; // Rank dimulai dari 1
            $sumOfReciprocals += 1 / $rank;
        }

        foreach ($rankedCriteriaIds as $index => $kriteriaId) {
            $rank = $index + 1;
            $weights[$kriteriaId] = round((1 / $rank) / $sumOfReciprocals, 4); // Bulatkan bobot
        }
        $results['weights'] = $weights;


        // --- Step 3: Additive Relative Importance (Q_i^(1)) ---
        $additiveImportance = [];
        foreach ($alternatives as $alt) {
            $sum = 0;
            foreach ($criterias as $crit) {
                $sum += $normalizedMatrix[$alt->alternative_id][$crit->kriteria_id] * $weights[$crit->kriteria_id];
            }
            $additiveImportance[$alt->alternative_id] = round($sum, 4); // Bulatkan hasil
        }
        $results['additive_importance'] = $additiveImportance;


        // --- Step 4: Multiplicative Relative Importance (Q_i^(2)) ---
        $multiplicativeImportance = [];
        foreach ($alternatives as $alt) {
            $product = 1;
            foreach ($criterias as $crit) {
                $base = $normalizedMatrix[$alt->alternative_id][$crit->kriteria_id];
                $exponent = $weights[$crit->kriteria_id];
                // Pastikan basis tidak nol jika eksponen bukan nol
                // Jika basis nol, dan eksponen positif, hasilnya nol. Jika eksponen nol, hasilnya 1.
                // Hindari 0^0, yang bisa jadi undefined atau 1 tergantung konteks.
                if ($base == 0 && $exponent > 0) {
                    $term = 0;
                } elseif ($base == 0 && $exponent == 0) { // Atau tentukan sebagai 1
                    $term = 1; // Konvensi umum untuk 0^0 dalam perhitungan seperti ini
                } else {
                    $term = pow($base, $exponent);
                }

                $product *= $term;
            }
            $multiplicativeImportance[$alt->alternative_id] = round($product, 4); // Bulatkan hasil
        }
        $results['multiplicative_importance'] = $multiplicativeImportance;


        // --- Step 5: Joint Generalized Criterion (Q_i) ---
        $jointCriterion = [];
        foreach ($alternatives as $alt) {
            $q1 = $additiveImportance[$alt->alternative_id];
            $q2 = $multiplicativeImportance[$alt->alternative_id];
            $jointCriterion[$alt->alternative_id] = round(0.5 * $q1 + 0.5 * $q2, 4); // Bulatkan hasil
        }
        $results['joint_criterion'] = $jointCriterion;

        // Tambahkan informasi alternatif dan kriteria ke hasil untuk tampilan di frontend
        $results['alternative_details'] = $alternatives->keyBy('alternative_id');
        $results['criteria_details'] = $criterias->keyBy('kriteria_id');

        return $results;
    }

        public function rank(Request $request)
    {
        $userId = Auth::id();

        // Ambil log perhitungan terbaru untuk user yang sedang login
        $latestLog = UserLog::where('user_id', $userId)
                             ->latest() // Mengurutkan berdasarkan `created_at` DESC
                             ->first(); // Mengambil satu log terbaru

        $rankingResults = [];
        if ($latestLog && $latestLog->result) {
            // Kolom 'result' di-cast sebagai JSON di model Log, jadi sudah berupa array/objek PHP
            $rankingResults = $latestLog->result;
            // Data sudah diurutkan berdasarkan final_qi saat disimpan di SPKController::calculation,
            // jadi kita tidak perlu mengurutkan lagi di sini.
        } else {
            // Jika tidak ada log ditemukan, arahkan user ke halaman awal atau berikan pesan
            return redirect()->route('pick')->with('error', 'Tidak ada hasil perhitungan yang ditemukan. Silakan mulai proses SPK dari awal.');
        }

        return view('spk.rank', [
            'rankingResults' => $rankingResults,
        ]);
    }
}