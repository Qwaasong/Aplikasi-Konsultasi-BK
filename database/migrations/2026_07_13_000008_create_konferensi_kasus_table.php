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
        Schema::create('konferensi_kasus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasus_id')->constrained('kasus_bk')->cascadeOnDelete();
            $table->date('tanggal_konferensi');
            $table->string('uraian_masalah');
            $table->string('penanganan');
            $table->text('tindak_lanjut')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konferensi_kasus');
    }
};
