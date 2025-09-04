<?php

namespace App\Http\Controllers;

use App\Models\BapForm;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
public function home()
{
    $data_account    = User::count();
    $total_service   = Service::count();
    $service_selesai = Service::where('status_id', 7)->count();
    $service_reject  = Service::where('status_id', 10)->count();

    $total_bap   = BapForm::count();
    $bap_reject  = BapForm::where('status', 'Ditolak')->count();

    // ✅ Tambahin hitung badge Service (belum approve SPV/Manager)
    $totalPendingService = Service::whereIn('status_id', [3,4,5])->count();

    // ✅ Tambahin hitung badge Request Service (belum selesai)
    $totalPendingRequestService = \App\Models\RequestService::whereIn('status_id', [6,7])->count();

    return view('dashboard', compact(
        'data_account',
        'total_service',
        'service_selesai',
        'service_reject',
        'total_bap',
        'bap_reject',
        'totalPendingService',
        'totalPendingRequestService'
    ));
}


    public function ubah_password(Request $request)
    {
        DB::beginTransaction();

        $id              = Auth::user()->id;
        $old_pass        = $request->password_old;
        $new_pass        = $request->password_new;
        $conf_new_pass   = $request->confirm_password_new;

        $user = User::find($id);

        // Check password lama
        if (!Hash::check($old_pass, $user->password)) {
            DB::rollBack();
            return Redirect::back()->with('failed', 'Update Your Password Failed, Password Lama Anda Salah !!!');
        }

        // Check kalau password baru sama dengan lama
        if (Hash::check($new_pass, $user->password)) {
            DB::rollBack();
            return Redirect::back()->with('failed', 'Update Your Password Failed, Tidak Dapat Menggunakan Password Lama !!!');
        }

        // Check konfirmasi password
        if ($new_pass !== $conf_new_pass) {
            DB::rollBack();
            return Redirect::back()->with('failed', 'Update Your Password Failed, Password Baru Anda Tidak Sesuai !!!');
        }

        // Update password
        $reset = DB::table('users')
            ->where('id', $id)
            ->update([
                'password' => Hash::make($new_pass)
            ]);

        if ($reset) {
            DB::commit();
            return Redirect::back()->with('success', 'Update Your Password Success !!!');
        } else {
            DB::rollBack();
            return Redirect::back()->with('failed', 'Update Your Password Failed, Silahkan Cek Kembali Data Anda !!!');
        }
    }
}
