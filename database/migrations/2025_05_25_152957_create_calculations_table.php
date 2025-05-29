<?php

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
        Schema::create('calculations', function (Blueprint $table) {
            $table->uuid('calculation_id')->primary();
            $table->uuid('user_id');
            $table->uuid('alternative_id');
            $table->uuid('kriteria_id');
            $table->decimal('score_raw', 5, 2);
            $table->decimal('score_normalized', 8, 4);
            $table->decimal('qi_add', 8, 4);
            $table->decimal('qi_multi', 8, 4);
            $table->decimal('final_qi', 8, 4)->nullable(); // total akhir
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('alternative_id')->references('alternative_id')->on('alternatives')->onDelete('cascade');
            $table->foreign('kriteria_id')->references('kriteria_id')->on('criterias')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calculations');
    }
};
