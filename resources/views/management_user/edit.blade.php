@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">✏️ Edit Data User</h4>
        </div>
        <form action="{{ route('management_user.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="p-4">
            @csrf
            @method('PATCH')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control" value="{{ $user->nama }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>NIK</label>
                        <input type="text" name="nik" class="form-control" value="{{ $user->nik }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password baru (opsional)">
                    </div>
                    <div class="form-group mb-3">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="password_confirmed" class="form-control" placeholder="Ulangi password">
                    </div>
                    <div class="form-group mb-3">
                        <label>Jenis Kelamin</label>
                        <div class="d-flex flex-wrap">
                            @foreach($genders as $gender)
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="gender_id" id="gender{{ $gender->id }}" value="{{ $gender->id }}" {{ $user->gender_id == $gender->id ? 'checked' : '' }}>
                                    <label class="form-check-label" for="gender{{ $gender->id }}">{{ $gender->gender }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label>Upload TTD (opsional)</label>
                        <input type="file" name="ttd" class="form-control">
                        @if($user->ttd_path)
                            <div class="mt-2">
                                <small class="d-block text-muted">TTD saat ini:</small>
                                <img src="{{ asset('storage/' . $user->ttd_path) }}" width="120" class="border rounded shadow-sm mt-1">
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label>Department</label>
                        <select name="department_id" class="form-control" required>
                            <option value="">-- Pilih Department --</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}" {{ $user->department_id == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label>Jabatan</label>
                        <select name="jabatan_id" class="form-control" required>
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach($jabatans as $j)
                                <option value="{{ $j->id }}" {{ $user->jabatan_id == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label>Status</label>
                        <select name="status_id" class="form-control" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" {{ $user->status_id == $status->id ? 'selected' : '' }}>{{ $status->status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label>Unit</label>
                        <select name="unit_id" class="form-control" required>
                            <option value="">-- Pilih Unit --</option>
                            @foreach($units as $u)
                                <option value="{{ $u->id }}" {{ $user->unit_id == $u->id ? 'selected' : '' }}>{{ $u->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label>Kepala Unit</label>
                        <select name="kepala_unit_id" class="form-control">
                            <option value="">-- Pilih Kepala Unit --</option>
                            @foreach($allUsers as $u)
                                <option value="{{ $u->id }}" {{ optional($user->unit)->kepala_unit_id == $u->id ? 'selected' : '' }}>
                                    {{ $u->nama }} - {{ $u->unit->nama_unit ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label>Supervisor Unit</label>
                        <select name="supervisor_unit_id" class="form-control">
                            <option value="">-- Pilih Supervisor --</option>
                            @foreach($allUsers as $u)
                                <option value="{{ $u->id }}" {{ optional($user->unit)->supervisor_unit_id == $u->id ? 'selected' : '' }}>
                                    {{ $u->nama }} - {{ $u->unit->nama_unit ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label>Manajer Unit</label>
                        <select name="manager_unit_id" class="form-control">
                            <option value="">-- Pilih Manajer --</option>
                            @foreach($allUsers as $u)
                                <option value="{{ $u->id }}" {{ optional($user->unit)->manager_unit_id == $u->id ? 'selected' : '' }}>
                                    {{ $u->nama }} - {{ $u->unit->nama_unit ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- ✅ Alert jika jabatan staff tapi menjabat posisi unit --}}
            @php
                $jabatanStaffId = 1;
                $userIsStaff = $user->jabatan_id == $jabatanStaffId;

                $isKepalaUnit = $user->id == optional($user->unit)->kepala_unit_id;
                $isSupervisorUnit = $user->id == optional($user->unit)->supervisor_unit_id;
                $isManagerUnit = $user->id == optional($user->unit)->manager_unit_id;
            @endphp

            @if ($userIsStaff && ($isKepalaUnit || $isSupervisorUnit || $isManagerUnit))
                <div class="alert alert-warning mt-4">
                    ⚠️ <strong>Perhatian:</strong> User ini jabatannya masih <strong>Staff</strong>,
                    namun terdaftar sebagai:
                    <ul class="mb-0">
                        @if ($isKepalaUnit)
                            <li><strong>Kepala Unit</strong></li>
                        @endif
                        @if ($isSupervisorUnit)
                            <li><strong>Supervisor Unit</strong></li>
                        @endif
                        @if ($isManagerUnit)
                            <li><strong>Manajer Unit</strong></li>
                        @endif
                    </ul>
                    Mohon pertimbangkan untuk memperbarui jabatannya agar sesuai dengan struktur unit.
                </div>
            @endif

            <div class="text-end mt-4">
                <a href="{{ url('management_user') }}" class="btn btn-secondary">⬅️ Kembali</a>
                <button type="submit" class="btn btn-success">💾 Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
