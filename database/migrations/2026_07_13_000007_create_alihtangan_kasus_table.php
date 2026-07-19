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
        Schema::create('alihtangan_kasus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasus_id')->constrained('kasus_bk')->cascadeOnDelete();
            $table->foreignId('nama_asal')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignId('nama_penerima')->constrained('pegawai')->cascadeOnDelete();
            $table->date('tanggal_alih');
            $table->text('alasan_alih')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alihtangan_kasus');
    }
};
