@extends('layouts.app')

@section('content')

<div class="header bg-primary pb-6">
  <div class="px-4">
      <div class="header-body">
          <div class="row align-items-center py-4">
              <div class="col-lg-6 col-7">
                  <h6 class="h2 text-white d-inline-block mb-0">Tables</h6>
                  <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                      <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                          <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                          <li class="breadcrumb-item"><a href="{{ url('retur_obat') }}">Retur Obat</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Tables</li>
                      </ol>
                  </nav>
              </div>
              @if (Auth::user()->department_id == 5)
                  <div class="col-lg-6 col-5">
                      <!-- Button trigger modal -->
                      <div class="button-right">
                          <a href="{{ url('retur_obat/create') }}" class="btn btn-secondary">
                              Tambah data
                          </a>
                      </div>
                  </div>
              @endif
          </div>
      </div>
  </div>
</div>
<!-- Page content -->
<div class="container-fluid mt--6">
  <div class="row">
    <div class="col">
          <div class="card">
            @include('components.alert')
              <!-- Card header -->
              <div class="card-header border-0">
                  <h3 class="mb-0">Retur Obat</h3>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped table-bordered table-data" style="width: 100%">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nama Pasien</th>
                        <th>No. RM</th>
                        <th>Ruangan</th>
                        <td>Tanggal</td>
                        <td>Petugas Apotik/Ruangan</td>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($retur_obats as $retur_obat)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $retur_obat->nama_pasien }}</td>
                        <td>{{ $retur_obat->no_rm }}</td>
                        <td>{{ $retur_obat->ruangan }}</td>
                        <td>{{ $retur_obat->created_at->format('d-m-Y') }}</td>
                        <td>{{ $retur_obat->user->nama }}</td>
                        <td>
                          <a href="{{ url('retur_obat/show', $retur_obat->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                            </div>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
              </div>
              </div>

          </div>
      </div>
  </div>

  <!-- Footer -->
  <footer class="footer pt-0">
      <div class="row align-items-center justify-content-lg-between">
          <div class="col-lg-6">
              <div class="copyright text-center  text-lg-left  text-muted">
                  &copy; 2022 <a  href="https://www.creative-tim.com" class="font-weight-bold ml-1"
                      target="_blank">Rumah Sakit Keluarga Kita</a>
              </div>
          </div>
      </div>
  </footer>
</div>

@endsection
