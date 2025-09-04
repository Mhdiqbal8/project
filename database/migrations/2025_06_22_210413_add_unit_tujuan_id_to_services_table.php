<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitTujuanIdToServicesTable_Old extends Migration // ✅ Tambahin _Old di belakang
{
    public function up()
    {
        Schema::table('service', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_tujuan_id')->nullable()->after('status_id');
            $table->foreign('unit_tujuan_id')->references('id')->on('units')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('service', function (Blueprint $table) {
            $table->dropForeign(['unit_tujuan_id']);
            $table->dropColumn('unit_tujuan_id');
        });
    }
}
