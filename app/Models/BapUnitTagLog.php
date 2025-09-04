<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BapUnitTagLog extends Model
{
    use HasFactory;

    // (opsional) kalau nama tabel default sudah "bap_unit_tag_logs" ga perlu ini
    // protected $table = 'bap_unit_tag_logs';

    // biar aman dari mass assignment
    protected $fillable = [
        'bap_form_id',
        'unit_id',
        'tagged_by',
        'action',     // 'ADD' | 'REMOVE'
        'tagged_at',  // kalau kolom ini ada di migration lu
        'notes',      // opsional
    ];

    // Constants biar konsisten
    public const ACTION_ADD    = 'ADD';
    public const ACTION_REMOVE = 'REMOVE';

    protected $casts = [
        'tagged_at'  => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** RELATIONS */
    public function bapForm()
    {
        return $this->belongsTo(BapForm::class, 'bap_form_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function actor() // atau ganti nama ke tagger()
    {
        return $this->belongsTo(User::class, 'tagged_by');
    }

    /** SCOPES (buat gampangin query) */
    public function scopeForForm($q, $formId)
    {
        return $q->where('bap_form_id', $formId);
    }

    public function scopeForUnit($q, $unitId)
    {
        return $q->where('unit_id', $unitId);
    }
}
