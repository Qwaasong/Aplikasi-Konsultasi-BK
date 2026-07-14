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
        Schema::create('bimbingan_kelompok_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bimbingan_kelompok_id')->constrained('bimbingan_kelompok')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('data_siswa')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bimbingan_kelompok_siswa');
    }
};
