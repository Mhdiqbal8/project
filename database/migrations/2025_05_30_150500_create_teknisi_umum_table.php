<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeknisiUmumTable extends Migration
{
    public function up()
    {
        Schema::create('teknisi_umum', function (Blueprint $table) {
            $table->id();
           $table->string('nama_teknisi_umum');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('teknisi_umum');
    }
}

