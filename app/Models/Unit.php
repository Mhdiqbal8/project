<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\BapForm;
use App\Models\Department;

class Unit extends Model
{
    protected $table = 'units';

    protected $fillable = [
        'nama_unit',
        'kepala_unit_id',
        'supervisor_unit_id',
        'manager_unit_id',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function bapForms()
    {
        return $this->belongsToMany(BapForm::class, 'bap_unit_tags')->withTimestamps();
    }

    public function kepalaUnit()
    {
        return $this->belongsTo(User::class, 'kepala_unit_id');
    }

    public function supervisorUnit()
    {
        return $this->belongsTo(User::class, 'supervisor_unit_id');
    }

    public function managerUnit()
    {
        return $this->belongsTo(User::class, 'manager_unit_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
