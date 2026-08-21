<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sosiometri_respons', function (Blueprint $table) {
            // Jadikan siswa_dipilih_id nullable (untuk input manual)
            $table->foreignId('siswa_dipilih_id')->nullable()->change();
            // Kolom nama teks bebas (diisi jika tidak pakai picker)
            $table->string('nama_dipilih')->nullable()->after('siswa_dipilih_id');
        });
    }

    public function down(): void
    {
        Schema::table('sosiometri_respons', function (Blueprint $table) {
            $table->foreignId('siswa_dipilih_id')->nullable(false)->change();
            $table->dropColumn('nama_dipilih');
        });
    }
};
