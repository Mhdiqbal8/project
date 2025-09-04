<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeknisiUmum extends Model
{
    use HasFactory;

    // Tambahin baris ini kalau nama tabel lu di database adalah 'teknisi_umum'
    protected $table = 'teknisi_umum';

    protected $fillable = ['nama'];
}
