<?php
    $user  = $user  ?? auth()->user();
    $units = $units ?? collect();
?>


<?php if(
    in_array(strtolower(optional($user->jabatan)->nama), ['kepala unit', 'supervision']) &&
    !$form->kepala_unit_approved_at &&
    !$form->supervision_approved_at
): ?>
    <?php if(strtolower(optional($user->jabatan)->nama) === 'kepala unit'): ?>
        <form method="POST" action="<?php echo e(route('bap.approve_kepala_unit', $form->id)); ?>" class="mb-3">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-success">ACC Kepala Unit</button>
        </form>
    <?php elseif(strtolower(optional($user->jabatan)->nama) === 'supervision'): ?>
        <form method="POST" action="<?php echo e(route('bap.approve_supervision', $form->id)); ?>" class="mb-3">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-primary">ACC Supervision</button>
        </form>
    <?php endif; ?>
<?php endif; ?>


<?php if(user_can('acc_manager_bap') && !$form->manager_approved_at): ?>
    <form method="POST" action="<?php echo e(route('bap.approve', $form->id)); ?>" class="mb-3">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-success">Approve sebagai Manager</button>
    </form>
<?php endif; ?>


<?php if(user_can('acc_mutu_bap') && $form->manager_approved_at && !$form->mutu_approved_at): ?>
    <form method="POST" action="<?php echo e(route('bap.accMutu', $form->id)); ?>" class="mb-3">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-warning">✔ ACC MUTU & Tag Unit</button>
    </form>
<?php endif; ?>


<?php if(user_can('acc_mutu_bap') && $form->mutu_approved_at): ?>
    <button class="btn btn-info mb-3" data-bs-toggle="modal" data-bs-target="#modalTagUnit">
        📌 Tag Unit Terkait
    </button>

    <!-- Modal Tag Unit -->
    <div class="modal fade" id="modalTagUnit" tabindex="-1" aria-labelledby="modalTagUnitLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?php echo e(route('bap.tag_unit', $form->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTagUnitLabel">Pilih Unit yang Terlibat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="unit_ids[]"
                                           value="<?php echo e($unit->id); ?>"
                                           id="unit<?php echo e($unit->id); ?>"
                                           <?php echo e($form->taggedUnits->contains($unit->id) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="unit<?php echo e($unit->id); ?>">
                                        <?php echo e($unit->nama); ?>

                                    </label>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Simpan Tag Unit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>


<?php if(
    user_can('acc_final_bap') &&
    !$form->final_approved_at &&
    $form->manager_approved_at &&
    $form->mutu_approved_at
): ?>
    <!-- <form method="POST" action="<?php echo e(route('bap.approve', $form->id)); ?>" class="mb-3">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-dark">Finalisasi Form</button>
    </form> -->

    
    <form method="POST" action="<?php echo e(route('bap.kendala_update', $form->id)); ?>" class="mb-3">
        <?php echo csrf_field(); ?>
        <div class="mb-3">
            <label for="kendala" class="form-label fw-bold">
                Isi Kendala / Tindakan Unit Terkait:
            </label>
            <textarea name="kendala" id="kendala" class="form-control" rows="3" required><?php echo e($form->kendala); ?></textarea>
        </div>
        <button type="submit" class="btn btn-warning">Simpan Kendala</button>
    </form>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\project_form\resources\views/sistem_sdm/bap/partials/approval-buttons.blade.php ENDPATH**/ ?>