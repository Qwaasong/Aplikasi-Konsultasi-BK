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
        Schema::create('konferensi_kasus_peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konferensi_kasus_id')->constrained('konferensi_kasus')->cascadeOnDelete();
            $table->string('nama_peserta');
            $table->string('peran_peserta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konferensi_kasus_peserta');
    }
};
