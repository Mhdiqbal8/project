<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKerja extends Model
{
    use HasFactory;

    protected $table = 'laporan_kerja';

    protected $guarded = [];

    protected $casts = [
        'foto_bukti' => 'array',
        'approver_foto' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function komentar()
    {
        return $this->hasMany(LaporanKerjaKomentar::class);
    }

    /**
     * Accessor → cek apakah laporan ini sudah di-approve oleh user login.
     */
    public function getSudahApproveAttribute()
    {
        return $this->approver_id === auth()->id();
    }
}
