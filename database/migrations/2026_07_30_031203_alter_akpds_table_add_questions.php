<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('akpds', function (Blueprint $table) {
            $table->dropColumn(['pribadi', 'sosial', 'belajar', 'karir', 'kesimpulan', 'catatan']);
            $table->string('tahun_pelajaran', 20)->nullable()->after('tanggal');
            for ($i = 1; $i <= 50; $i++) {
                $col = 'q' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
                $table->string($col, 3)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('akpds', function (Blueprint $table) {
            $table->dropColumn('tahun_pelajaran');
            for ($i = 1; $i <= 50; $i++) {
                $table->dropColumn('q' . str_pad((string) $i, 2, '0', STR_PAD_LEFT));
            }
            $table->text('pribadi')->nullable();
            $table->text('sosial')->nullable();
            $table->text('belajar')->nullable();
            $table->text('karir')->nullable();
            $table->text('kesimpulan')->nullable();
            $table->text('catatan')->nullable();
        });
    }
};
