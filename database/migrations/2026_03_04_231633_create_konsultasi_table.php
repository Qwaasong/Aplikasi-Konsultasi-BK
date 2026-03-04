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
            $table->timestamp('tanggal');
            $table->foreignId('id_siswa')->constrained('data_siswa')->onDelete('cascade');
            $table->string('jenis_layanan');
            $table->text('deskripsi_masalah');
            $table->text('hasil_layanan');
            $table->text('tindak_lanjut');
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->string('file')->nullable();
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
