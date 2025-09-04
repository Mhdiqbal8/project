<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AksesUserSeeder extends Seeder
{
    public function run(): void
    {
        // Contoh: kasih 1 akses dasar ke user tertentu by email (ubah sendiri)
        $email = 'staff@rskk.local'; // ubah sesuai
        $user  = DB::table('users')->where('email', $email)->first();
        $akses = DB::table('akses')->where('kode','access_leave')->first();

        if ($user && $akses) {
            DB::table('akses_user')->updateOrInsert(
                ['user_id'=>$user->id, 'akses_id'=>$akses->id], []
            );
        }
    }
}
