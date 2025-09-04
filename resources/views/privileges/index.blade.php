@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="text-success mb-3">
        <i class="fas fa-user-shield"></i> Manajemen Privilege
    </h3>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <span><i class="fas fa-users-cog me-2"></i> Daftar User & Akses</span>
        </div>

        <div class="card-body">
            {{-- FILTER --}}
            <div class="mb-3">
                <form method="GET" class="row gy-2 gx-2 align-items-end">
                    <div class="col-md-3">
                        <label for="unit" class="form-label mb-0 small">Filter Unit</label>
                        <select name="unit" id="unit" class="form-select form-select-sm">
                            <option value="">-- Semua Unit --</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" {{ request('unit') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->nama_unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="search" class="form-label mb-0 small">Cari Nama / Email</label>
                        <input type="text" name="search" id="search" class="form-control form-control-sm"
                               value="{{ request('search') }}" placeholder="Ketik nama atau email...">
                    </div>

                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('privileges.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-sync-alt"></i> Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- TABEL --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 150px;">Nama</th>
                            <th>Email</th>
                            <th style="min-width: 120px;">Unit</th>
                            <th style="min-width: 250px;">Akses</th>
                            <th class="text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->nama }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->unit->nama_unit ?? '-' }}</td>
                            <td>
                                @if ($user->akses->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($user->akses as $akses)
                                            <span class="badge bg-{{ $akses->nama_akses === 'SUPER_ADMIN' ? 'danger' : 'primary' }}">
                                                {{ strtoupper($akses->nama_akses) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="badge bg-secondary">Tidak Ada Akses</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('privileges.edit', $user->id) }}" class="btn btn-sm btn-outline-success" title="Lihat / Edit Akses">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada user yang terdaftar.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ===== SMOOTH GREEN UI (appearance only) ===== --}}
<style>
  :root{
    --brand-50:#ecfdf5;
    --brand-100:#d1fae5;
    --brand-200:#a7f3d0;
    --brand-300:#6ee7b7;
    --brand-400:#34d399;
    --brand-500:#22c55e;
    --brand-600:#16a34a;
    --ink-700:#0f172a;
    --ink-500:#334155;
  }

  /* Header card: hijau smooth */
  .card-header.bg-success{
    background: linear-gradient(135deg, var(--brand-300), var(--brand-500)) !important;
    border: none !important;
    box-shadow: 0 3px 10px rgba(0,0,0,.05);
    font-weight: 700;
    letter-spacing: .2px;
  }

  /* Filter bar adem */
  .card-body > .mb-3{
    background: linear-gradient(135deg, var(--brand-50), #fff);
    border: 1px solid var(--brand-100);
    border-radius: 12px;
    padding: .9rem;
  }
  .form-select.form-select-sm,
  .form-control.form-control-sm{
    border-radius: 10px;
    border:1px solid rgba(0,0,0,.12);
    transition: box-shadow .15s ease, border-color .15s ease;
  }
  .form-select.form-select-sm:focus,
  .form-control.form-control-sm:focus{
    border-color: var(--brand-300);
    box-shadow: 0 0 0 .2rem rgba(52,211,153,.18);
  }

  /* Badge akses jadi chip pill halus */
  .badge{
    border-radius: 999px;
    font-weight: 700;
    padding: .4rem .6rem;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
  }
  .badge.bg-primary{
    background: linear-gradient(135deg, var(--brand-400), var(--brand-600)) !important;
    color:#fff !important;
    border:0;
  }
  /* SUPER_ADMIN: mahkota emas 👑 */
  .badge.bg-danger{
    background: linear-gradient(135deg, #f59e0b, #fbbf24) !important; /* gold */
    color:#111827 !important;
    border:0;
    position: relative;
    padding-left: 1.9rem;             /* ruang ikon */
  }
  .badge.bg-danger::before{
    content:"\f521";                   /* fa-crown */
    font-family:"Font Awesome 5 Free";
    font-weight:900;
    position:absolute; left:.6rem; top:50%; transform:translateY(-50%);
  }

  /* Tabel: sticky header + zebra + hover lembut */
  .table thead.table-light th{
    position: sticky; top: 0; z-index: 2;
    background: #f8fafc !important;
    border-bottom: 1px solid rgba(0,0,0,.08) !important;
  }
  .table tbody tr:nth-child(odd){ background-color:#ffffff; }
  .table tbody tr:nth-child(even){ background-color:#fbfdfc; }
  .table tbody tr:hover{
    background: linear-gradient(90deg, #ffffff, #f5fff9);
  }
  .table td, .table th{ vertical-align: middle; }

  /* Tombol aksi: tetap route edit, tapi ikon jadi “mata” */
  .btn-outline-success{
    border-color: var(--brand-300) !important;
    color: var(--brand-600) !important;
    border-radius: 10px;
  }
  .btn-outline-success:hover{
    background: var(--brand-50) !important;
    border-color: var(--brand-500) !important;
    color: var(--brand-700) !important;
  }
  /* ganti ikon fa-edit → fa-eye tanpa ubah markup/route */
  .btn-outline-success .fa-edit:before{
    content:"\f06e";                    /* fa-eye */
    font-family:"Font Awesome 5 Free";
    font-weight:900;
  }
</style>
@endsection
