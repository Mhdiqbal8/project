<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\BapForm;
use App\Models\User;
use App\Models\KronologisForm;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot()
    {
        $this->registerPolicies();

        // ===== Helper mini biar nggak ngulang-ngulang =====
        $isIT = function (User $user): bool {
            return
                (method_exists($user, 'isIT') && $user->isIT()) ||
                (method_exists($user, 'hasRole') && $user->hasRole('it')) ||
                strtolower(optional($user->unit)->nama ?? optional($user->unit)->nama_unit ?? '') === 'it' ||
                (method_exists($user, 'hasAccess') && $user->hasAccess('approve_it'));
        };

        $isMutu = function (User $user): bool {
            if (function_exists('isUserMutu')) return isUserMutu();
            return
                (method_exists($user, 'hasRole') && $user->hasRole('mutu')) ||
                (method_exists($user, 'hasAccess') && (
                    $user->hasAccess('acc_mutu_bap') ||
                    $user->hasAccess('approve_mutu') ||
                    $user->hasAccess('mutu') ||
                    $user->hasAccess('mutu_read')
                ));
        };

        // ===== Akses panel user mgmt =====
        Gate::define('access_user_management', function (User $user) {
            return method_exists($user, 'hasAccess') && $user->hasAccess('access_user_management');
        });

        Gate::define('laporan_service', function (User $user) {
            return method_exists($user, 'hasPrivilege') && $user->hasPrivilege('laporan_service');
        });

        Gate::define('laporan_bap', function (User $user) {
            return method_exists($user, 'hasPrivilege') && $user->hasPrivilege('laporan_bap');
        });

        // ===== NEW: lihat Activity Logs =====
        Gate::define('view-activity-logs', function (User $user) use ($isIT, $isMutu) {
            return
                (method_exists($user, 'hasRole') && $user->hasRole('admin')) ||
                $isIT($user) || $isMutu($user) ||
                (method_exists($user, 'hasAccess') && $user->hasAccess('view_activity_logs'));
        });

        // ===== E-Personalia / HR Gates =====
        Gate::define('access_personalia', function (User $user) {
            return method_exists($user, 'hasPrivilege') && $user->hasPrivilege('access_personalia');
        });

        Gate::define('access_attendance', function (User $user) {
            return method_exists($user, 'hasPrivilege') && $user->hasPrivilege('access_attendance');
        });

        Gate::define('access_leave', function (User $user) {
            return method_exists($user, 'hasPrivilege') && $user->hasPrivilege('access_leave');
        });

        Gate::define('access_payroll', function (User $user) {
            return method_exists($user, 'hasPrivilege') && $user->hasPrivilege('access_payroll');
        });

        Gate::define('approve_cuti_spv', function (User $user) {
            return method_exists($user, 'hasPrivilege') && $user->hasPrivilege('approve_cuti_spv');
        });

        Gate::define('approve_cuti_manager', function (User $user) {
            return method_exists($user, 'hasPrivilege') && $user->hasPrivilege('approve_cuti_manager');
        });

        // ===== View 1 BAP (detail/PDF) =====
        Gate::define('view-bap', function (User $user, BapForm $form) use ($isIT, $isMutu) {
            if (
                (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) ||
                $isIT($user) ||
                $isMutu($user)
            ) {
                return true;
            }

            if ($user->id === $form->user_id) return true;

            $deptUser = $user->department_id ?? null;
            $deptForm = optional($form->creator)->department_id;
            $sameDept = $deptUser && $deptForm && ($deptUser === $deptForm);

            $involved = in_array($user->id, array_filter([
                $form->kepala_unit_user_id,
                $form->supervision_user_id,
                $form->manager_user_id,
                $form->final_user_id,
                $form->it_user_id,
                $form->mutu_user_id,
            ], fn($v) => !is_null($v)), true);

            $tagged = false;
            try {
                $tagged = $form->taggedUnits()->where('units.id', $user->unit_id)->exists();
            } catch (\Throwable $e) {
                if ($form->relationLoaded('taggedUnits')) {
                    $tagged = $form->taggedUnits->pluck('id')->contains($user->unit_id);
                }
            }

            return $sameDept || $involved || $tagged;
        });

        // ===== Write Kronologis =====
        Gate::define('write-kronologis', function (User $user, BapForm $form) {
            if (!is_null($form->final_approved_at)) return false;
            if (empty($form->mutu_approved_at)) return false;

            $isMutu = (method_exists($user,'hasRole') && $user->hasRole('mutu'))
                   || (method_exists($user,'hasAccess') && (
                        $user->hasAccess('acc_mutu_bap') ||
                        $user->hasAccess('approve_mutu') ||
                        $user->hasAccess('mutu') ||
                        $user->hasAccess('mutu_read')
                      ));
            if ($isMutu) return false;

            $unitId = $user->unit_id ?? null;
            if (!$unitId) return false;

            $isTagged = false;
            try {
                $isTagged = $form->taggedUnits()->where('units.id', $unitId)->exists();
            } catch (\Throwable $e) {
                if ($form->relationLoaded('taggedUnits')) {
                    $isTagged = $form->taggedUnits->pluck('id')->contains($unitId);
                }
            }
            if (!$isTagged) return false;

            return true;
        });

        // ===== View Kronologis =====
        Gate::define('view-kronologis', function (User $user, KronologisForm $k) use ($isIT, $isMutu) {
            $k->loadMissing('creator.unit', 'creator.department', 'bapForm');

            $mutuAcc = (bool) ($k->bapForm?->mutu_approved_at);

            if ($isMutu($user)) return true;
            if ($isIT($user)) return $mutuAcc;
            if ($user->id === ($k->creator->id ?? null)) return true;

            $u = $k->creator->unit ?? null;
            if ($u) {
                if (in_array($user->id, array_filter([
                    $u->kepala_unit_id ?? null,
                    $u->supervisor_unit_id ?? null,
                    $u->manager_unit_id ?? null,
                ], fn($v)=>!is_null($v)), true)) {
                    return true;
                }
            }

            $creatorDeptId = $k->creator->department_id ?? null;
            $sameDept      = $creatorDeptId && $creatorDeptId === ($user->department_id ?? null);
            $jabatanName   = strtolower(optional($user->jabatan)->nama ?? optional($user->jabatan)->jabatan ?? '');
            $isAtasan      = str_contains($jabatanName, 'manager')
                          || str_contains($jabatanName, 'manajer')
                          || str_contains($jabatanName, 'spv')
                          || str_contains($jabatanName, 'supervision')
                          || str_contains($jabatanName, 'kepala');

            return $sameDept && $isAtasan;
        });
    }
}
