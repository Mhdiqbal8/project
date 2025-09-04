<!-- Modal Tambah -->
<div class="modal fade" id="tambah" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="<?php echo e(route('service.store')); ?>" method="POST">
      <?php echo csrf_field(); ?>
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalTambahLabel">Tambah Permohonan Service</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          
          <div class="form-group">
            <label for="jenis_inventaris_id">Jenis Inventaris</label>
            <select name="jenis_inventaris_id" id="jenis_inventaris_id" class="form-control" required>
              <option value="">-- Pilih Jenis --</option>
              <?php $__currentLoopData = $jenis_inventariss; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($j->id); ?>"><?php echo e($j->jenis_inventaris); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>

          
          <div class="form-group">
            <label for="inventaris_id">Nama Inventaris</label>
            <select name="inventaris_id" id="inventaris_id" class="form-control" required>
              <option value="">-- Pilih Inventaris --</option>
            </select>
          </div>

          
<div class="form-group">
  <label for="unit_tujuan_id">Unit Tujuan</label>
  <select name="unit_tujuan_id" id="unit_tujuan_id" class="form-control" required>
    <option value="">-- Pilih Unit Tujuan --</option>
    <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <option value="<?php echo e($unit->id); ?>"><?php echo e($unit->nama_unit); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </select>
</div>

          
          <div class="form-group">
            <label for="service">Deskripsi Perbaikan</label>
            <textarea name="service" class="form-control" rows="3" required></textarea>
          </div>

          
          <div class="form-group">
            <label for="keterangan">Keterangan Tambahan</label>
            <textarea name="keterangan" class="form-control" rows="3"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>
<?php /**PATH C:\xampp\htdocs\project_form\resources\views/service/_modal_tambah.blade.php ENDPATH**/ ?>