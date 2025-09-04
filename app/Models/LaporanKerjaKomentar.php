<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKerjaKomentar extends Model
{
    use HasFactory;

    protected $table = 'laporan_kerja_komentar';

    protected $guarded = [];

    protected $casts = [
        'is_beres' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function laporan()
    {
        return $this->belongsTo(LaporanKerja::class, 'laporan_kerja_id');
    }
}
