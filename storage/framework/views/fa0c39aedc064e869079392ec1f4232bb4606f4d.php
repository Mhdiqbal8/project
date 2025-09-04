
<div class="modal fade" id="modalTolak<?php echo e($service->id); ?>" tabindex="-1" role="dialog" aria-labelledby="modalRejectLabel<?php echo e($service->id); ?>" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <form action="<?php echo e(url('reject/service')); ?>" method="POST">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="service_id" value="<?php echo e($service->id); ?>">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="modalRejectLabel<?php echo e($service->id); ?>">Tolak Permohonan</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Alasan Penolakan</label>
            <textarea name="keterangan" class="form-control" rows="3" required></textarea>
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
<?php /**PATH C:\xampp\htdocs\project_form\resources\views/service/_modal_tolak.blade.php ENDPATH**/ ?>