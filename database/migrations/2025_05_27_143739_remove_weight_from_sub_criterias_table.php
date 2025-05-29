<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sub_criterias', function (Blueprint $table) {
            $table->dropColumn('weight');
        });
    }

    public function down(): void
    {
        Schema::table('sub_criterias', function (Blueprint $table) {
            // Jika ingin bisa rollback, definisikan ulang kolom weight di sini
            // Atau biarkan kosong jika Anda tidak berencana rollback migrasi ini
            $table->decimal('weight', 5, 2)->nullable(); // Tambahkan nullable jika memungkinkan
        });
    }
};