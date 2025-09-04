<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BapStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'bap_form_id',
        'aktivitas',
        'user_id',
        'keterangan',
    ];

    public function bapForm()
    {
        return $this->belongsTo(BapForm::class, 'bap_form_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
