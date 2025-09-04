

<?php $__env->startSection('content'); ?>
<div class="container mt-4">
    <h3 class="text-success mb-4">
        <i class="fas fa-user-shield"></i> Edit Privilege: <?php echo e($user->nama); ?>

    </h3>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('failed')): ?>
        <div class="alert alert-danger"><?php echo e(session('failed')); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="<?php echo e(route('privileges.update', $user->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>  
                <div class="mb-3">
                    <label class="form-label"><strong>Nama:</strong></label>
                    <p class="form-control-plaintext"><?php echo e($user->nama); ?></p>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Email:</strong></label>
                    <p class="form-control-plaintext"><?php echo e($user->email); ?></p>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Akses:</strong></label>
                    <div class="row">
                        <?php $__currentLoopData = $allAkses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $akses): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input 
                                        type="checkbox" 
                                        name="akses[]" 
                                        value="<?php echo e($akses->id); ?>"
                                        class="form-check-input"
                                        id="akses<?php echo e($akses->id); ?>"
                                        <?php echo e($user->akses->contains('id', $akses->id) ? 'checked' : ''); ?>

                                    >
                                    <label class="form-check-label" for="akses<?php echo e($akses->id); ?>">
                                        <?php echo e($akses->nama_akses); ?> <br>
                                        <small class="text-muted"><?php echo e($akses->deskripsi); ?></small>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-success mt-3">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>

    <a href="<?php echo e(route('privileges.index')); ?>" class="btn btn-secondary mt-3">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\project_form\resources\views/privileges/edit.blade.php ENDPATH**/ ?>