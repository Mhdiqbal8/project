
<div class="modal fade" id="modalApprove<?php echo e($sr->id); ?>" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form method="POST" action="<?php echo e(route('request_service.approve', $sr->id)); ?>">
      <?php echo csrf_field(); ?>
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Approve Permohonan</h5>
          <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">Setujui permohonan service ini?</div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Approve</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>


<div class="modal fade" id="modalProgress<?php echo e($sr->id); ?>" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form method="POST" action="<?php echo e(route('request_service.onprogress', $sr->id)); ?>">
      <?php echo csrf_field(); ?>
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Konfirmasi On Progress</h5>
          <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">Lanjutkan ke status <strong>On Progress</strong>?</div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Ya</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>


<div class="modal fade" id="modalTolak<?php echo e($sr->id); ?>" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form method="POST" action="<?php echo e(route('request_service.reject', $sr->id)); ?>">
      <?php echo csrf_field(); ?>
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Tolak Permohonan</h5>
          <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Alasan Penolakan</label>
            <textarea name="alasan" class="form-control" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Tolak</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>


<div class="modal fade" id="modalSelesai<?php echo e($sr->id); ?>" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form method="POST" action="<?php echo e(route('request_service.selesai', $sr->id)); ?>">
      <?php echo csrf_field(); ?>
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Tandai Selesai</h5>
          <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">Yakin permohonan ini sudah <strong>SELESAI</strong> dikerjakan?</div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Selesai</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>


<div class="modal fade" id="modalClosed<?php echo e($sr->id); ?>" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form method="POST" action="<?php echo e(route('request_service.closed', $sr->id)); ?>">
      <?php echo csrf_field(); ?>
      <div class="modal-content">
        <div class="modal-header bg-warning text-white">
          <h5 class="modal-title">Tutup Permohonan (CLOSED)</h5>
          <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">Yakin ingin menutup permohonan ini tanpa menyelesaikannya?</div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Tutup</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>
<?php /**PATH C:\xampp\htdocs\project_form\resources\views/request_service/partials/modal_action_fix.blade.php ENDPATH**/ ?>