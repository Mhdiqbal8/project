<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kronologis_forms', function (Blueprint $table) {
            // taruh setelah kolom approval manager biar rapi (ubah 'manager_approved_at' kalau beda)
            $table->timestamp('mutu_checked_at')->nullable()->after('manager_approved_at');
            // optional: index kalau sering difilter
            // $table->index('mutu_checked_at');
        });
    }

    public function down(): void
    {
        Schema::table('kronologis_forms', function (Blueprint $table) {
            $table->dropColumn('mutu_checked_at');
        });
    }
};
