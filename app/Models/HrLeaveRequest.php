<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class HrLeaveRequest extends Model
{
use SoftDeletes;
protected $table = 'hr_leave_requests';
protected $fillable = [
'user_id','employee_id','unit_id','department_id','jenis','tanggal_mulai','tanggal_selesai','total_hari','alasan','status','approved_spv_id','approved_spv_at','approved_manager_id','approved_manager_at'
];


protected $casts = [
'approved_spv_at' => 'datetime',
'approved_manager_at' => 'datetime',
'tanggal_mulai' => 'date',
'tanggal_selesai' => 'date',
];


public function pemohon(){ return $this->belongsTo(User::class, 'user_id'); }
public function employee(){ return $this->belongsTo(HrEmployeeProfile::class, 'employee_id'); }
public function unit(){ return $this->belongsTo(Unit::class); }
public function department(){ return $this->belongsTo(Department::class); }
public function spv(){ return $this->belongsTo(User::class, 'approved_spv_id'); }
public function manager(){ return $this->belongsTo(User::class, 'approved_manager_id'); }
}