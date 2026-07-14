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
        Schema::create('pelanggaran_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('data_siswa')->cascadeOnDelete();
            $table->foreignId('jenis_pelanggaran_id')->nullable()->constrained('jenis_pelanggaran')->nullOnDelete();
            $table->integer('jumlah_poin')->default(0);
            $table->date('tanggal_pernyataan');
            $table->text('deskripsi_pelanggaran');
            $table->text('sanksi');
            $table->string('bukti_foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggaran_siswa');
    }
};
