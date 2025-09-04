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
                          <li class="breadcrumb-item"><a href="#">Management Obat</a></li>
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
                                  <h5 class="modal-title" id="exampleModalLabel">Management Obat</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                  </button>
                              </div>
                              <div class="modal-body">
                                  <form action="{{ url('management_obat/store') }}" method="post">
                                      @csrf
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <label for="">Nama Obat</label>
                                        </div>
                                        <div class="col-md-12">
                                            <input type="text" name="nama_obat" class="form-control" placeholder="Nama Obat" value="{{ old('nama_obat') }}" autofocus >
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
                  <h3 class="mb-0">Management Obat</h3>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped table-bordered table-data" style="width: 100%">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nama Obat</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($obats as $obat)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $obat->nama_obat }}</td>
                        <td>
                            <div class="aksi" id="aksi">
                                <button type="button" class="btn btn-sm btn-warning" data-toggle="modal"
                                    data-target="#edit{{ $obat->id }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <br>
                                <!-- Modal -->
                                <div class="modal fade" id="edit{{ $obat->id }}" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header text-center">
                                                <h5 class="modal-title" id="exampleModalLabel">Management Obat</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                              <form action="{{ url('management_obat/update', $obat->id) }}" method="post">
                                                  @method('PATCH')
                                                  @csrf
                                                <div class="form-group">
                                                    <div class="col-md-6">
                                                        <label for="">Nama Obat</label>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <input type="text" name="nama_obat" class="form-control" value="{{ $obat->nama_obat }}" required>
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
                  &copy; 2022 <a  href="https://www.creative-tim.com" class="font-weight-bold ml-1"
                      target="_blank">Rumah Sakit Keluarga Kita</a>
              </div>
          </div>
      </div>
  </footer>
</div>

@endsection
