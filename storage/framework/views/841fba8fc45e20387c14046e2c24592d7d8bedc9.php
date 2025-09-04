

<?php $__env->startSection('content'); ?>
<div class="container mt-4">

    <h3 class="fw-bold mb-4 text-success">
        <i class="fas fa-calendar-check me-2"></i> Laporan Harian Kerja
    </h3>

    
    <?php if($errors->any()): ?>
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div class="alert alert-success shadow-sm">
            <i class="fas fa-check-circle me-2"></i> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('laporan-kerja.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">

                
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control shadow-sm" value="<?php echo e(old('tanggal')); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Shift</label>
                        <select name="shift" id="shift" class="form-select shadow-sm" required>
                            <option value="">-- Pilih Shift --</option>
                            <option value="Pagi" <?php echo e(old('shift') == 'Pagi' ? 'selected' : ''); ?>>Pagi</option>
                            <option value="Middle" <?php echo e(old('shift') == 'Middle' ? 'selected' : ''); ?>>Middle</option>
                            <option value="Sore" <?php echo e(old('shift') == 'Sore' ? 'selected' : ''); ?>>Sore</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <span class="text-muted small">Format waktu 24 jam, misal: 08:00, 15:00, 23:59</span>
                    </div>
                </div>

                
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Jam In (Mulai)</label>
                        <input type="time" name="jam_in_mulai" class="form-control shadow-sm" step="60" value="<?php echo e(old('jam_in_mulai')); ?>" placeholder="08:00">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Jam In (Selesai)</label>
                        <input type="time" name="jam_in_selesai" class="form-control shadow-sm" step="60" value="<?php echo e(old('jam_in_selesai')); ?>" placeholder="12:00">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Jam Out (Mulai)</label>
                        <input type="time" name="jam_out_mulai" class="form-control shadow-sm" step="60" value="<?php echo e(old('jam_out_mulai')); ?>" placeholder="13:00">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Jam Out (Selesai)</label>
                        <input type="time" name="jam_out_selesai" class="form-control shadow-sm" step="60" value="<?php echo e(old('jam_out_selesai')); ?>" placeholder="23:59">
                    </div>
                </div>

                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Kegiatan di Meja / Spesialis</label>
                        <textarea name="kegiatan_in" rows="4" class="form-control shadow-sm"
                            placeholder="- Perbaikan Teramedik&#10;- Design Foto / Editing Video&#10;- Perbaikan EForm Service"><?php echo e(old('kegiatan_in')); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Kegiatan di Lapangan / Luar Kantor</label>
                        <textarea name="kegiatan_out" rows="4" class="form-control shadow-sm"
                            placeholder="- Aktivasi Windows / Microsoft Office&#10;- Pengecekan Hardware&#10;- Pemasangan Hardware"><?php echo e(old('kegiatan_out')); ?></textarea>
                    </div>
                </div>

                
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Nama Petugas</label>
                        <input type="text" class="form-control bg-light shadow-sm" readonly value="<?php echo e(auth()->user()->nama); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Jabatan</label>
                        <input type="text" class="form-control bg-light shadow-sm" readonly value="<?php echo e(optional(auth()->user()->jabatan)->nama); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tanda Tangan</label>
                        <div class="border rounded p-3 bg-light text-center shadow-sm" style="min-height: 150px;">
                            <?php if(auth()->user()->ttd_path): ?>
                                <img src="<?php echo e(asset('storage/' . auth()->user()->ttd_path)); ?>" height="80" class="mb-2">
                                <br>
                                <a href="<?php echo e(route('user.hapus_ttd', auth()->user()->id)); ?>" class="btn btn-sm btn-danger">
                                    Hapus TTD
                                </a>
                            <?php else: ?>
                                <em class="text-muted">Belum ada tanda tangan.</em>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                
                <div class="mb-4">
                    <label class="form-label fw-bold">Catatan / Komentar Staff (Opsional)</label>
                    <textarea name="komentar_staff" rows="3" class="form-control shadow-sm"
                        placeholder="Tulis catatan jika perlu..."><?php echo e(old('komentar_staff')); ?></textarea>
                </div>

                
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-success px-4 py-2 shadow-sm">
                        <i class="fas fa-save me-2"></i> Simpan Laporan
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>


<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const shift = document.getElementById('shift');
        const placeholders = {
            Pagi: ['08:00', '12:00', '13:00', '15:00'],
            Middle: ['12:00', '15:00', '15:30', '18:00'],
            Sore: ['15:00', '18:00', '18:30', '23:59'],
        };

        shift.addEventListener('change', function () {
            const [inMulai, inSelesai, outMulai, outSelesai] = placeholders[this.value] || ['','','',''];
            document.querySelector('input[name="jam_in_mulai"]').placeholder = inMulai;
            document.querySelector('input[name="jam_in_selesai"]').placeholder = inSelesai;
            document.querySelector('input[name="jam_out_mulai"]').placeholder = outMulai;
            document.querySelector('input[name="jam_out_selesai"]').placeholder = outSelesai;
        });
    });
</script>
<?php $__env->stopPush(); ?>


<style>
    label.form-label {
        font-size: 0.95rem;
        color: #212529;
    }

    .form-control, .form-select, textarea {
        border-radius: .4rem;
        color: #212529;
    }

    .form-control::placeholder,
    textarea::placeholder {
        color: #6c757d;
        opacity: 0.8;
    }

    .form-control:focus,
    .form-select:focus,
    textarea:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, .25);
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\project_form\resources\views/laporan_kerja/create.blade.php ENDPATH**/ ?>