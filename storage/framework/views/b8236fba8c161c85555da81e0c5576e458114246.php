
<?php $__env->startSection('content'); ?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-success mb-0">
      <i class="fas fa-id-card mr-2"></i> Master Karyawan
    </h4>
    
    <button class="btn btn-success" data-toggle="modal" data-target="#addEmp">+ Tambah</button>
  </div>

  <?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  
  <?php if($errors->any()): ?>
    <div class="alert alert-danger">
      <div class="mb-1 font-weight-bold">Periksa kembali isian berikut:</div>
      <ul class="mb-0">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><?php echo e($err); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  <?php endif; ?>

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
          <?php $__empty_1 = true; $__currentLoopData = $profiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($p->nama_lengkap); ?></td>
              <td><?php echo e($p->nik); ?></td>
              <td><?php echo e(optional($p->unit)->nama_unit); ?></td>
              <td><?php echo e(optional($p->department)->nama); ?></td>
              <td><?php echo e(optional($p->jabatan)->nama); ?></td>
              <td>
                <?php if($p->tanggal_masuk): ?>
                  <?php echo e(\Carbon\Carbon::parse($p->tanggal_masuk)->format('d/m/Y')); ?>

                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="6" class="text-center text-muted">Belum ada data</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <div class="card-footer"><?php echo e($profiles->links()); ?></div>
  </div>
</div>


<div class="modal fade" id="addEmp" tabindex="-1" role="dialog" aria-labelledby="addEmpLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="post" action="<?php echo e(route('hr.employees.store')); ?>">
        <?php echo csrf_field(); ?>
        <div class="modal-header">
          <h5 class="modal-title" id="addEmpLabel">Tambah Karyawan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label">Nama</label>
            <input name="nama_lengkap" class="form-control" value="<?php echo e(old('nama_lengkap')); ?>" required>
            <?php $__errorArgs = ['nama_lengkap'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          <div class="row">
            <div class="col-md-6 mb-2">
              <label class="form-label">NIK</label>
              <input name="nik" class="form-control" value="<?php echo e(old('nik')); ?>" required>
              <?php $__errorArgs = ['nik'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label">Tanggal Masuk</label>
              <input type="date" name="tanggal_masuk" class="form-control" value="<?php echo e(old('tanggal_masuk')); ?>" required>
              <?php $__errorArgs = ['tanggal_masuk'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 mb-2">
              <label class="form-label">Unit (ID)</label>
              <input name="unit_id" class="form-control" placeholder="ID Unit" value="<?php echo e(old('unit_id')); ?>" required>
              <?php $__errorArgs = ['unit_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-4 mb-2">
              <label class="form-label">Dept (ID)</label>
              <input name="department_id" class="form-control" placeholder="ID Dept" value="<?php echo e(old('department_id')); ?>" required>
              <?php $__errorArgs = ['department_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-4 mb-2">
              <label class="form-label">Jabatan (ID)</label>
              <input name="jabatan_id" class="form-control" placeholder="ID Jabatan" value="<?php echo e(old('jabatan_id')); ?>" required>
              <?php $__errorArgs = ['jabatan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-2">
              <label class="form-label">Email Kantor</label>
              
              <input name="email" type="email" class="form-control" value="<?php echo e(old('email')); ?>">
              <?php $__errorArgs = ['email_kantor'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label">No HP</label>
              <input name="no_hp" class="form-control" value="<?php echo e(old('no_hp')); ?>">
              <?php $__errorArgs = ['no_hp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\project_form\resources\views/hr/employee_profiles/index.blade.php ENDPATH**/ ?>