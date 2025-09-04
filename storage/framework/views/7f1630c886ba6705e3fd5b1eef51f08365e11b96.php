

<?php $__env->startSection('content'); ?>
<div class="container mt-4" style="max-width: 1200px;">

    
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <a href="<?php echo e(route('laporan-kerja.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <!-- <a href="<?php echo e(route('laporan-kerja.cetak', $laporan->id)); ?>" target="_blank" class="btn btn-success">
            <i class="fas fa-print me-1"></i> Cetak PDF
        </a> -->
    </div>

    <div class="card shadow-sm border-0 bg-white">
        <div class="card-body p-4">
            <h3 class="mb-4 fw-bold text-success">
                <i class="fas fa-calendar-check me-2"></i>
                Detail Laporan Harian Kerja
            </h3>

            
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <label class="fw-bold text-dark small">Tanggal</label>
                    <div class="form-control bg-light fs-6 py-3 rounded">
                        <?php echo e($laporan->tanggal ?? '-'); ?>

                    </div>
                </div>
                <div class="col-md-3">
                    <label class="fw-bold text-dark small">Shift</label>
                    <div class="form-control bg-light fs-6 py-3 rounded">
                        <?php echo e($laporan->shift ?? '-'); ?>

                    </div>
                </div>
                <div class="col-md-3">
                    <label class="fw-bold text-dark small">Jam In (Mulai-Selesai)</label>
                    <div class="form-control bg-light fs-6 py-3 rounded">
                         <?php if($laporan->jam_in_mulai || $laporan->jam_in_selesai): ?>
                         <?php echo e($laporan->jam_in_mulai ?? '-'); ?> - <?php echo e($laporan->jam_in_selesai ?? '-'); ?>

                        <?php else: ?>
                            <span class="text-muted">Tidak ada jam.</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="fw-bold text-dark small">Jam Out (Mulai-Selesai)</label>
                    <div class="form-control bg-light fs-6 py-3 rounded">
                        <?php if($laporan->jam_out_mulai || $laporan->jam_out_selesai): ?>
                            <?php echo e($laporan->jam_out_mulai ?? '-'); ?> - <?php echo e($laporan->jam_out_selesai ?? '-'); ?>

                        <?php else: ?>
                            <span class="text-muted">Tidak ada jam.</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="mb-4">
                <label class="fw-bold text-dark small">Kegiatan di Meja / Spesialis</label>
                <div class="p-3 bg-light rounded border fs-6" style="
                    min-height: 150px;
                    white-space: pre-line;
                    word-break: break-word;
                    line-height: 1.6;
                ">
                    <?php echo e($laporan->kegiatan_in ?: 'Tidak ada kegiatan di meja.'); ?>

                </div>
            </div>

            
            <div class="mb-4">
                <label class="fw-bold text-dark small">Kegiatan di Lapangan / Luar Kantor</label>
                <div class="p-3 bg-light rounded border fs-6" style="
                    min-height: 150px;
                    white-space: pre-line;
                    word-break: break-word;
                    line-height: 1.6;
                ">
                    <?php echo e($laporan->kegiatan_out ?: 'Tidak ada kegiatan di luar kantor.'); ?>

                </div>
            </div>

            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="fw-bold text-dark small">Nama Petugas</label>
                    <div class="form-control bg-light fs-6 py-3 rounded">
                        <?php echo e(optional($laporan->user)->nama ?? '-'); ?>

                    </div>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-dark small">Jabatan</label>
                    <div class="form-control bg-light fs-6 py-3 rounded">
                        <?php echo e(optional($laporan->user->jabatan)->nama ?? '-'); ?>

                    </div>
                </div>
            </div>

            
            <div class="mb-4">
                <label class="fw-bold text-dark small">Komentar Staff</label>
                <div class="p-3 bg-light rounded border fs-6" style="
                    min-height: 100px;
                    max-height: 300px;
                    overflow-y: auto;
                    white-space: pre-wrap;
                    word-break: break-word;
                    line-height: 1.6;
                ">
                    <?php if($laporan->komentar_staff): ?>
                        <?php echo nl2br(e($laporan->komentar_staff)); ?>

                    <?php else: ?>
                        <em class="text-muted">Tidak ada komentar.</em>
                    <?php endif; ?>
                </div>
            </div>

            
            <h5 class="fw-bold mt-5 text-success">
                <i class="fas fa-comments me-2"></i> Histori Komentar
            </h5>

            <div class="table-responsive mt-3">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-success text-dark">
                        <tr class="text-center">
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Waktu</th>
                            <th style="width: 50%">Komentar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $laporan->komentar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $komentar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="fw-bold"><?php echo e($komentar->user->nama); ?></td>
                                <td><?php echo e(optional($komentar->user->jabatan)->nama ?? '-'); ?></td>
                                <td><?php echo e($komentar->created_at->format('d-m-Y H:i')); ?></td>
                                <td>
                                    <div class="bg-light border p-2 rounded mb-2" style="
                                        white-space: pre-wrap;
                                        word-break: break-word;
                                        overflow-wrap: anywhere;
                                        font-size: 0.9rem;
                                        line-height: 1.5;
                                    ">
                                        <?php echo e($komentar->komentar); ?>

                                    </div>

                                    <?php if(!$komentar->is_beres && auth()->user()->jabatan_id >= 2): ?>
                                        <form action="<?php echo e(route('laporan-kerja.komentar.beres', $komentar->id)); ?>"
                                              method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <button class="btn btn-success btn-sm shadow-sm">
                                                ✅ Tandai Sudah Dibahas
                                            </button>
                                        </form>
                                    <?php elseif($komentar->is_beres): ?>
                                        <span class="badge bg-success">✅ Sudah Dibahas</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    <em>Belum ada komentar.</em>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <?php if(auth()->user()->jabatan_id >= 2): ?>
                <h5 class="fw-bold mt-5 text-primary">
                    <i class="fas fa-comment-dots me-2"></i> Kirim Komentar
                </h5>
                <form action="<?php echo e(route('laporan-kerja.komentar', $laporan->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Komentar Anda</label>
                        <textarea name="komentar" class="form-control fs-6 shadow-sm" rows="4" placeholder="Tulis komentar Anda..."><?php echo e(old('komentar')); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm">
                        <i class="fas fa-paper-plane me-1"></i> Kirim Komentar
                    </button>
                </form>
            <?php endif; ?>

        </div>
    </div>
</div>


<style>
    .table thead th {
        font-size: 0.9rem;
    }

    .table td {
        font-size: 0.9rem;
        vertical-align: top;
    }

    .table-hover tbody tr:hover {
        background-color: #e9fbe8;
    }

    .badge {
        font-size: 0.8rem;
    }

    .btn-sm {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\project_form\resources\views/laporan_kerja/show.blade.php ENDPATH**/ ?>