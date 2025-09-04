<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJenisInventarisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
 public function up(): void
{
    if (!Schema::hasTable('jenis_inventaris')) {
        Schema::create('jenis_inventaris', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis');
            $table->timestamps();
        });
    }
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jenis_inventaris');
    }
}
