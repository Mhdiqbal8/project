<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_kerja_komentar', function (Blueprint $table) {
            $table->boolean('is_beres')->default(false)->after('komentar');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_kerja_komentar', function (Blueprint $table) {
            $table->dropColumn('is_beres');
        });
    }
};
