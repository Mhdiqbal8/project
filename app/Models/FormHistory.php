<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jenis_form',
        'judul',
        'deskripsi',
        'status',
    ];

    // Relasi user (opsional)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
