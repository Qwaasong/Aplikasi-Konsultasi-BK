<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('nis')->unique();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->text('alamat')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->integer('anak_ke')->nullable();
            $table->integer('jml_saudara')->nullable();
            $table->string('asal_smp')->nullable();
            $table->string('agama')->nullable();
            $table->string('hobi')->nullable();
            $table->string('bakat')->nullable();
            $table->enum('rencana_lulus', ['Bekerja', 'Kuliah', 'Menikah'])->nullable();
            $table->text('detail_rencana_lulus')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
