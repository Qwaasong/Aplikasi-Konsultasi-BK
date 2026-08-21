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
        Schema::table('gaya_belajars', function (Blueprint $table) {
            $table->json('jawaban')->nullable()->after('kinestetik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gaya_belajars', function (Blueprint $table) {
            $table->dropColumn('jawaban');
        });
    }
};
