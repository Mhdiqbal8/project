<?php
// app/Http/Controllers/Hr/EmployeeProfileController.php
namespace App\Http\Controllers\Hr;


use App\Http\Controllers\Controller;
use App\Models\HrEmployeeProfile;
use Illuminate\Http\Request;


class EmployeeProfileController extends Controller
{
public function __construct(){
    $this->middleware(function($req,$next){
        $u = auth()->user();
        abort_unless($u && $u->hasPrivilege('access_personalia'), 403);
        return $next($req);
    });
}


public function index(){
    $profiles = HrEmployeeProfile::with(['unit','department','jabatan'])
        ->latest()->paginate(20);
   return view('hr.employee_profiles.index', compact('profiles'));

}

public function store(Request $r){
    // Pastikan user punya hak CRUD
    abort_unless(auth()->user()?->hasPrivilege('hr_employee_manage'), 403);

    // Terima dua kemungkinan nama field: email_kantor ATAU email
    $data = $r->validate([
        'nama_lengkap'   => 'required|string|max:255',
        'nik'            => 'required|string|max:50|unique:hr_employee_profiles,nik',
        'user_id'        => 'nullable|exists:users,id|unique:hr_employee_profiles,user_id',
        'jabatan_id'     => 'required|exists:jabatans,id',
        'department_id'  => 'required|exists:departments,id',
        'unit_id'        => 'required|exists:units,id',
        'email_kantor'   => 'nullable|email',
        'email'          => 'nullable|email', // ← alias dari form lama
        'no_hp'          => 'nullable|string|max:32',
        'tanggal_masuk'  => 'required|date',
        'status_kerja'   => 'required|in:tetap,kontrak,magang,outsource',
        'attendance_emp_code' => 'nullable|string|max:50|unique:hr_employee_profiles,attendance_emp_code',
    ]);

    // Map otomatis: pakai email_kantor kalau ada; kalau nggak, pakai email
    $data['email_kantor'] = $data['email_kantor'] ?? ($data['email'] ?? null);
    unset($data['email']); // bersihin key 'email' biar mass-assign aman

    HrEmployeeProfile::create($data);
    return back()->with('success','Profil karyawan tersimpan.');
}


}