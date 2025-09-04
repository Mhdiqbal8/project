<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusTable extends Migration
{
    public function up()
    {
        Schema::create('status', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->timestamps();
        });

        // Optional: Insert default status (isi langsung)
        DB::table('status')->insert([
            ['status' => 'Active'],
            ['status' => 'In Active'],
            ['status' => 'Menunggu Persetujuan'],
            ['status' => 'Disetujui SPV'],
            ['status' => 'Menunggu Tindakan Finance'],
            ['status' => 'On Progress'],
            ['status' => 'Completed'],
            ['status' => 'Closed'],
            ['status' => 'Rejected'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('status');
    }
}
