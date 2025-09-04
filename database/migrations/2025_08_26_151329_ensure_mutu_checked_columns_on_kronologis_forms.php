<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // tambah FK user yang menandai, kalau belum ada
        if (!Schema::hasColumn('kronologis_forms', 'mutu_checked_by')) {
            Schema::table('kronologis_forms', function (Blueprint $table) {
                $col = $table->foreignId('mutu_checked_by')->nullable();
                // taruh setelah manager_approved_at kalau ada
                if (Schema::hasColumn('kronologis_forms','manager_approved_at')) {
                    $col->after('manager_approved_at');
                }
                $col->constrained('users')->nullOnDelete();
            });
        }

        // tambah timestamp dibaca Mutu, kalau belum ada
        if (!Schema::hasColumn('kronologis_forms', 'mutu_checked_at')) {
            Schema::table('kronologis_forms', function (Blueprint $table) {
                $col = $table->timestamp('mutu_checked_at')->nullable();
                if (Schema::hasColumn('kronologis_forms','mutu_checked_by')) {
                    $col->after('mutu_checked_by');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('kronologis_forms', 'mutu_checked_by')) {
            Schema::table('kronologis_forms', function (Blueprint $table) {
                try { $table->dropForeign(['mutu_checked_by']); } catch (\Throwable $e) {}
                $table->dropColumn('mutu_checked_by');
            });
        }
        if (Schema::hasColumn('kronologis_forms', 'mutu_checked_at')) {
            Schema::table('kronologis_forms', function (Blueprint $table) {
                $table->dropColumn('mutu_checked_at');
            });
        }
    }
};
