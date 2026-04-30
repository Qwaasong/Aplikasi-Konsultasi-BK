<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('konsultasi', function (Blueprint $table) {
            $table->json('files')->nullable()->after('tindak_lanjut');
        });
    }

    public function down()
    {
        Schema::table('konsultasi', function (Blueprint $table) {
            $table->dropColumn('files');
        });
    }
};