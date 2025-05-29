<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\Criteria;
use Illuminate\Http\Request;

class PickController extends Controller
{
    public function index()
    {
        // Ambil semua alternatif dari database
        $alternatives = Alternative::all();

        // Ambil semua kriteria dari database
        // Asumsi urutan default adalah sesuai urutan id atau anda bisa menambahkan 'order' kolom di tabel kriteria
        $criterias = Criteria::orderBy('name')->get(); // Urutkan berdasarkan nama atau kolom lain jika ada

        return view('pick.index', compact('alternatives', 'criterias'));
    }

    // Anda akan membutuhkan method lain untuk menangani submission dari modal
    // public function processPick(Request $request)
    // {
    //     $selectedAlternatives = $request->input('selected_alternatives');
    //     $rankedCriterias = $request->input('ranked_criterias');

    //     // Lakukan logika SPK WASPAS di sini
    //     // Simpan hasil, redirect, atau kirim response JSON

    //     return response()->json(['message' => 'Data received and processed!', 'data' => $request->all()]);
    // }
}