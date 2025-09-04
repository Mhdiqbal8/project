<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturObat extends Model
{
    use HasFactory;

    protected $table = 'retur_obat';


    public function pasien(){
        return $this->belongsTo('App\Models\Pasien');
    }
}
