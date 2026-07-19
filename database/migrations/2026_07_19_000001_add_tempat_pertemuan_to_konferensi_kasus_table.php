<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('konferensi_kasus', function (Blueprint $table) {
            $table->string('tempat_pertemuan')->nullable()->after('tindak_lanjut');
        });
    }

    public function down(): void
    {
        Schema::table('konferensi_kasus', function (Blueprint $table) {
            $table->dropColumn('tempat_pertemuan');
        });
    }
};
