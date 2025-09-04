<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AksesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('akses')->upsert([
            // ==== existing ====
            ["nama_akses"=>"access_bap",             "kode"=>"access_bap",             "deskripsi"=>null],
            ["nama_akses"=>"approve_manager",        "kode"=>"approve_manager",        "deskripsi"=>null],
            ["nama_akses"=>"approve_it",             "kode"=>"approve_it",             "deskripsi"=>null],
            ["nama_akses"=>"acc_spv_bap",            "kode"=>"acc_spv_bap",            "deskripsi"=>"Akses approve Kepala Unit / SPV"],
            ["nama_akses"=>"acc_manager_bap",        "kode"=>"acc_manager_bap",        "deskripsi"=>"Akses approve Manager Unit"],
            ["nama_akses"=>"acc_final_bap",          "kode"=>"acc_final_bap",          "deskripsi"=>"Akses finalisasi oleh Unit Terkait"],
            ["nama_akses"=>"acc_spv_kronologis",     "kode"=>"acc_spv_kronologis",     "deskripsi"=>"Akses approve SPV Kronologis"],
            ["nama_akses"=>"acc_manager_kronologis", "kode"=>"acc_manager_kronologis", "deskripsi"=>"Akses approve Manager Kronologis"],
            ["nama_akses"=>"acc_final_kronologis",   "kode"=>"acc_final_kronologis",   "deskripsi"=>"Akses finalisasi Unit Terkait Kronologis"],
            ["nama_akses"=>"access_service",         "kode"=>"access_service",         "deskripsi"=>"Akses ke fitur Service"],
            ["nama_akses"=>"super_admin",            "kode"=>"super_admin",            "deskripsi"=>null],
            ["nama_akses"=>"access_request_service", "kode"=>"access_request_service", "deskripsi"=>null],
            ["nama_akses"=>"laporan_service",        "kode"=>"laporan_service",        "deskripsi"=>"Akses ke menu Laporan Service"],
            ["nama_akses"=>"ACC Mutu Form BAP",      "kode"=>"acc_mutu_bap",           "deskripsi"=>"Akses untuk ACC Mutu dan tagging unit terkait pada Form BAP"],
            ["nama_akses"=>"Laporan BAP",            "kode"=>"laporan_bap",            "deskripsi"=>"Akses ke menu Laporan BAP"],
            ["nama_akses"=>"Activity Logs",          "kode"=>"view_activity_logs",     "deskripsi"=>"Akses melihat Activity Logs"],

            // ==== NEW: E-Personalia / HR ====
            ["nama_akses"=>"Access E-Personalia",    "kode"=>"access_personalia",      "deskripsi"=>"Akses modul E-Personalia / HR"],
           ["nama_akses"=>"Manage Master Karyawan", "kode"=>"hr_employee_manage", "deskripsi"=>"CRUD Master Karyawan"],
            ["nama_akses"=>"Access Absensi",         "kode"=>"access_attendance",      "deskripsi"=>"Akses modul Absensi"],
            ["nama_akses"=>"Access Cuti/Izin",       "kode"=>"access_leave",           "deskripsi"=>"Akses modul Cuti/Izin"],
            ["nama_akses"=>"Access Payroll",         "kode"=>"access_payroll",         "deskripsi"=>"Akses modul Payroll"],
            ["nama_akses"=>"Approve Cuti SPV",       "kode"=>"approve_cuti_spv",       "deskripsi"=>"Approval cuti oleh SPV"],
            ["nama_akses"=>"Approve Cuti Manager",   "kode"=>"approve_cuti_manager",   "deskripsi"=>"Approval cuti oleh Manager"],
        ], ['kode'], ['nama_akses','deskripsi']);
    }
}
