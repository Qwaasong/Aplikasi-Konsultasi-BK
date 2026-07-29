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
            $table->foreignId('guru_bk_id')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->date('tanggal_konferensi');
            $table->string('tempat_pertemuan')->nullable();
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
