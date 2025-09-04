<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KronologisForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'bap_form_id',
        'user_id',

        // meta kronologis
        'tipe_kronologis',           // 'Medis' | 'Non-Medis'
        'judul',
        'deskripsi',
        'tanggal',

        // Medis
        'nama_pasien',
        'no_rm',
        'diagnosa',
        'ruangan',
        'usia',
        'jenis_kelamin',
        'dokter_penanggung_jawab',
        'masalah',

        // status/approval
        'status',
        'spv_user_id',
        'spv_approved_at',
        'manager_user_id',
        'manager_approved_at',
        'final_user_id',
        'final_approved_at',

        // optional routing
        'divisi_verifikasi',

        // >>> penanda baca oleh Mutu
        'mutu_checked_by',
        'mutu_checked_at',
    ];

    protected $casts = [
        'tanggal'             => 'date',
        'spv_approved_at'     => 'datetime',
        'manager_approved_at' => 'datetime',
        'final_approved_at'   => 'datetime',
        'mutu_checked_at'     => 'datetime',
    ];

    /** ========== RELATIONS ========== */

    // Pembuat
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // SPV
    public function spvUser()
    {
        return $this->belongsTo(User::class, 'spv_user_id');
    }

    // Manager
    public function managerUser()
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    // Final Approver (Unit Terkait)
    public function finalUser()
    {
        return $this->belongsTo(User::class, 'final_user_id');
    }

    // Ke Form BAP
    public function bapForm()
    {
        return $this->belongsTo(BapForm::class, 'bap_form_id');
    }

    // (Opsional) IT user jika dipakai
    public function itUser()
    {
        return $this->belongsTo(User::class, 'it_user_id');
    }

    // Alias final_user sebagai Unit Terkait (kalau dipakai)
    public function unitUser()
    {
        return $this->belongsTo(User::class, 'final_user_id');
    }

    // >>> Mutu yang menandai "sudah dibaca"
    public function mutuChecker()
    {
        return $this->belongsTo(User::class, 'mutu_checked_by');
    }
}
