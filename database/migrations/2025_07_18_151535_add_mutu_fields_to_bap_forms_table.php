<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMutuFieldsToBapFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('bap_forms', function (Blueprint $table) {
        $table->foreignId('mutu_user_id')->nullable()->after('manager_user_id')->constrained('users')->nullOnDelete();
        $table->timestamp('mutu_approved_at')->nullable()->after('manager_approved_at');
    });
}

public function down()
{
    Schema::table('bap_forms', function (Blueprint $table) {
        $table->dropForeign(['mutu_user_id']);
        $table->dropColumn(['mutu_user_id', 'mutu_approved_at']);
    });
}

}
