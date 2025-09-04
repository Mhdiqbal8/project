<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Service;
use App\Models\RequestService;
use App\Models\BapForm;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
{
    // 🕐 Set lokal dan zona waktu
    \Carbon\Carbon::setLocale('id');
    date_default_timezone_set('Asia/Jakarta');

    // ✅ Inject global view composer (navbar, dsb)
    \Illuminate\Support\Facades\View::composer('*', function ($view) {
        $totalPendingRequestService = 0;
        $totalPendingService = 0;
        $totalPendingBap = 0;
        $notifications = collect();
        $unreadNotifications = collect();

        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = auth()->user();
            $user->load('akses');

            $userUnitId = $user->unit_id ?? null;

            // 🎯 Hitung Pending SERVICE (akses dari unit pembuat)
            if ($user->hasAccess('access_service')) {
                $totalPendingService = \App\Models\Service::whereHas('user', function ($q) use ($userUnitId) {
                    $q->where('unit_id', $userUnitId);
                })->whereIn('status_id', [3, 4, 5])->count();
            }

            // 🎯 Hitung Pending REQUEST SERVICE (akses dari unit tujuan)
            if ($user->hasAccess('access_request_service')) {
                $totalPendingRequestService = \App\Models\RequestService::whereIn('status_id', [6, 7])
                    ->whereHas('service.user', function ($q) use ($userUnitId) {
                        $q->where('unit_id', $userUnitId);
                    })->count();
            }

            // 🎯 Hitung Pending BAP
            if ($user->hasAccess('access_bap')) {
                $totalPendingBap = \App\Models\BapForm::where('status', '!=', 'Selesai')->count();
            }

            $notifications = $user->notifications()->latest()->take(10)->get();
            $unreadNotifications = $user->unreadNotifications;
        }

        $view->with([
            'totalPendingRequestService' => $totalPendingRequestService,
            'totalPendingService' => $totalPendingService,
            'totalPendingBap' => $totalPendingBap,
            'userNotifications' => $notifications,
            'unreadNotifications' => $unreadNotifications,
        ]);
    });
}

}
