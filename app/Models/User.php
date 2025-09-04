<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'nik',
        'gender_id',
        'department_id',
        'status_id',
        'jabatan_id',
        'ttd_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Otomatis eager-load relasi penting biar gak null/null/null
    protected $with = ['department', 'jabatan', 'status', 'akses', 'unit'];

    // =======================
    // RELASI
    // =======================

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function akses()
    {
        return $this->belongsToMany(Akses::class, 'akses_user');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // =======================
    // FUNGSI HAK AKSES
    // =======================

    // ✅ Cek berdasarkan kode akses (misalnya: 'laporan_it', 'approve_manager')
    public function hasAccess($key)
    {
        // 🛡️ Bypass semua akses kalau role-nya 'superadmin' (Spatie Role)
        if ($this->hasRole('superadmin')) {
            return true;
        }

        return $this->akses->pluck('kode')->contains($key);
    }

    // ✅ Alias lain jika mau tetap pakai hasPrivilege()
    public function hasPrivilege($kode)
    {
        return $this->akses->pluck('kode')->contains($kode);
    }

    // ✅ SuperAdmin versi custom via tabel akses (kode 'SUPER_ADMIN')
    public function isSuperAdmin()
    {
        return $this->akses->pluck('kode')->contains('SUPER_ADMIN');
    }

    // =======================
    // HELPER DEPARTEMEN IT
    // =======================

    /**
     * Cek apakah user berasal dari Departemen IT.
     * - Sesuaikan variasi nama sesuai data di DB lu.
     * - Atau ganti ke cek ID tetap (lihat komentar).
     */
    public function isIT(): bool
    {
        $name = strtolower(optional($this->department)->nama ?? '');

        if (in_array($name, ['it', 'ti', 'information technology', 'teknologi informasi'], true)) {
            return true;
        }

        // (Opsional) kalau mau pakai ID departemen IT biar presisi:
        // return (int)$this->department_id === 1; // ganti 1 dengan ID departemen IT di DB lu

        return false;
    }
}
