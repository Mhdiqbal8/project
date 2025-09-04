
<?php if($sr->service): ?>

  
  <div class="modal fade" id="modalProgress<?php echo e($sr->id); ?>" tabindex="-1" role="dialog" aria-labelledby="modalProgressLabel<?php echo e($sr->id); ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form method="POST" action="<?php echo e(route('request_service.approveProgress', ['id' => $sr->id])); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PATCH'); ?>
        <input type="hidden" name="request_service_id" value="<?php echo e($sr->id); ?>">
        <input type="hidden" name="service_id" value="<?php echo e($sr->service_id); ?>">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="modalProgressLabel<?php echo e($sr->id); ?>">Konfirmasi On Progress</h5>
            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            Lanjutkan status ke <strong>On Progress</strong> sekarang?
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Ya</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  
  <div class="modal fade" id="modalTolak<?php echo e($sr->id); ?>" tabindex="-1" role="dialog" aria-labelledby="modalTolakLabel<?php echo e($sr->id); ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form method="POST" action="<?php echo e(route('request_service.reject')); ?>">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="request_service_id" value="<?php echo e($sr->id); ?>">
        <input type="hidden" name="service_id" value="<?php echo e($sr->service_id); ?>">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="modalTolakLabel<?php echo e($sr->id); ?>">Tolak Permohonan</h5>
            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Alasan Penolakan</label>
              <textarea name="keterangan" class="form-control" rows="3" required placeholder="Tulis alasan penolakan..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menolak permohonan ini?')">Tolak</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  
  <div class="modal fade" id="modalSelesai<?php echo e($sr->id); ?>" tabindex="-1" role="dialog" aria-labelledby="modalSelesaiLabel<?php echo e($sr->id); ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form method="POST" action="<?php echo e(route('request_service.approveFinish', ['id' => $sr->id])); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PATCH'); ?>
        <input type="hidden" name="request_service_id" value="<?php echo e($sr->id); ?>">
        <input type="hidden" name="service_id" value="<?php echo e($sr->service_id); ?>">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="modalSelesaiLabel<?php echo e($sr->id); ?>">Tandai Selesai</h5>
            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <p class="mb-2">Tuliskan ringkasan pekerjaan/perbaikan yang dilakukan (WAJIB).</p>
            <div class="form-group">
              <label>Ringkasan pekerjaan/perbaikan</label>
              <textarea name="keterangan" class="form-control" rows="3" required placeholder="Contoh: Ganti keyboard, update driver VGA, bersihkan fan, tes OK."><?php echo e(old('keterangan')); ?></textarea>
              <?php $__errorArgs = ['keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <small class="text-danger d-block mt-1"><?php echo e($message); ?></small>
              <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Selesai</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  
  <div class="modal fade" id="modalClosed<?php echo e($sr->id); ?>" tabindex="-1" role="dialog" aria-labelledby="modalClosedLabel<?php echo e($sr->id); ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
      
      <form method="POST" action="<?php echo e(route('request_service.closed', ['id' => $sr->id])); ?>">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="request_service_id" value="<?php echo e($sr->id); ?>">
        <input type="hidden" name="service_id" value="<?php echo e($sr->service_id); ?>">
        <div class="modal-content">
          <div class="modal-header bg-warning text-white">
            <h5 class="modal-title" id="modalClosedLabel<?php echo e($sr->id); ?>">Tutup Tiket (Closed)</h5>
            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            Tutup tiket tanpa tindakan lanjutan?
            <div class="form-group mt-3">
              <label>Keterangan (opsional)</label>
              <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan close tiket..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-warning" onclick="return confirm('Yakin menutup tiket ini?')">Closed</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          </div>
        </div>
      </form>
    </div>
  </div>

<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\project_form\resources\views/request_service/partials/modal_action.blade.php ENDPATH**/ ?>