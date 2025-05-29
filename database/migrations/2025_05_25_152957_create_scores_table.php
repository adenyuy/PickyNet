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
        Schema::create('scores', function (Blueprint $table) {
            $table->uuid('score_id')->primary();
            $table->uuid('user_id');
            $table->uuid('alternative_id');
            $table->uuid('kriteria_id');
            $table->uuid('subkriteria_id');
            $table->decimal('value', 5, 2);
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('alternative_id')->references('alternative_id')->on('alternatives')->onDelete('cascade');
            $table->foreign('kriteria_id')->references('kriteria_id')->on('criterias')->onDelete('cascade');
            $table->foreign('subkriteria_id')->references('subkriteria_id')->on('sub_criterias')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
