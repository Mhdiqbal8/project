<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKronologisFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kronologis_forms', function (Blueprint $table) {
            $table->id();

            // 🔗 Hubungkan ke Form BAP
            $table->foreignId('bap_form_id')->constrained('bap_forms')->onDelete('cascade');

            // 🔗 User yang isi Kronologis
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // ✅ Data Kronologis
            $table->date('tanggal');
            $table->string('nama_pasien');
            $table->string('no_rm');
            $table->string('diagnosa');
            $table->string('ruangan');
            $table->integer('usia');
            $table->text('masalah');

            // ✅ Status & Verifikasi Unit + Manager
            $table->string('status')->default('Pending');

            $table->foreignId('unit_user_id')->nullable(); // siapa yang verifikasi (IT/Maintenance dll)
            $table->string('divisi_verifikasi')->nullable(); // Divisi yang verifikasi (IT/Maintenance)
            $table->timestamp('unit_approved_at')->nullable();

            $table->foreignId('manager_user_id')->nullable();
            $table->timestamp('manager_approved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kronologis_forms');
    }
}
