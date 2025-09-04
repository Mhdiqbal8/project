<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nama')) {
                $table->string('nama')->nullable()->after('id');
            }
            if (!Schema::hasColumn('users', 'nik')) {
                $table->string('nik')->nullable()->after('username');
            }
            if (!Schema::hasColumn('users', 'gender_id')) {
                $table->unsignedBigInteger('gender_id')->nullable()->after('nik');
            }
            if (!Schema::hasColumn('users', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('gender_id');
            }
            if (!Schema::hasColumn('users', 'jabatan_id')) {
                $table->unsignedBigInteger('jabatan_id')->nullable()->after('department_id');
            }
            if (!Schema::hasColumn('users', 'status_id')) {
                $table->unsignedBigInteger('status_id')->nullable()->after('jabatan_id');
            }
            if (!Schema::hasColumn('users', 'ttd_path')) {
                $table->string('ttd_path')->nullable()->after('status_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nama',
                'nik',
                'gender_id',
                'department_id',
                'jabatan_id',
                'status_id',
                'ttd_path'
            ]);
        });
    }
};

