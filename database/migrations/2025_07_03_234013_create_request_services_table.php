<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_services', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // ✅ PENTING
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('judul')->nullable();
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('status_id')->nullable()->default(1);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('status_id')->references('id')->on('status')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_services');
    }
};
