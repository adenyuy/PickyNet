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
        Schema::create('sub_criterias', function (Blueprint $table) {
            $table->uuid('subkriteria_id')->primary();
            $table->uuid('kriteria_id');
            $table->string('name');
            $table->integer('value'); // biasanya 1-4
            $table->decimal('weight', 5, 2);
            $table->timestamps();

            $table->foreign('kriteria_id')->references('kriteria_id')->on('criterias')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_criterias');
    }
};
