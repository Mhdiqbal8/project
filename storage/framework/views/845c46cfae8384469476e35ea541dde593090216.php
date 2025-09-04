

<?php $__env->startSection('content'); ?>
<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">✏️ Edit Data User</h4>
        </div>
        <form action="<?php echo e(route('management_user.update', $user->id)); ?>" method="POST" enctype="multipart/form-data" class="p-4">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control" value="<?php echo e($user->nama); ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>NIK</label>
                        <input type="text" name="nik" class="form-control" value="<?php echo e($user->nik); ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo e($user->username); ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo e($user->email); ?>" required>
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
                            <?php $__currentLoopData = $genders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gender): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="gender_id" id="gender<?php echo e($gender->id); ?>" value="<?php echo e($gender->id); ?>" <?php echo e($user->gender_id == $gender->id ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="gender<?php echo e($gender->id); ?>"><?php echo e($gender->gender); ?></label>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label>Upload TTD (opsional)</label>
                        <input type="file" name="ttd" class="form-control">
                        <?php if($user->ttd_path): ?>
                            <div class="mt-2">
                                <small class="d-block text-muted">TTD saat ini:</small>
                                <img src="<?php echo e(asset('storage/' . $user->ttd_path)); ?>" width="120" class="border rounded shadow-sm mt-1">
                            </div>
                        <?php endif; ?>
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
                            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($d->id); ?>" <?php echo e($user->department_id == $d->id ? 'selected' : ''); ?>><?php echo e($d->nama); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label>Jabatan</label>
                        <select name="jabatan_id" class="form-control" required>
                            <option value="">-- Pilih Jabatan --</option>
                            <?php $__currentLoopData = $jabatans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($j->id); ?>" <?php echo e($user->jabatan_id == $j->id ? 'selected' : ''); ?>><?php echo e($j->nama); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label>Status</label>
                        <select name="status_id" class="form-control" required>
                            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status->id); ?>" <?php echo e($user->status_id == $status->id ? 'selected' : ''); ?>><?php echo e($status->status); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label>Unit</label>
                        <select name="unit_id" class="form-control" required>
                            <option value="">-- Pilih Unit --</option>
                            <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($u->id); ?>" <?php echo e($user->unit_id == $u->id ? 'selected' : ''); ?>><?php echo e($u->nama_unit); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label>Kepala Unit</label>
                        <select name="kepala_unit_id" class="form-control">
                            <option value="">-- Pilih Kepala Unit --</option>
                            <?php $__currentLoopData = $allUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($u->id); ?>" <?php echo e(optional($user->unit)->kepala_unit_id == $u->id ? 'selected' : ''); ?>>
                                    <?php echo e($u->nama); ?> - <?php echo e($u->unit->nama_unit ?? ''); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label>Supervisor Unit</label>
                        <select name="supervisor_unit_id" class="form-control">
                            <option value="">-- Pilih Supervisor --</option>
                            <?php $__currentLoopData = $allUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($u->id); ?>" <?php echo e(optional($user->unit)->supervisor_unit_id == $u->id ? 'selected' : ''); ?>>
                                    <?php echo e($u->nama); ?> - <?php echo e($u->unit->nama_unit ?? ''); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label>Manajer Unit</label>
                        <select name="manager_unit_id" class="form-control">
                            <option value="">-- Pilih Manajer --</option>
                            <?php $__currentLoopData = $allUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($u->id); ?>" <?php echo e(optional($user->unit)->manager_unit_id == $u->id ? 'selected' : ''); ?>>
                                    <?php echo e($u->nama); ?> - <?php echo e($u->unit->nama_unit ?? ''); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
            </div>

            
            <?php
                $jabatanStaffId = 1;
                $userIsStaff = $user->jabatan_id == $jabatanStaffId;

                $isKepalaUnit = $user->id == optional($user->unit)->kepala_unit_id;
                $isSupervisorUnit = $user->id == optional($user->unit)->supervisor_unit_id;
                $isManagerUnit = $user->id == optional($user->unit)->manager_unit_id;
            ?>

            <?php if($userIsStaff && ($isKepalaUnit || $isSupervisorUnit || $isManagerUnit)): ?>
                <div class="alert alert-warning mt-4">
                    ⚠️ <strong>Perhatian:</strong> User ini jabatannya masih <strong>Staff</strong>,
                    namun terdaftar sebagai:
                    <ul class="mb-0">
                        <?php if($isKepalaUnit): ?>
                            <li><strong>Kepala Unit</strong></li>
                        <?php endif; ?>
                        <?php if($isSupervisorUnit): ?>
                            <li><strong>Supervisor Unit</strong></li>
                        <?php endif; ?>
                        <?php if($isManagerUnit): ?>
                            <li><strong>Manajer Unit</strong></li>
                        <?php endif; ?>
                    </ul>
                    Mohon pertimbangkan untuk memperbarui jabatannya agar sesuai dengan struktur unit.
                </div>
            <?php endif; ?>

            <div class="text-end mt-4">
                <a href="<?php echo e(url('management_user')); ?>" class="btn btn-secondary">⬅️ Kembali</a>
                <button type="submit" class="btn btn-success">💾 Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\project_form\resources\views/management_user/edit.blade.php ENDPATH**/ ?>