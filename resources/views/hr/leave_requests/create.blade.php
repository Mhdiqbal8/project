@extends('layouts.app')
@section('content')
<div class="container mt-4">
<h4 class="text-success mb-3">Ajukan Cuti / Izin</h4>
<div class="card border-0 shadow-sm p-3">
<form method="post" action="{{ route('hr.leave.store') }}">@csrf
<div class="row g-3">
<div class="col-md-4"><label class="form-label">Jenis</label>
<select name="jenis" class="form-select">
<option>Cuti Tahunan</option>
<option>Izin</option>
<option>Sakit</option>
<option>Cuti Melahirkan</option>
<option>Cuti Menikah</option>
<option>Lainnya</option>
</select>
</div>
<div class="col-md-4"><label class="form-label">Mulai</label><input type="date" name="tanggal_mulai" class="form-control" required></div>
<div class="col-md-4"><label class="form-label">Selesai</label><input type="date" name="tanggal_selesai" class="form-control" required></div>
<div class="col-12"><label class="form-label">Alasan</label><textarea name="alasan" class="form-control" rows="3"></textarea></div>
</div>
<div class="mt-3"><button class="btn btn-success">Kirim Pengajuan</button></div>
</form>
</div>
</div>
@endsection