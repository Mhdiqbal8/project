<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
{
    use HasFactory;

    protected $table = 'inventaris';
    protected $fillable = [
      'nama',
      'jenis_inventaris_id',
      'no_inventaris'
    ];


    public function jenis_inventaris()
{
    return $this->belongsTo(JenisInventaris::class);
}

}
