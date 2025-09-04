

<?php $__env->startSection('content'); ?>
<div class="container mt-4">

    
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold mb-0 text-success">
            <i class="fas fa-calendar-check me-2"></i> Daftar Laporan Harian Kerja
        </h3>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('laporan-kerja.export-excel')); ?>" class="btn btn-outline-success shadow-sm">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="<?php echo e(route('laporan-kerja.create')); ?>" class="btn btn-success shadow-sm">
                <i class="fas fa-plus"></i> Tambah Laporan
            </a>
        </div>
    </div>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success shadow-sm">
            <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    <form method="GET" class="card card-body shadow-sm border-0 mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="<?php echo e(request('tanggal')); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Nama User</label>
                <input type="text" name="user_name" class="form-control"
                       placeholder="Cari nama user..." value="<?php echo e(request('user_name')); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Status Komentar</label>
                <select name="komentar_status" class="form-select">
                    <option value="">-- Semua --</option>
                    <option value="ada" <?php echo e(request('komentar_status') == 'ada' ? 'selected' : ''); ?>>🔴 Ada Komentar</option>
                    <option value="beres" <?php echo e(request('komentar_status') == 'beres' ? 'selected' : ''); ?>>✅ Sudah Dibahas</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary w-100 shadow-sm">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <?php if(request()->anyFilled(['tanggal', 'user_name', 'komentar_status'])): ?>
                    <a href="<?php echo e(route('laporan-kerja.index')); ?>" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </form>

    
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle border">
            <thead class="bg-success text-white">
                <tr class="text-center">
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Shift</th>
                    <th>User</th>
                    <th>Jam In</th>
                    <th>Jam Out</th>
                    <th>Komentar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $laporans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $laporan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $shift = $laporan->shift;
                        $shiftBadge = match($shift) {
                            'AM' => ['color' => 'info', 'icon' => 'fa-sun'],
                            'PM' => ['color' => 'warning text-dark', 'icon' => 'fa-moon'],
                            'MALAM' => ['color' => 'secondary', 'icon' => 'fa-star'],
                            default => ['color' => 'secondary', 'icon' => 'fa-clock'],
                        };
                    ?>
                    <tr>
                        <td class="text-center">
                            <?php echo e(($laporans->currentPage() - 1) * $laporans->perPage() + $loop->iteration); ?>

                        </td>
                        <td><?php echo e(\Carbon\Carbon::parse($laporan->tanggal)->format('d/m/Y')); ?></td>
                        <td class="text-center">
                            <span class="badge bg-<?php echo e($shiftBadge['color']); ?>">
                                <i class="fas <?php echo e($shiftBadge['icon']); ?> me-1"></i>
                                <?php echo e($shift ?? '-'); ?>

                            </span>
                        </td>
                        <td><?php echo e($laporan->user?->nama ?? '-'); ?></td>
                        <td class="text-center">
                            <?php if($laporan->jam_in_mulai || $laporan->jam_in_selesai): ?>
                                <span class="badge bg-light text-dark border">
                                    <?php echo e($laporan->jam_in_mulai ?? '-'); ?> - <?php echo e($laporan->jam_in_selesai ?? '-'); ?>

                                </span>
                            <?php else: ?>
                                <span class="text-muted">Tidak Ada Jam In</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if($laporan->jam_out_mulai || $laporan->jam_out_selesai): ?>
                                <span class="badge bg-light text-dark border">
                                    <?php echo e($laporan->jam_out_mulai ?? '-'); ?> - <?php echo e($laporan->jam_out_selesai ?? '-'); ?>

                                </span>
                            <?php else: ?>
                                <span class="text-muted">Tidak Ada Jam Out</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php
                                $total_komentar = $laporan->komentar->count();
                                $total_beres = $laporan->komentar->where('is_beres', true)->count();
                            ?>

                            <?php if($total_komentar > 0 && $total_beres < $total_komentar): ?>
                                <span class="badge bg-danger">🔴 Ada Komentar</span>
                            <?php elseif($total_komentar > 0): ?>
                                <span class="badge bg-success">✅ Sudah Dibahas</span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="<?php echo e(route('laporan-kerja.show', $laporan->id)); ?>"
                               class="btn btn-sm btn-primary shadow-sm" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">Belum ada laporan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <div class="mt-4 d-flex justify-content-center">
        <?php echo e($laporans->links('pagination::bootstrap-5')); ?>

    </div>

    
    <div class="mt-4">
        <div class="card border-danger bg-light shadow-sm">
            <div class="card-body p-2 text-dark small">
                <i class="fas fa-info-circle me-2 text-danger"></i>
                <strong>Note:</strong> Gunakan jam maksimal 
                <span class="badge bg-dark text-white">23:59</span> 
                untuk jam akhir shift malam.
            </div>
        </div>
    </div>

</div>


<style>
    .pagination .page-link {
        padding: .4rem .7rem;
        font-size: 0.9rem;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\project_form\resources\views/laporan_kerja/index.blade.php ENDPATH**/ ?>