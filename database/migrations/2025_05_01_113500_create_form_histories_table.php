<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('form_histories', function (Blueprint $table) {
        $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT
        $table->unsignedBigInteger('user_id'); // PENTING: harus sama tipe sama users.id
        $table->string('jenis_form');
        $table->string('judul')->nullable();
        $table->text('deskripsi')->nullable();
        $table->string('status')->default('Pending');
        $table->timestamps();
    
        // Foreign key beneran
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
    
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_histories');
    }
}
