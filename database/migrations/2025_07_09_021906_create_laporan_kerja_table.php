<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporan_kerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_out')->nullable();
            $table->time('jam_in')->nullable();
            $table->text('kegiatan_in')->nullable();         
            $table->text('kegiatan_out')->nullable();        
            $table->text('komentar_staff')->nullable();      
            $table->longText('ttd')->nullable();
            $table->json('foto_bukti')->nullable();
            $table->foreignId('status_id')->constrained('status')->onDelete('cascade');
            $table->foreignId('approver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('approver_foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_kerja');
    }
};
