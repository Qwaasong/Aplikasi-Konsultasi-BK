<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akpds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('data_siswa')->cascadeOnDelete();
            $table->date('tanggal');
            $table->text('pribadi')->nullable();
            $table->text('sosial')->nullable();
            $table->text('belajar')->nullable();
            $table->text('karir')->nullable();
            $table->text('kesimpulan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akpds');
    }
};
