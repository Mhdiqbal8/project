<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_kerja', function (Blueprint $table) {
            $table->text('kegiatan_in')->nullable()->after('jam_in');
            $table->text('kegiatan_out')->nullable()->after('jam_out');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_kerja', function (Blueprint $table) {
            $table->dropColumn(['kegiatan_in', 'kegiatan_out']);
        });
    }
};
