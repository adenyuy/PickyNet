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
        Schema::table('users', function (Blueprint $table) {
            // Pastikan kolom ini belum ada, atau gunakan after() jika perlu
            $table->string('google_id')->nullable()->after('password');
            $table->string('avatar')->nullable()->after('google_id');
            // Ubah kolom username dan password menjadi nullable
            $table->string('username')->nullable()->change();
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'avatar']);
            // Kembalikan kolom username dan password menjadi tidak nullable jika diperlukan
            $table->string('username')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
