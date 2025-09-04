<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGenderTable extends Migration
{
    public function up(): void
{
    Schema::create('genders', function (Blueprint $table) {
        $table->id();
        $table->string('gender');
        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('gender');
    }
}
