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
        Schema::create('sosiometri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemilih_siswa_id')->constrained('data_siswa')->cascadeOnDelete();
            $table->foreignId('pilihan1_siswa_id')->constrained('data_siswa')->cascadeOnDelete();
            $table->text('alasan_1')->nullable();
            $table->foreignId('pilihan2_siswa_id')->constrained('data_siswa')->cascadeOnDelete();
            $table->text('alasan_2')->nullable();
            $table->foreignId('pilihan3_siswa_id')->constrained('data_siswa')->cascadeOnDelete();
            $table->text('alasan_3')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sosiometri');
    }
};
