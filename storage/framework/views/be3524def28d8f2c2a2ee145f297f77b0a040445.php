<?php $__env->startSection('content'); ?>
<div class="header bg-primary pb-6">
  <div class="px-4">
      <div class="header-body">
          <div class="row align-items-center py-4">
              <div class="col-lg-6 col-7">
                  <h6 class="h2 text-white d-inline-block mb-0">Tables</h6>
                  <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                      <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                          <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                          <li class="breadcrumb-item"><a href="<?php echo e(url('management_inventaris')); ?>">Management Inventaris</a></li>
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
                                  <form action="<?php echo e(url('management_inventaris/store')); ?>" method="post">
                                      <?php echo csrf_field(); ?>
                                      <div class="form-group">
                                        <div class="col-md-12">
                                            <label for="">Nama Inventaris</label>
                                            <br>
                                        </div>
                                        <div class="col-md-12">
                                            <input type="text" name="nama" class="form-control" placeholder="Nama Inventaris" value="<?php echo e(old('nama')); ?>" autofocus >
                                          </div>
                                    </div>
                                      <div class="form-group">
                                        <div class="col-md-12">
                                            <label for="exampleFormControlInput1">Jenis Inventaris</label>
                                        </div>
                                        <div class="col-md-12">
                                          <?php $__currentLoopData = $jenis_inventaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jenis_inventaris): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                              <input id="jenis_inventaris_id" type="radio" name="jenis_inventaris_id" value="<?php echo e($jenis_inventaris->id); ?>" required> <?php echo e($jenis_inventaris->jenis_inventaris); ?>

                                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <label for="">No polisi</label>
                                            <br>
                                            <small class="text-warning">silahkan isi jika inventaris Motor/Mobil</small>
                                        </div>
                                        <div class="col-md-12">
                                            <input type="text" name="no_inventaris" class="form-control" placeholder="No Polisi" value="<?php echo e(old('no_invetaris')); ?>" autofocus >
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
        <?php echo $__env->make('components.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
                      <?php $__currentLoopData = $inventaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventaris): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e($inventaris->nama); ?></td>
                        <td><?php echo e($inventaris->jenis_inventaris->jenis_inventaris); ?></td>
                        <td><?php echo e($inventaris->no_inventaris); ?></td>
                        <td>
                            <div class="aksi" id="aksi">
                                <button type="button" class="btn btn-sm btn-warning" data-toggle="modal"
                                    data-target="#edit<?php echo e($inventaris->id); ?>">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <br>
                                <!-- Modal -->
                                <div class="modal fade" id="edit<?php echo e($inventaris->id); ?>" tabindex="-1"
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
                                              <form action="<?php echo e(url('management_inventaris/update', $inventaris->id)); ?>" method="post">
                                                  <?php echo method_field('PATCH'); ?>
                                                  <?php echo csrf_field(); ?>
                                                  <div class="form-group">
                                                    <div class="col-md-6">
                                                      <label for="nama">Nama Inventaris</label>
                                                    </div>
                                                    <div class="col-md-12">
                                                      <input type="text" name="nama" class="form-control" value="<?php echo e($inventaris->nama); ?>" placeholder="Nama Inventaris" required>
                                                    </div>
                                                  </div>
                                                  <div class="form-group">
                                                    <div class="col-md-6">
                                                        <label for="jenis_inventaris_id">Jenis Inventaris</label>
                                                    </div>
                                                    <div class="col-md-12">
                                                      <?php $__currentLoopData = $jenis_inventaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jenis_inventaris): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                          <?php if($inventaris->jenis_inventaris->id == $jenis_inventaris->id): ?>
                                                            <input type="radio" id="jenis_inventaris_id" name="jenis_inventaris_id" value="<?php echo e($jenis_inventaris->id); ?>" checked><?php echo e($jenis_inventaris->jenis_inventaris); ?>

                                                            <?php else: ?>
                                                            <input type="radio" id="jenis_inventaris_id" name="jenis_inventaris_id" value="<?php echo e($jenis_inventaris->id); ?>"><?php echo e($jenis_inventaris->jenis_inventaris); ?>

                                                          <?php endif; ?>
                                                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                  </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-12">
                                                        <label for="">Nomor Polisi</label>
                                                        <br>
                                                        <small class="text-warning">Di isi ketika memilih Inventaris untuk Mobil/Motor</small>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <input type="text" name="no_inventaris" class="form-control" value="<?php echo e($inventaris->no_inventaris); ?>" placeholder="Nomor Polisi">
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
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\project_form\resources\views/management_inventaris/index.blade.php ENDPATH**/ ?>