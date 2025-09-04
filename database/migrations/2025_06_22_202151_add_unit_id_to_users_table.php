<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitIdToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_id')->nullable()->after('department_id');

            // Optional: foreign key kalau tabel units udah ada
            // $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('unit_id');

            // Kalau tadi nambah foreign key
            // $table->dropForeign(['unit_id']);
        });
    }
}
