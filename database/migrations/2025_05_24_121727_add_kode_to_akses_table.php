<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKodeToAksesTable extends Migration
{
    public function up(): void
    {
        Schema::table('akses', function (Blueprint $table) {
            $table->string('kode')->unique()->after('nama_akses');
        });
    }

    public function down(): void
    {
        Schema::table('akses', function (Blueprint $table) {
            $table->dropColumn('kode');
        });
    }
}
