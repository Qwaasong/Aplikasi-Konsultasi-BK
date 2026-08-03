<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akpds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('data_siswa')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('tahun_pelajaran', 20)->nullable();
            for ($i = 1; $i <= 50; $i++) {
                $table->string('q' . str_pad((string) $i, 2, '0', STR_PAD_LEFT), 3)->nullable();
            }
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akpds');
    }
};
