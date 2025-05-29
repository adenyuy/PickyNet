<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->uuid('session_id')->nullable()->after('user_id'); // Atau setelah kolom yang sesuai
            // Jika Anda ingin ini wajib (not nullable), Anda harus mengisi nilai default untuk data yang sudah ada
            // $table->uuid('session_id')->after('user_id');
            // Jika not nullable, pastikan juga kolom ini diisi di seeder/update data yang sudah ada.
            // Atau, jalankan `php artisan migrate:fresh --seed`
            $table->foreign('session_id')->references('log_id')->on('logs')->onDelete('cascade'); // Link ke logs
        });
    }

    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->dropColumn('session_id');
        });
    }
};