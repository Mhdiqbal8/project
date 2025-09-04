<?php
// app/Models/HrEmployeeProfile.php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class HrEmployeeProfile extends Model
{
use SoftDeletes;
protected $table = 'hr_employee_profiles';
protected $fillable = [
'user_id','jabatan_id','department_id','unit_id','nik','nama_lengkap','email','no_hp','tanggal_masuk','status_kerja','alamat','npwp','bpjs_ketenagakerjaan','bpjs_kesehatan'
];


public function user(){ return $this->belongsTo(User::class); }
public function jabatan(){ return $this->belongsTo(Jabatan::class); }
public function department(){ return $this->belongsTo(Department::class); }
public function unit(){ return $this->belongsTo(Unit::class); }
}