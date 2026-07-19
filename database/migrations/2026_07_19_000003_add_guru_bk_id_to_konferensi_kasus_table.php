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
        Schema::table('konferensi_kasus', function (Blueprint $table) {
            $table->foreignId('guru_bk_id')
                ->nullable()
                ->after('kasus_id')
                ->constrained('pegawai')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('konferensi_kasus', function (Blueprint $table) {
            $table->dropForeign(['guru_bk_id']);
            $table->dropColumn('guru_bk_id');
        });
    }
};
