<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPrivilege
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $requiredPrivilege)
{
    if (!auth()->check() || !auth()->user()->hasPrivilege($requiredPrivilege)) {
        abort(403, 'Anda tidak punya akses.');
    }
    return $next($request);
}

}
