// database/migrations/2025_08_26_000000_create_activity_logs_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
       Schema::create('activity_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
    $table->string('action', 120);
    $table->string('subject_type')->nullable();
    $table->unsignedBigInteger('subject_id')->nullable();
    $table->text('description')->nullable();
    $table->json('properties')->nullable();

    $table->string('ip', 45)->nullable();              // ⬅️ ganti dari ip_address -> ip
    $table->text('user_agent')->nullable();            // (lebih aman dari 255)
    $table->string('method', 10)->nullable();
    $table->string('url', 2048)->nullable();

    $table->timestamps();

    $table->index(['created_at']);
    $table->index(['action']);
    $table->index(['user_id']);
    $table->index(['subject_type', 'subject_id']);
});

    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
