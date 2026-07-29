<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gaya_belajars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('data_siswa')->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('visual')->default(0);
            $table->integer('auditori')->default(0);
            $table->integer('kinestetik')->default(0);
            $table->string('hasil', 50)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gaya_belajars');
    }
};
