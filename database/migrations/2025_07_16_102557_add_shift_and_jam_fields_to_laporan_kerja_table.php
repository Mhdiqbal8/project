<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_kerja', function (Blueprint $table) {
            $table->string('shift')->nullable()->after('tanggal');
            $table->time('jam_in_mulai')->nullable()->after('shift');
            $table->time('jam_in_selesai')->nullable()->after('jam_in_mulai');
            $table->time('jam_out_mulai')->nullable()->after('jam_in_selesai');
            $table->time('jam_out_selesai')->nullable()->after('jam_out_mulai');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_kerja', function (Blueprint $table) {
            $table->dropColumn([
                'shift',
                'jam_in_mulai',
                'jam_in_selesai',
                'jam_out_mulai',
                'jam_out_selesai',
            ]);
        });
    }
};
