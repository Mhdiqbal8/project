<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeteranganService extends Model
{
    use HasFactory;

    protected $table = 'keterangan_service';

    protected $fillable = [
        'service_id',
        'keterangan',
        'user_id',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
