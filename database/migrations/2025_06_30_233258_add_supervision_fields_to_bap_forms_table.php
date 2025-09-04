<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bap_forms', function (Blueprint $table) {
            $table->unsignedBigInteger('supervision_user_id')->nullable();
            $table->timestamp('supervision_approved_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('bap_forms', function (Blueprint $table) {
            $table->dropColumn(['supervision_user_id', 'supervision_approved_at']);
        });
    }
};
