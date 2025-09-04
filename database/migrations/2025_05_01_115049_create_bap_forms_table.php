<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBapFormsTable extends Migration
{
    public function up()
    {
        Schema::create('bap_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('jenis_form')->nullable();
            $table->string('judul')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('status')->default('Pending');

            // Tindakan & masalah
            $table->json('perbaikan')->nullable();
            $table->text('tindakan_medis')->nullable();
            $table->text('lain_lain')->nullable();
            $table->text('permasalahan_lain')->nullable();
            $table->text('kendala')->nullable();

            // Approver
            $table->foreignId('it_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('manager_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('spv_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('final_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('spv_approved_at')->nullable();
            $table->timestamp('it_approved_at')->nullable();
            $table->timestamp('manager_approved_at')->nullable();
            $table->timestamp('final_approved_at')->nullable();

            $table->string('divisi_verifikasi')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bap_forms');
    }
}
