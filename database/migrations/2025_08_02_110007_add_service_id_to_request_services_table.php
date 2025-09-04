<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServiceIdToRequestServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
// database/migrations/2025_08_02_110007_add_service_id_to_request_services_table.php
public function up()
{
    Schema::table('request_services', function (Blueprint $table) {
        if (!Schema::hasColumn('request_services', 'service_id')) {
            $table->unsignedBigInteger('service_id')->nullable()->after('id');
        }
    });

    Schema::table('request_services', function (Blueprint $table) {
        try {
            $table->foreign('service_id')
                  ->references('id')->on('service') // TABEL SINGULAR
                  ->onDelete('cascade');
        } catch (\Throwable $e) {
            // kalau FK sudah ada / nama constraint beda, skip
        }
    });
}

public function down()
{
    Schema::table('request_services', function (Blueprint $table) {
        try { $table->dropForeign(['service_id']); } catch (\Throwable $e) {}
        if (Schema::hasColumn('request_services', 'service_id')) {
            $table->dropColumn('service_id');
        }
    });
}


}
