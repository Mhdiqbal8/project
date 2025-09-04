<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNamaToJabatansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('jabatans', function (Blueprint $table) {
        $table->string('nama')->nullable();
    });
}

public function down()
{
    Schema::table('jabatans', function (Blueprint $table) {
        $table->dropColumn('nama');
    });
}

}
