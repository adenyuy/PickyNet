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
        Schema::table('sub_criterias', function (Blueprint $table) {
            // Kolom ini hanya akan digunakan jika kriteria induknya memiliki input_method 'direct_value'.
            // Mereka mendefinisikan rentang numerik untuk sub-kriteria tersebut.
            // Nullable karena tidak semua sub-kriteria (atau kriteria) akan menggunakan rentang ini.
            $table->decimal('range_min', 15, 2)->nullable()->after('value'); // Batas bawah rentang
            $table->decimal('range_max', 15, 2)->nullable()->after('range_min'); // Batas atas rentang
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_criterias', function (Blueprint $table) {
            if (Schema::hasColumn('sub_criterias', 'range_min')) {
                $table->dropColumn('range_min');
            }
            if (Schema::hasColumn('sub_criterias', 'range_max')) {
                $table->dropColumn('range_max');
            }
        });
    }
};