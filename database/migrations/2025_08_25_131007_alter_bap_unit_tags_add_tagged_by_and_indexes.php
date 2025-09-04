<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bap_unit_tags', function (Blueprint $table) {
            if (!Schema::hasColumn('bap_unit_tags', 'tagged_by')) {
                $table->foreignId('tagged_by')
                      ->nullable()
                      ->constrained('users')
                      ->nullOnDelete()
                      ->after('unit_id');
            }

            // cegah duplikat tag per form
            $table->unique(['bap_form_id','unit_id'], 'bap_unit_tags_unique');

            // index bantu performa utk query by unit
            $table->index(['unit_id','bap_form_id'], 'bap_unit_tags_unit_form_idx');
        });
    }

    public function down(): void
    {
        Schema::table('bap_unit_tags', function (Blueprint $table) {
            if (Schema::hasColumn('bap_unit_tags','tagged_by')) {
                $table->dropForeign(['tagged_by']);
                $table->dropColumn('tagged_by');
            }
            $table->dropUnique('bap_unit_tags_unique');
            $table->dropIndex('bap_unit_tags_unit_form_idx');
        });
    }
};
