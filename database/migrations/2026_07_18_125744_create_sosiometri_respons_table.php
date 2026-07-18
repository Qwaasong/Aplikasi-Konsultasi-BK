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
        Schema::create('sosiometri_respons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sosiometri_id')->constrained('sosiometri')->cascadeOnDelete();
            $table->foreignId('siswa_dipilih_id')->constrained('data_siswa')->cascadeOnDelete();
            $table->foreignId('siswa_pemilih_id')->constrained('data_siswa')->cascadeOnDelete();
            $table->integer('urutan');
            $table->text('alasan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sosiometri_respons');
    }
};
