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
        Schema::create('bimbingan_kelompok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasus_id')->nullable()->constrained('kasus_bk')->nullOnDelete();
            $table->date('tanggal_layanan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bimbingan_kelompok');
    }
};
