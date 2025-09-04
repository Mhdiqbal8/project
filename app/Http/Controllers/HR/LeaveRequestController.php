<?php
namespace App\Http\Controllers\Hr;


use App\Http\Controllers\Controller;
use App\Models\HrLeaveRequest;
use Illuminate\Http\Request;


class LeaveRequestController extends Controller
{
public function __construct(){
$this->middleware('auth');
}


public function index(){
abort_unless(auth()->user()?->hasPrivilege('access_cuti_izin'), 403);
$items = HrLeaveRequest::with(['pemohon','unit','department'])->latest()->paginate(20);
return view('hr.leave_requests.index', compact('items'));
}


public function create(){
abort_unless(auth()->user()?->hasPrivilege('access_cuti_izin'), 403);
return view('hr.leave_requests.create');
}


public function store(Request $r){
abort_unless(auth()->user()?->hasPrivilege('access_cuti_izin'), 403);
$data = $r->validate([
'jenis' => 'required|string',
'tanggal_mulai' => 'required|date',
'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
'alasan' => 'nullable|string',
]);
$data['user_id'] = auth()->id();
$data['unit_id'] = auth()->user()->unit_id ?? null;
$data['department_id'] = auth()->user()->department_id ?? null;
$data['employee_id'] = null; // bisa diisi kalau sudah di‑map
$data['total_hari'] = now()->parse($data['tanggal_mulai'])->diffInDays(now()->parse($data['tanggal_selesai'])) + 1;
$data['status'] = 'diajukan';
HrLeaveRequest::create($data);
return redirect()->route('hr.leave.index')->with('success','Pengajuan cuti dibuat.');
}


public function approveSpv(HrLeaveRequest $leave){
abort_unless(auth()->user()?->hasPrivilege('approve_cuti_spv'), 403);
$leave->update(['status'=>'disetujui_spv','approved_spv_id'=>auth()->id(),'approved_spv_at'=>now()]);
return back()->with('success','Disetujui SPV.');
}


public function rejectSpv(HrLeaveRequest $leave){
abort_unless(auth()->user()?->hasPrivilege('approve_cuti_spv'), 403);
$leave->update(['status'=>'ditolak_spv','approved_spv_id'=>auth()->id(),'approved_spv_at'=>now()]);
return back()->with('success','Ditolak SPV.');
}


public function approveManager(HrLeaveRequest $leave){
abort_unless(auth()->user()?->hasPrivilege('approve_cuti_manager'), 403);
// Manager boleh langsung approve dari status diajukan atau pasca SPV
$leave->update(['status'=>'disetujui_manager','approved_manager_id'=>auth()->id(),'approved_manager_at'=>now()]);
return back()->with('success','Disetujui Manager.');
}


public function rejectManager(HrLeaveRequest $leave){
abort_unless(auth()->user()?->hasPrivilege('approve_cuti_manager'), 403);
$leave->update(['status'=>'ditolak_manager','approved_manager_id'=>auth()->id(),'approved_manager_at'=>now()]);
return back()->with('success','Ditolak Manager.');
}
}