<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_spk_sessions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('spk_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Primary Key menggunakan UUID
            $table->foreignUuid('user_id')->constrained(table: 'users', column: 'user_id')->onDelete('cascade');            
            $table->string('session_name')->nullable(); // Nama sesi SPK, opsional
            $table->json('selected_alternatives'); // Menyimpan array ID alternatif yang dipilih
            $table->json('criteria_ranking_and_weights'); // Menyimpan array objek: {criteria_id, name, type, rank, weight}
            $table->json('user_scores')->nullable(); // Menyimpan array objek: [{alternative_id, alternative_name (ops), scores: [{criteria_id, criteria_name (ops), selected_sub_criterion_id, selected_sub_criterion_name, value}]}]
            $table->json('normalized_matrix')->nullable(); // Hasil matriks normalisasi
            $table->json('q1_values')->nullable(); // Hasil Q1 (Additive) per alternatif
            $table->json('q2_values')->nullable(); // Hasil Q2 (Multiplicative) per alternatif
            $table->json('final_qi_ranking')->nullable(); // Hasil Q final dan ranking per alternatif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_sessions');
    }
};