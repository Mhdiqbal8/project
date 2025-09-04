<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_kerja', function (Blueprint $table) {
            $table->text('komentar_staff')->nullable()->after('kegiatan_out');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_kerja', function (Blueprint $table) {
            $table->dropColumn('komentar_staff');
        });
    }
};
