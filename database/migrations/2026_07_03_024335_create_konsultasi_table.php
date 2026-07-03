<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('konsultasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('data_siswa')->cascadeOnDelete();
            $table->foreignId('konselor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->foreignId('kategori_id')->constrained('kategori_konsultasi')->cascadeOnDelete();
            $table->string('judul');
            $table->string('isi_konsultasi');
            $table->enum('status', ['Menunggu', 'Diproses', 'Ditolak'])->default('Menunggu');
            $table->enum('prioritas', ['Rendah', 'Sedang', 'Tinggi'])->default('Rendah');
            $table->timestamp('tanggal_konsultasi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konsultasi');
    }
};
