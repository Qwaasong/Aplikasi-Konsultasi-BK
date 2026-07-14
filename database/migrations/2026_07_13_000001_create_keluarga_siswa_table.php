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
        Schema::create('keluarga_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('data_siswa')->cascadeOnDelete();
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('pendidikan_ayah')->nullable();
            $table->string('pendidikan_ibu')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('telp_ortu')->nullable();
            $table->string('status_rumah')->nullable();
            $table->string('dinding_rumah')->nullable();
            $table->string('lantai_rumah')->nullable();
            $table->integer('jml_kamar')->nullable();
            $table->boolean('punya_kamar_sendiri')->default(false);
            $table->integer('jml_tv')->nullable();
            $table->integer('kendaraan_mobil')->nullable();
            $table->integer('kendaraan_motor')->nullable();
            $table->string('biaya_sekolah_dari')->nullable();
            $table->string('kendaraan_ke_sekolah')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keluarga_siswa');
    }
};
