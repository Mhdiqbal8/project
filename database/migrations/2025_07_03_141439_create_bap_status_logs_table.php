<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bap_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bap_form_id')->constrained()->onDelete('cascade');
            $table->string('aktivitas');         // misalnya: Dibuat, Disetujui Unit, Selesai
            $table->foreignId('user_id')->constrained('users');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bap_status_logs');
    }
};
