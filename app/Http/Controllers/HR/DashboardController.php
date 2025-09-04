<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
  public function index()
{
    $today = \Carbon\Carbon::today();

    $totalKaryawan = 0;
    $cutiPending   = 0;
    $hadirHariIni  = 0;

    // Total karyawan: ambil dari HR profile; kalau belum ada, fallback users
    if (\Schema::hasTable('hr_employee_profiles')) {
        $q = \DB::table('hr_employee_profiles');
        // Hitung yang aktif jika kolom tanggal_keluar ada
        if (\Schema::hasColumn('hr_employee_profiles', 'tanggal_keluar')) {
            $q->whereNull('tanggal_keluar');
        }
        $totalKaryawan = $q->count();
    } elseif (\Schema::hasTable('users')) {
        $totalKaryawan = \DB::table('users')->count();
    }

    // Cuti pending
    if (\Schema::hasTable('hr_leave_requests')) {
        $cutiPending = \DB::table('hr_leave_requests')
            ->whereIn('status', ['diajukan', 'disetujui_spv'])
            ->count();
    }

    // Hadir hari ini: hitung distinct employee, bukan baris
    if (\Schema::hasTable('hr_attendances')) {
        $q = \DB::table('hr_attendances')->whereDate('tanggal', $today);

        // Kalau ada kolom status, hanya hitung hadir/terlambat (opsional)
        if (\Schema::hasColumn('hr_attendances', 'status')) {
            $q->whereIn('status', ['Hadir', 'Telat']);
        }

        // Kalau ada employee_id, count distinct; kalau tidak, fallback ke count baris
        $hadirHariIni = \Schema::hasColumn('hr_attendances', 'employee_id')
            ? $q->distinct('employee_id')->count('employee_id')
            : $q->count();
    }

    return view('hr.dashboard', compact('totalKaryawan','cutiPending','hadirHariIni'));
}
}
