<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HRGrantAdminSeeder extends Seeder
{
    public function run(): void
    {
        // TODO: ganti sesuai akun admin lo
        $adminIdentifier = [
            // salah satu saja yang cocok di proyek lo:
            'email' => 'admin@rskk.local', // <- ubah ke email admin
            // 'username' => 'admin',       // atau pakai username
            // 'id' => 1,                   // boleh id kalau yakin
        ];

        // Ambil user_id admin
        $user = DB::table('users')
            ->when(isset($adminIdentifier['email']), fn($q)=>$q->orWhere('email', $adminIdentifier['email']))
            ->when(isset($adminIdentifier['username']), fn($q)=>$q->orWhere('username', $adminIdentifier['username']))
            ->when(isset($adminIdentifier['id']), fn($q)=>$q->orWhere('id', $adminIdentifier['id']))
            ->first();

        if (!$user) {
            // kalau belum ada user adminnya, biarin aja—seeder aman dijalankan ulang
            return;
        }

        $userId = $user->id;

        // Kode akses yang mau dikasih ke admin awal
        $kodes = [
            'access_personalia',
            'access_attendance',
            'access_leave',
            'access_payroll',
            'approve_cuti_spv',
            'approve_cuti_manager',
        ];

        $aksesIds = DB::table('akses')
            ->whereIn('kode', $kodes)
            ->pluck('id')
            ->all();

        foreach ($aksesIds as $aksesId) {
            DB::table('akses_user')->updateOrInsert(
                ['user_id' => $userId, 'akses_id' => $aksesId],
                [] // no extra columns
            );
        }
    }
}
