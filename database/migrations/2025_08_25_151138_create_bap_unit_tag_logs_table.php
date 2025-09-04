<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('bap_unit_tag_logs')) {
            Schema::create('bap_unit_tag_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bap_form_id')->constrained('bap_forms')->onDelete('cascade');
                $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
                $table->foreignId('tagged_by')->nullable()->constrained('users')->nullOnDelete();
                // action: ADD atau REMOVE
                $table->string('action', 10);
                $table->timestamps();

                $table->index(['bap_form_id', 'created_at']);
                $table->index(['unit_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bap_unit_tag_logs');
    }
};
