<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bap_forms', function (Blueprint $table) {
            if (!Schema::hasColumn('bap_forms', 'kepala_unit_user_id')) {
                $table->unsignedBigInteger('kepala_unit_user_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('bap_forms', 'kepala_unit_approved_at')) {
                $table->timestamp('kepala_unit_approved_at')->nullable()->after('kepala_unit_user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bap_forms', function (Blueprint $table) {
            $table->dropColumn([
                'kepala_unit_user_id',
                'kepala_unit_approved_at',
            ]);
        });
    }
};
