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
        Schema::create('konsultasi_lampiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konsultasi_id')->constrained()->cascadeOnDelete();
            $table->string('nama_file');
            $table->string('path_file');
            $table->string('tipe_file');
            $table->unsignedBigInteger('ukuran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konsultasi_lampiran');
    }
};
