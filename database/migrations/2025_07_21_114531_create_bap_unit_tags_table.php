<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBapUnitTagsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('bap_unit_tags')) { // ⬅️ guard biar nggak bikin ulang
            Schema::create('bap_unit_tags', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bap_form_id')->constrained('bap_forms')->onDelete('cascade');
                $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('bap_unit_tags');
    }
}
