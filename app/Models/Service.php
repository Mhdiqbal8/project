<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'service';
    protected $guarded = [];

    /**
     * Relasi ke User (Pembuat Service)
     */
   public function user()
{
    return $this->belongsTo(User::class);
}

    /**
     * Relasi ke Status Service
     */
   public function status()
{
    return $this->belongsTo(Status::class);
}


    /**
     * Akses cepat nama status
     */
   public function getStatusNameAttribute()
{
    return config("status_label.{$this->status_id}") ?? ($this->status->status ?? '-');
}


    /**
     * Relasi ke Inventaris
     */
    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id');
    }

    /**
     * Relasi ke Teknisi (yang ngerjain)
     */
    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }

    /**
     * Relasi ke keterangan service (riwayat komentar/feedback)
     */
    public function keteranganServices()
    {
        return $this->hasMany(KeteranganService::class, 'service_id');
    }

    public function unitTujuan()
{
    return $this->belongsTo(Unit::class, 'unit_tujuan_id');
}

public function keterangan_service()
{
    return $this->hasMany(KeteranganService::class, 'service_id');
}

public function unit()
{
    return $this->unitTujuan(); // alias, biar errornya hilang
}

public function teknisi_umum()
{
    return $this->belongsTo(\App\Models\TeknisiUmum::class, 'teknisi_umum_id');
}

public function pemohon()
{
    return $this->belongsTo(User::class, 'user_id'); // ✅ fix error null
}



}
