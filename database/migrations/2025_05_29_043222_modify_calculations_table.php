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
        Schema::table('calculations', function (Blueprint $table) {
            // Hapus kolom yang tidak relevan yang sudah ada
            $table->dropColumn(['qi_add', 'qi_multi', 'final_qi']);

            // Tambahkan kolom log_id
            $table->uuid('log_id')->after('kriteria_id'); // Posisikan sesuai kebutuhan
            
            // Tambahkan foreign key
            $table->foreign('log_id')->references('log_id')->on('logs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calculations', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu sebelum menghapus kolom
            $table->dropForeign(['log_id']);
            $table->dropColumn('log_id');

            // Tambahkan kembali kolom yang dihapus di up(), jika perlu untuk rollback
            $table->decimal('qi_add', 8, 4)->nullable()->after('score_normalized');
            $table->decimal('qi_multi', 8, 4)->nullable()->after('qi_add');
            $table->decimal('final_qi', 8, 4)->nullable()->after('qi_multi');
        });
    }
};