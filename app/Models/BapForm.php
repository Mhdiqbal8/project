<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // ⬅️ butuh untuk type-hint scopeVisibleTo()

class BapForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jenis_form',
        'judul',
        'deskripsi',
        'status',
        'tindakan_medis',
        'lain_lain',
        'permasalahan_lain',
        'kendala',
        'perbaikan',
        'divisi_verifikasi',
        'it_user_id',
        'kepala_unit_user_id',
        'kepala_unit_approved_at',
        'supervision_user_id',
        'supervision_approved_at',
        'manager_user_id',
        'manager_approved_at',
        'final_user_id',
        'final_approved_at',
        'it_approved_at',
        'mutu_user_id',
        'mutu_approved_at',
        'unit_id', // pastikan kolom ini ada di DB
    ];

    protected $casts = [
        'perbaikan'               => 'array',
        'kepala_unit_approved_at' => 'datetime',
        'supervision_approved_at' => 'datetime',
        'manager_approved_at'     => 'datetime',
        'final_approved_at'       => 'datetime',
        'it_approved_at'          => 'datetime',
        'mutu_approved_at'        => 'datetime',
    ];

    // ================================
    // 🔗 RELASI
    // ================================
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function itUser()
    {
        return $this->belongsTo(User::class, 'it_user_id');
    }

    public function kepalaUnitUser()
    {
        return $this->belongsTo(User::class, 'kepala_unit_user_id');
    }

    public function supervisionUser()
    {
        return $this->belongsTo(User::class, 'supervision_user_id');
    }

    public function managerUser()
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function finalUser()
    {
        return $this->belongsTo(User::class, 'final_user_id');
    }

    public function mutuUser()
    {
        return $this->belongsTo(User::class, 'mutu_user_id');
    }

    // Unit utama form (untuk badge/emoji/notifikasi)
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    // Kronologis terkait
    public function kronologis()
    {
        return $this->hasMany(KronologisForm::class, 'bap_form_id');
    }

    // Jabatan terkait (jika dipakai)
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    // Log status
    public function logs()
    {
        return $this->hasMany(BapStatusLog::class, 'bap_form_id');
    }

    public function latestLog()
    {
        return $this->hasOne(BapStatusLog::class, 'bap_form_id')->latestOfMany();
    }

    // Unit yang ditag Mutu
    public function taggedUnits()
{
    return $this->belongsToMany(Unit::class, 'bap_unit_tags', 'bap_form_id', 'unit_id')
                ->withPivot('tagged_by')   // <-- supaya bisa baca siapa yang nge-tag
                ->withTimestamps();
}


    // ================================
    // 🔎 SCOPES UTIL & VISIBILITAS
    // ================================
    public function scopeInPeriod($q, $start, $end)
    {
        return $q->whereBetween('created_at', [
            \Carbon\Carbon::parse($start)->startOfDay(),
            \Carbon\Carbon::parse($end)->endOfDay(),
        ]);
    }

    /** BAP yang sudah final/beres */
    public function scopeSelesai($q)
    {
        return $q->whereNotNull('final_approved_at');
    }

    /**
     * Scope visibilitas BAP (berbasis DEPARTEMEN):
     * - SuperAdmin / Mutu / IT  => bebas
     * - Selain itu               => BAP dengan creator.department_id == user.department_id
     *                               ATAU user TERLIBAT sebagai approver
     *                               (opsional) ATAU unit user ditag oleh Mutu
     */
    public function scopeVisibleTo($q, User $user)
{
    if (!$user) {
        return $q->whereRaw('1=0'); // aman: ga login -> kosong
    }

    // ===== FREE PASS (tanpa Spatie permission) =====
    $isMutuMode = (function_exists('isUserMutu') && isUserMutu())
               || (function_exists('user_can') && user_can('acc_mutu_bap'))
               || (method_exists($user, 'hasRole') && $user->hasRole('mutu'));

    $isIT = (function_exists('isUserIT') && isUserIT())
         || (method_exists($user, 'isIT') && $user->isIT())
         || (method_exists($user, 'hasRole') && $user->hasRole('it'))
         || (function_exists('user_can') && user_can('approve_it')); // ⬅️ akses custom lu

    $isSuper = (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin())
            || (method_exists($user, 'hasRole') && ($user->hasRole('super_admin') || $user->hasRole('superadmin')));

    if ($isMutuMode || $isIT || $isSuper) {
        return $q; // lihat semua
    }

    // ===== ROLE FLAGS (tanpa Spatie permission) =====
    $jab  = strtolower(optional($user->jabatan)->nama ?? '');
    $isKU = $jab === 'kepala unit';
    $isSPV = $jab === 'supervision' || $jab === 'spv';
    $isMgr = $jab === 'manager' || $jab === 'manajer';

    $unitId = $user->unit_id;
    $deptId = $user->department_id;

    return $q->where(function ($qq) use ($user, $unitId, $deptId, $isKU, $isSPV, $isMgr) {
        // ==== BASE: unit-only + keterlibatan langsung ====
        $qq
        // a) dibuat oleh dia sendiri
        ->where('user_id', $user->id)

        // b) creator 1 UNIT yang sama
        ->orWhereHas('creator', fn($qc) => $qc->where('unit_id', $unitId))

        // c) unit dia DITAG Mutu
        ->orWhereHas('taggedUnits', fn($qt) => $qt->where('units.id', $unitId))

        // d) dia sebagai approver / final
        ->orWhere('kepala_unit_user_id', $user->id)
        ->orWhere('supervision_user_id', $user->id)
        ->orWhere('manager_user_id', $user->id)
        ->orWhere('final_user_id', $user->id)
        ->orWhere('it_user_id', $user->id)
        ->orWhere('mutu_user_id', $user->id);

        // ==== Kepala Unit: unit yg DIA pimpin ====
        if ($isKU) {
            $qq->orWhereHas('creator.unit', fn($qu) => $qu->where('kepala_unit_id', $user->id))
               ->orWhereHas('taggedUnits', fn($qt) => $qt->where('units.kepala_unit_id', $user->id));
        }

        // ==== SPV & Manager: batas 1 DEPARTEMEN mereka ====
        // Kalau mau unit-only juga untuk SPV/Mgr, tinggal hapus blok ini.
        if ($isSPV || $isMgr) {
            $qq->orWhereHas('creator', fn($qc) => $qc->where('department_id', $deptId));
        }
    });
}



    public function tagLogs()
{
    return $this->hasMany(\App\Models\BapUnitTagLog::class, 'bap_form_id')->latest();
}

}
