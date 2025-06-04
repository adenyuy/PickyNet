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
        Schema::table('criterias', function (Blueprint $table) {
            // Kolom 'input_method' akan menentukan bagaimana user menginput nilai untuk kriteria ini.
            // 'select': User memilih dari daftar sub-kriteria yang sudah ada nilainya.
            // 'direct_value': User menginput angka/nilai langsung, yang kemudian akan dicocokkan dengan rentang di sub_criterias.
            $table->enum('input_method', ['select', 'direct_value'])
                  ->default('select') // Defaultnya adalah memilih dari sub-kriteria
                  ->after('type');    // Posisi kolom setelah kolom 'type', bisa disesuaikan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('criterias', function (Blueprint $table) {
            // Cek dulu apakah kolomnya ada sebelum mencoba drop, untuk menghindari error jika migrasi di-rollback berkali-kali
            if (Schema::hasColumn('criterias', 'input_method')) {
                $table->dropColumn('input_method');
            }
        });
    }
};