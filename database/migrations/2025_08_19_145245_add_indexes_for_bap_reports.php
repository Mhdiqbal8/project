<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bap_forms', function (Blueprint $t) {
            $t->index('created_at');
        });

        Schema::table('users', function (Blueprint $t) {
            $t->index('unit_id');
        });
    }

    public function down(): void
    {
        Schema::table('bap_forms', function (Blueprint $t) {
            $t->dropIndex(['created_at']);
        });

        Schema::table('users', function (Blueprint $t) {
            $t->dropIndex(['unit_id']);
        });
    }
};
