<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnlyManager
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Boleh lanjut jika Manager (jabatan_id 4) atau Super Admin
        if ($user && ($user->jabatan_id == 4 || $user->isSuperAdmin())) {
            return $next($request);
        }

        return redirect()->back()->with('error', 'Hanya Manager yang boleh akses halaman ini.');
    }
}
