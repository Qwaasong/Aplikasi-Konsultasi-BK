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
        Schema::create('bimbingan_individus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasus_id')->constrained('kasus_bk')->cascadeOnDelete();
            $table->foreignId('guru_bk_id')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->date('tanggal_layanan');
            $table->text('uraian_masalah');
            $table->text('penanganan');
            $table->text('tindak_lanjut')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bimbingan_individus');
    }
};
