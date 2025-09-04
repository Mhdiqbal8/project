<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class RequestService extends Model
{
    use HasFactory;

    protected $table = 'request_services';

    protected $guarded = [];

    /**
     * Relasi ke model Service
     * Satu RequestService dimiliki oleh satu Service
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relasi ke Unit Tujuan
     */
    public function unitTujuan()
    {
        return $this->belongsTo(Unit::class, 'unit_tujuan_id');
    }

    /**
     * Relasi ke Status
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function serviceModel()
{
    return $this->belongsTo(Service::class, 'service_id');
}

public function teknisi_umum()
{
    return $this->belongsTo(TeknisiUmum::class, 'teknisi_umum_id');
}


public function inventaris()
{
    return $this->belongsTo(Inventaris::class, 'inventaris_id');
}


    /**
     * (Optional) Contoh relasi ke teknisi, user, dll kalau mau dipakai nanti
     */
    
    // public function teknisi()
    // {
    //     return $this->belongsTo(User::class, 'teknisi_id');
    // }

    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'user_id');
    // }
}
