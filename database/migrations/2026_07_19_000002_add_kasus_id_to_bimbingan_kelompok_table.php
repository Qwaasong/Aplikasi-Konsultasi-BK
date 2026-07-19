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
        Schema::table('bimbingan_kelompok', function (Blueprint $table) {
            $table->foreignId('kasus_id')
                ->nullable()
                ->after('id')
                ->constrained('kasus_bk')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bimbingan_kelompok', function (Blueprint $table) {
            $table->dropForeign(['kasus_id']);
            $table->dropColumn('kasus_id');
        });
    }
};
