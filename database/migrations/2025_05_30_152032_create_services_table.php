<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service', function (Blueprint $table) {
            $table->id();
            $table->string('no_ticket')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('status_id')->default(1);
            $table->unsignedBigInteger('inventaris_id');
            $table->unsignedBigInteger('teknisi_id')->nullable();
            $table->unsignedBigInteger('teknisi_umum_id')->nullable();
            $table->text('service');
            $table->bigInteger('biaya_service')->nullable();
            $table->text('keterangan')->nullable();
            $table->date('tgl_teknisi')->nullable();
            $table->string('type_permohonan')->default('0');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('status')->onDelete('cascade');
            $table->foreign('inventaris_id')->references('id')->on('inventaris')->onDelete('cascade');
            $table->foreign('teknisi_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('teknisi_umum_id')->references('id')->on('teknisi_umum')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service');
    }
}
