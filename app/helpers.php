<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Cek apakah user punya akses dengan KODE tertentu (case-insensitive).
 * Aman kalau relasi akses kosong/null. Cache per-request & per-user.
 */
if (! function_exists('user_can')) {
    function user_can(string $fitur): bool
    {
        $user = Auth::user();
        if (! $user) return false;

        static $aksesCache = []; // cache per user-id
        $uid = (int) $user->id;

        if (! array_key_exists($uid, $aksesCache)) {
            try {
                $list = $user->akses()->pluck('kode')->all();
            } catch (\Throwable $e) {
                $list = ($user->akses?->pluck('kode') ?? collect())->all();
            }
            $aksesCache[$uid] = array_map('strtolower', array_map('strval', $list));
        }

        return in_array(strtolower($fitur), $aksesCache[$uid], true);
    }
}

/**
 * Cek permission Spatie dengan AMAN (tanpa melempar exception jika permission belum ada).
 */
if (! function_exists('user_has_perm')) {
    function user_has_perm(string $name): bool
    {
        $u = Auth::user();
        if (! $u) return false;

        $want = strtolower($name);

        // Kalau Spatie tersedia, ambil semua permission yang dimiliki user (tidak error)
        if (method_exists($u, 'getAllPermissions')) {
            try {
                return $u->getAllPermissions()
                         ->pluck('name')
                         ->contains(fn($n) => strtolower((string)$n) === $want);
            } catch (\Throwable $e) {
                return false;
            }
        }

        // Fallback super-aman
        return false;
    }
}

/**
 * Deteksi apakah user termasuk Mutu.
 * Bisa dari: kode akses, role, permission, atau nama unit.
 */
if (! function_exists('isUserMutu')) {
    function isUserMutu(): bool
    {
        $u = Auth::user();
        if (! $u) return false;

        $byAkses = user_can('acc_mutu_bap') || user_can('approve_mutu') || user_can('mutu');
        $byRole  = method_exists($u, 'hasRole') && $u->hasRole('mutu');
        $byPerm  = user_has_perm('approve_mutu'); // ⬅️ aman, tidak throw
        $unit    = strtolower(optional($u->unit)->nama ?? optional($u->unit)->nama_unit ?? '');
        $byUnit  = str_contains($unit, 'mutu');

        return $byAkses || $byRole || $byPerm || $byUnit;
    }
}

/**
 * Deteksi apakah user termasuk IT.
 * Bisa dari: role, permission, akses custom, atau nama unit.
 */
if (! function_exists('isUserIT')) {
    function isUserIT(): bool
    {
        $u = Auth::user();
        if (! $u) return false;

        $byRole  = method_exists($u, 'hasRole') && $u->hasRole('it');
        $byPerm  = user_has_perm('approve_it');   // ⬅️ aman
        $byAkses = user_can('approve_it');
        $unit    = strtolower(optional($u->unit)->nama ?? optional($u->unit)->nama_unit ?? '');
        $byUnit  = ($unit === 'it') || ($unit === 'ti') || str_contains($unit, 'it');

        return $byRole || $byPerm || $byAkses || $byUnit;
    }
}

/**
 * Catat aktivitas (generic) ke tabel activity_logs.
 */
if (! function_exists('activity_log')) {
    function activity_log(string $action, $subject = null, ?string $description = null, array $properties = []): void
    {
        try {
            $req = function_exists('request') ? request() : null;

            \App\Models\ActivityLog::create([
                'user_id'      => optional(Auth::user())->id,
                'action'       => $action,
                'subject_type' => $subject ? get_class($subject) : null,
                'subject_id'   => is_object($subject) && isset($subject->id)
                                    ? $subject->id
                                    : (is_numeric($subject) ? (int) $subject : null),
                'description'  => $description,
                'properties'   => $properties ?: null,
                'ip_address'   => $req?->ip(),
                'user_agent'   => $req?->userAgent(),
                'method'       => $req?->method(),
                'url'          => $req?->fullUrl(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('activity_log failed', ['error' => $e->getMessage()]);
        }
    }
}
