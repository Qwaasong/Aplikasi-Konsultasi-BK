<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom tambahan agar tabel komulatif_record cocok dengan spreadsheet
     * Google Forms klien (alamat/nomor WA ortu terpisah, lokasi rumah, medsos).
     */
    public function up(): void
    {
        Schema::table('komulatif_record', function (Blueprint $table) {
            $table->string('tahun_pelajaran')->nullable()->after('siswa_id');
            $table->string('alamat_ayah')->nullable()->after('telp_ortu');
            $table->string('alamat_ibu')->nullable()->after('alamat_ayah');
            $table->string('nomor_wa_ayah')->nullable()->after('alamat_ibu');
            $table->string('nomor_wa_ibu')->nullable()->after('nomor_wa_ayah');
            $table->string('lokasi_rumah')->nullable()->after('status_rumah');
            $table->string('media_sosial')->nullable()->after('kendaraan_ke_sekolah');
        });
    }

    public function down(): void
    {
        Schema::table('komulatif_record', function (Blueprint $table) {
            $table->dropColumn([
                'tahun_pelajaran',
                'alamat_ayah',
                'alamat_ibu',
                'nomor_wa_ayah',
                'nomor_wa_ibu',
                'lokasi_rumah',
                'media_sosial',
            ]);
        });
    }
};
