<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('user_can')) {
    /**
     * Cek apakah user punya akses tertentu
     *
     * @param string $fitur kode fitur (misal: 'FINAL_BAP')
     * @return bool
     */
    function user_can($fitur) {
        return auth()->check() && auth()->user()->akses->pluck('kode')->contains($fitur);
    }
}

if (!function_exists('isUserMutu')) {
    /**
     * Cek apakah user adalah Mutu
     *
     * @return bool
     */
    function isUserMutu() {
        return auth()->check() && auth()->user()->hasRole('mutu');
    }
}
