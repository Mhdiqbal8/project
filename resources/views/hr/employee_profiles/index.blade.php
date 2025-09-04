@extends('layouts.app')
@section('content')
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-success mb-0">
      <i class="fas fa-id-card mr-2"></i> Master Karyawan
    </h4>
    {{-- BS4 modal trigger --}}
    <button class="btn btn-success" data-toggle="modal" data-target="#addEmp">+ Tambah</button>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- Error global (jika ada) --}}
  @if ($errors->any())
    <div class="alert alert-danger">
      <div class="mb-1 font-weight-bold">Periksa kembali isian berikut:</div>
      <ul class="mb-0">
        @foreach ($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
          <tr>
            <th>Nama</th>
            <th>NIK</th>
            <th>Unit</th>
            <th>Dept</th>
            <th>Jabatan</th>
            <th>Masuk</th>
          </tr>
        </thead>
        <tbody>
          @forelse($profiles as $p)
            <tr>
              <td>{{ $p->nama_lengkap }}</td>
              <td>{{ $p->nik }}</td>
              <td>{{ optional($p->unit)->nama_unit }}</td>
              <td>{{ optional($p->department)->nama }}</td>
              <td>{{ optional($p->jabatan)->nama }}</td>
              <td>
                @if($p->tanggal_masuk)
                  {{ \Carbon\Carbon::parse($p->tanggal_masuk)->format('d/m/Y') }}
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted">Belum ada data</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $profiles->links() }}</div>
  </div>
</div>

{{-- Modal Tambah (BS4) --}}
<div class="modal fade" id="addEmp" tabindex="-1" role="dialog" aria-labelledby="addEmpLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="post" action="{{ route('hr.employees.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="addEmpLabel">Tambah Karyawan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label">Nama</label>
            <input name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}" required>
            @error('nama_lengkap') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          <div class="row">
            <div class="col-md-6 mb-2">
              <label class="form-label">NIK</label>
              <input name="nik" class="form-control" value="{{ old('nik') }}" required>
              @error('nik') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label">Tanggal Masuk</label>
              <input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk') }}" required>
              @error('tanggal_masuk') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 mb-2">
              <label class="form-label">Unit (ID)</label>
              <input name="unit_id" class="form-control" placeholder="ID Unit" value="{{ old('unit_id') }}" required>
              @error('unit_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-4 mb-2">
              <label class="form-label">Dept (ID)</label>
              <input name="department_id" class="form-control" placeholder="ID Dept" value="{{ old('department_id') }}" required>
              @error('department_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-4 mb-2">
              <label class="form-label">Jabatan (ID)</label>
              <input name="jabatan_id" class="form-control" placeholder="ID Jabatan" value="{{ old('jabatan_id') }}" required>
              @error('jabatan_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-2">
              <label class="form-label">Email Kantor</label>
              {{-- Controller sudah memetakan "email" → "email_kantor", jadi aman --}}
              <input name="email" type="email" class="form-control" value="{{ old('email') }}">
              @error('email_kantor') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label">No HP</label>
              <input name="no_hp" class="form-control" value="{{ old('no_hp') }}">
              @error('no_hp') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-success">Simpan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
