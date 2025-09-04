<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('kronologis_forms', function (Blueprint $table) {
            if (!Schema::hasColumn('kronologis_forms', 'spv_user_id')) {
                $table->unsignedBigInteger('spv_user_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('kronologis_forms', 'spv_approved_at')) {
                $table->timestamp('spv_approved_at')->nullable()->after('spv_user_id');
            }
            if (!Schema::hasColumn('kronologis_forms', 'final_user_id')) {
                $table->unsignedBigInteger('final_user_id')->nullable()->after('manager_approved_at');
            }
            if (!Schema::hasColumn('kronologis_forms', 'final_approved_at')) {
                $table->timestamp('final_approved_at')->nullable()->after('final_user_id');
            }
        });
    }

    public function down()
    {
        Schema::table('kronologis_forms', function (Blueprint $table) {
            $table->dropColumn([
                'spv_user_id',
                'spv_approved_at',
                'final_user_id',
                'final_approved_at',
            ]);
        });
    }
};
