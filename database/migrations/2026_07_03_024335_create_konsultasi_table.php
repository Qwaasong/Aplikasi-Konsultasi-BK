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
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('guru_bk_id')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->foreignId('kategori_id')->nullable()->constrained('kategori_konsultasi')->nullOnDelete();
            $table->string('judul');
            $table->text('isi_konsultasi');
            $table->enum('status', ['Open', 'Pending', 'Closed'])->default('Open');
            $table->enum('prioritas', ['Rendah', 'Sedang', 'Tinggi'])->default('Rendah');
            $table->date('tanggal_konsultasi');
            $table->enum('jenis_layanan', ['Individu', 'Kunjungan Rumah'])->nullable();
            $table->enum('fokus_masalah', ['Pribadi', 'Sosial', 'Belajar', 'Karir'])->nullable();
            $table->text('hasil_tindak_lanjut')->nullable();
            $table->text('tanda_tangan_siswa')->nullable();
            $table->text('tanda_tangan_guru')->nullable();
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
