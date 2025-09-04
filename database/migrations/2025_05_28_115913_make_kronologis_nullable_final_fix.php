<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeKronologisNullableFinalFix extends Migration

{
    public function up()
    {
        Schema::table('kronologis_forms', function (Blueprint $table) {
            if (Schema::hasColumn('kronologis_forms', 'nama_pasien')) {
                $table->string('nama_pasien')->nullable()->change();
            }
            if (Schema::hasColumn('kronologis_forms', 'no_rm')) {
                $table->string('no_rm')->nullable()->change();
            }
            if (Schema::hasColumn('kronologis_forms', 'diagnosa')) {
                $table->string('diagnosa')->nullable()->change();
            }
            if (Schema::hasColumn('kronologis_forms', 'ruangan')) {
                $table->string('ruangan')->nullable()->change();
            }
            if (Schema::hasColumn('kronologis_forms', 'jenis_kelamin')) {
                $table->string('jenis_kelamin')->nullable()->change();
            }
            if (Schema::hasColumn('kronologis_forms', 'dokter_penanggung_jawab')) {
                $table->string('dokter_penanggung_jawab')->nullable()->change();
            }
            if (Schema::hasColumn('kronologis_forms', 'usia')) {
                $table->integer('usia')->nullable()->change();
            }
            if (Schema::hasColumn('kronologis_forms', 'masalah')) {
                $table->text('masalah')->nullable()->change();
            }
        });
    }

    public function down()
    {
        Schema::table('kronologis_forms', function (Blueprint $table) {
            if (Schema::hasColumn('kronologis_forms', 'nama_pasien')) {
                $table->string('nama_pasien')->nullable(false)->change();
            }
            if (Schema::hasColumn('kronologis_forms', 'no_rm')) {
                $table->string('no_rm')->nullable(false)->change();
            }
            if (Schema::hasColumn('kronologis_forms', 'diagnosa')) {
                $table->string('diagnosa')->nullable(false)->change();
            }
            if (Schema::hasColumn('kronologis_forms', 'ruangan')) {
                $table->string('ruangan')->nullable(false)->change();
            }
            if (Schema::hasColumn('kronologis_forms', 'jenis_kelamin')) {
                $table->string('jenis_kelamin')->nullable(false)->change();
            }
            if (Schema::hasColumn('kronologis_forms', 'dokter_penanggung_jawab')) {
                $table->string('dokter_penanggung_jawab')->nullable(false)->change();
            }
            if (Schema::hasColumn('kronologis_forms', 'usia')) {
                $table->integer('usia')->nullable(false)->change();
            }
            if (Schema::hasColumn('kronologis_forms', 'masalah')) {
                $table->text('masalah')->nullable(false)->change();
            }
        });
    }
}
