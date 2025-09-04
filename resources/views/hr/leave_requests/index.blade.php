@extends('layouts.app')
@section('content')
<div class="container mt-4">
<div class="d-flex justify-content-between align-items-center mb-3">
<h4 class="text-success mb-0"><i class="fas fa-plane-departure me-2"></i> Cuti / Izin</h4>
<a href="{{ route('hr.leave.create') }}" class="btn btn-success">Ajukan Cuti</a>
</div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif


<div class="card border-0 shadow-sm">
<div class="table-responsive">
<table class="table table-hover align-middle mb-0">
<thead class="bg-light"><tr>
<th>Nama</th><th>Jenis</th><th>Tanggal</th><th>Total</th><th>Status</th><th class="text-end">Aksi</th>
</tr></thead>
<tbody>
@forelse($items as $i)
<tr>
<td>{{ optional($i->pemohon)->nama }}</td>
<td>{{ $i->jenis }}</td>
<td>{{ $i->tanggal_mulai }} → {{ $i->tanggal_selesai }}</td>
<td>{{ $i->total_hari }} hari</td>
<td><span class="badge bg-secondary">{{ $i->status }}</span></td>
<td class="text-end">
@if(auth()->user()->hasPrivilege('approve_cuti_spv'))
<form class="d-inline" method="post" action="{{ route('hr.leave.approve_spv',$i) }}">@csrf<button class="btn btn-sm btn-outline-success">Approve SPV</button></form>
<form class="d-inline" method="post" action="{{ route('hr.leave.reject_spv',$i) }}">@csrf<button class="btn btn-sm btn-outline-danger">Tolak SPV</button></form>
@endif
@if(auth()->user()->hasPrivilege('approve_cuti_manager'))
<form class="d-inline" method="post" action="{{ route('hr.leave.approve_manager',$i) }}">@csrf<button class="btn btn-sm btn-success">Approve Manager</button></form>
<form class="d-inline" method="post" action="{{ route('hr.leave.reject_manager',$i) }}">@csrf<button class="btn btn-sm btn-danger">Tolak Manager</button></form>
@endif
</td>
</tr>
@empty
<tr><td colspan="6" class="text-center text-muted">Belum ada pengajuan</td></tr>
@endforelse
</tbody>
</table>
</div>
<div class="card-footer">{{ $items->links() }}</div>
</div>
</div>
@endsection