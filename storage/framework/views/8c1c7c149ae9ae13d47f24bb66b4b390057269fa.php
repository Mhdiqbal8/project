

<?php $__env->startSection('content'); ?>
<?php
    // Deteksi Mutu (selaras dengan logic di AuthServiceProvider)
    $authUser = auth()->user();
    $isMutu = function_exists('isUserMutu')
        ? isUserMutu()
        : (
            (method_exists($authUser, 'hasRole') && $authUser->hasRole('mutu')) ||
            (method_exists($authUser, 'hasAccess') && (
                $authUser->hasAccess('acc_mutu_bap') ||
                $authUser->hasAccess('approve_mutu') ||
                $authUser->hasAccess('mutu') ||
                $authUser->hasAccess('mutu_read')
            ))
        );
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-success mb-0">
            <i class="fas fa-file-medical-alt me-2"></i> Detail Kronologis Pasien
        </h4>

        <div class="d-flex gap-2">
            
            <?php if($isMutu): ?>
                <?php if(empty($form->mutu_checked_at)): ?>
                    <form action="<?php echo e(route('kronologis.mutuCheck', $form->id)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-outline-primary rounded-pill me-2">
                            <i class="fas fa-check me-1"></i> Sudah Dibaca Mutu
                        </button>
                    </form>
                <?php else: ?>
                    <span class="badge bg-success align-self-center me-2">
                        Sudah dibaca Mutu <?php echo e(\Carbon\Carbon::parse($form->mutu_checked_at)->format('d-m-Y H:i')); ?>

                    </span>
                <?php endif; ?>
            <?php endif; ?>

            <a href="<?php echo e(route('kronologis.cetak', $form->id)); ?>" target="_blank" class="btn btn-outline-primary rounded-pill">
                <i class="fas fa-download me-2"></i> Download PDF
            </a>
        </div>
    </div>

    <div class="card shadow-lg p-5 rounded-4 border-0 bg-white">

        
        <div class="mb-4">
            <label class="fw-semibold text-dark">📝 Judul Kronologis:</label>
            <div class="fs-5 text-dark">
                <?php echo e($form->judul ?? '-'); ?>

            </div>
        </div>

        
        <div class="mb-4">
            <label class="fw-semibold text-dark">📋 Deskripsi:</label>
            <div class="border rounded bg-light p-3 text-dark" style="min-height: 80px;">
                <?php echo $form->deskripsi ?? '<em>(Belum ada deskripsi)</em>'; ?>

            </div>
        </div>

        
        <div class="mb-4">
            <label class="fw-semibold text-dark">👤 Informasi Pasien:</label>
            <ul class="list-unstyled ms-2 text-dark">
                <li><strong>👨‍⚕️ Nama:</strong> <?php echo e($form->nama_pasien ?? '-'); ?></li>
                <li><strong>📄 No. RM:</strong> <?php echo e($form->no_rm ?? '-'); ?></li>
                <li><strong>📝 Diagnosa:</strong> <?php echo e($form->diagnosa ?? '-'); ?></li>
                <li><strong>🏥 Ruangan:</strong> <?php echo e($form->ruangan ?? '-'); ?></li>
                <li><strong>🎂 Usia:</strong> <?php echo e($form->usia ?? '-'); ?></li>
                <li><strong>📅 Tanggal Kejadian:</strong> <?php echo e(\Carbon\Carbon::parse($form->tanggal)->format('d-m-Y')); ?></li>

                
                
            </ul>
        </div>

        
        <div class="mt-4">
            <a href="<?php echo e(route('bap.detail', $form->bapForm->id)); ?>" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Form BAP
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\project_form\resources\views/sistem_sdm/kronologis/detail_kronologis.blade.php ENDPATH**/ ?>