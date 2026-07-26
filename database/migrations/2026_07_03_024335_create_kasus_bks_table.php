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
        Schema::create('kasus_bk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('data_siswa')->cascadeOnDelete();
            $table->foreignId('guru_bk_id')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->foreignId('kategori_id')->nullable()->constrained('kategori_kasus')->nullOnDelete();
            $table->string('penanganan');
            $table->text('uraian_masalah');
            $table->enum('status', ['Open', 'Pending', 'Closed'])->default('Open');
            $table->enum('prioritas', ['Rendah', 'Sedang', 'Tinggi'])->default('Rendah');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kasus_bk');
    }
};
