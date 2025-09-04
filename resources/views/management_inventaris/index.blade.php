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
                          <li class="breadcrumb-item"><a href="{{ url('management_inventaris') }}">Management Inventaris</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Tables</li>
                      </ol>
                  </nav>
              </div>
              <div class="col-lg-6 col-5">
                  <!-- Button trigger modal -->
                  <div class="button-right">
                      <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#tambah">
                          Tambah data
                      </button>
                  </div>
                  <!-- Modal -->
                  <div class="modal fade" id="tambah" tabindex="-1" aria-labelledby="exampleModalLabel"
                      aria-hidden="true">
                      <div class="modal-dialog modal-md">
                          <div class="modal-content">
                              <div class="modal-header text-center">
                                  <h5 class="modal-title text-dark" id="exampleModalLabel">Management Inventaris</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                  </button>
                              </div>
                              <div class="modal-body">
                                  <form action="{{ url('management_inventaris/store') }}" method="post">
                                      @csrf
                                      <div class="form-group">
                                        <div class="col-md-12">
                                            <label for="">Nama Inventaris</label>
                                            <br>
                                        </div>
                                        <div class="col-md-12">
                                            <input type="text" name="nama" class="form-control" placeholder="Nama Inventaris" value="{{ old('nama') }}" autofocus >
                                          </div>
                                    </div>
                                      <div class="form-group">
                                        <div class="col-md-12">
                                            <label for="exampleFormControlInput1">Jenis Inventaris</label>
                                        </div>
                                        <div class="col-md-12">
                                          @foreach ($jenis_inventaries as $jenis_inventaris)
                                              <input id="jenis_inventaris_id" type="radio" name="jenis_inventaris_id" value="{{ $jenis_inventaris->id }}" required> {{ $jenis_inventaris->jenis_inventaris }}
                                          @endforeach
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <label for="">No polisi</label>
                                            <br>
                                            <small class="text-warning">silahkan isi jika inventaris Motor/Mobil</small>
                                        </div>
                                        <div class="col-md-12">
                                            <input type="text" name="no_inventaris" class="form-control" placeholder="No Polisi" value="{{ old('no_invetaris') }}" autofocus >
                                          </div>
                                    </div>
                                      <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary"
                                              data-dismiss="modal">Close</button>
                                          <button type="submit" class="btn btn-primary" onclick="return confirm('Apa Anda Yakin Dengan Data Anda?')">Simpan</button>
                                      </div>
                                  </form>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>
<!-- Page content -->
<div class="container-fluid mt--6">
  <div class="row">
    <div class="col">
        @include('components.alert')
          <div class="card">
              <!-- Card header -->
              <div class="card-header border-0">
                  <h3 class="mb-0 text-dark">Management Inventaris</h3>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped table-bordered table-data" style="width: 100%">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nama Inventaris</th>
                        <th>Jenis Inventaris</th>
                        <th>Nomor Polisi/Nomor Serial</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($inventaries as $inventaris)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $inventaris->nama }}</td>
                        <td>{{ $inventaris->jenis_inventaris->jenis_inventaris }}</td>
                        <td>{{ $inventaris->no_inventaris }}</td>
                        <td>
                            <div class="aksi" id="aksi">
                                <button type="button" class="btn btn-sm btn-warning" data-toggle="modal"
                                    data-target="#edit{{ $inventaris->id }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <br>
                                <!-- Modal -->
                                <div class="modal fade" id="edit{{ $inventaris->id }}" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header text-center">
                                                <h5 class="modal-title" id="exampleModalLabel">Management Inventaris</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                              <form action="{{ url('management_inventaris/update', $inventaris->id) }}" method="post">
                                                  @method('PATCH')
                                                  @csrf
                                                  <div class="form-group">
                                                    <div class="col-md-6">
                                                      <label for="nama">Nama Inventaris</label>
                                                    </div>
                                                    <div class="col-md-12">
                                                      <input type="text" name="nama" class="form-control" value="{{ $inventaris->nama }}" placeholder="Nama Inventaris" required>
                                                    </div>
                                                  </div>
                                                  <div class="form-group">
                                                    <div class="col-md-6">
                                                        <label for="jenis_inventaris_id">Jenis Inventaris</label>
                                                    </div>
                                                    <div class="col-md-12">
                                                      @foreach ($jenis_inventaries as $jenis_inventaris)
                                                          @if ($inventaris->jenis_inventaris->id == $jenis_inventaris->id)
                                                            <input type="radio" id="jenis_inventaris_id" name="jenis_inventaris_id" value="{{ $jenis_inventaris->id }}" checked>{{ $jenis_inventaris->jenis_inventaris }}
                                                            @else
                                                            <input type="radio" id="jenis_inventaris_id" name="jenis_inventaris_id" value="{{ $jenis_inventaris->id }}">{{ $jenis_inventaris->jenis_inventaris }}
                                                          @endif
                                                      @endforeach
                                                  </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-12">
                                                        <label for="">Nomor Polisi</label>
                                                        <br>
                                                        <small class="text-warning">Di isi ketika memilih Inventaris untuk Mobil/Motor</small>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <input type="text" name="no_inventaris" class="form-control" value="{{ $inventaris->no_inventaris }}" placeholder="Nomor Polisi">
                                                    </div>
                                                </div>
                                              </div>
                                                  <div class="modal-footer">
                                                      <button type="button" class="btn btn-secondary"
                                                          data-dismiss="modal">Close</button>
                                                      <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda Yakin Dengan Data Anda?')">Save changes</button>
                                                  </div>
                                              </form>
                                          </div>
                                        </div>
                                    </div>
                                </div>
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
                  &copy; 2022 <a  href="https://keluarga-kita.com" class="font-weight-bold ml-1"
                      target="_blank">Rumah Sakit Keluarga Kita - Developer TIM IT RSKK</a>
              </div>
          </div>
      </div>
  </footer>
</div>

@endsection
