<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calculations', function (Blueprint $table) {
            // Hapus foreign key lama terlebih dahulu
            $table->dropForeign(['log_id']);

            // Ubah nama kolom
            $table->renameColumn('log_id', 'session_id');

            // Tambahkan kembali foreign key baru
            $table->foreign('session_id')->references('log_id')->on('logs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('calculations', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->renameColumn('session_id', 'log_id');
            // Tambahkan kembali foreign key lama jika diperlukan untuk rollback
            $table->foreign('log_id')->references('log_id')->on('logs')->onDelete('cascade');
        });
    }
};