<!-- Modal Tambah User -->
<div class="modal fade" id="tambah" tabindex="-1" role="dialog" aria-labelledby="modalTambahUserLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content rounded-4 shadow">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalTambahUserLabel">➕ Tambah User Baru</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="<?php echo e(url('management_user/store')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label>Nama</label>
              <input type="text" name="nama" class="form-control" placeholder="Nama" required>

              <label class="mt-3">NIK</label>
              <input type="text" name="nik" class="form-control" maxlength="16" placeholder="NIK" required>

              <label class="mt-3">Username</label>
              <input type="text" name="username" class="form-control" placeholder="Username" required>

              <label class="mt-3">Email</label>
              <input type="email" name="email" class="form-control" placeholder="Email" required>

              <label class="mt-3">Password</label>
              <input type="password" name="password" class="form-control" required>

              <label class="mt-3">Konfirmasi Password</label>
              <input type="password" name="password_confirmed" class="form-control" required>

              <label class="mt-3 d-block">Jenis Kelamin</label>
              <small class="text-muted d-block mb-2">Silakan pilih salah satu: Laki-laki atau Perempuan</small>

              <?php $__currentLoopData = $genders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gender): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio"
                         name="gender_id" id="gender_<?php echo e($gender->id); ?>"
                         value="<?php echo e($gender->id); ?>" required>

                  <!-- data-initial = L / P (atau huruf pertama nama gender) -->
                  <label class="form-check-label gender-pill"
                         for="gender_<?php echo e($gender->id); ?>"
                         data-initial="<?php echo e(strtoupper(mb_substr($gender->nama, 0, 1))); ?>"
                         title="<?php echo e($gender->nama); ?>">
                    <span class="sr-only"><?php echo e($gender->nama); ?></span>
                  </label>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

              <label class="mt-3">Upload TTD (opsional)</label>
              <input type="file" name="ttd" class="form-control">
            </div>

            <div class="col-md-6">
              <label>Department</label>
              <select name="department_id" class="form-control" required>
                <option value="">-- Pilih Department --</option>
                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($department->id); ?>"><?php echo e($department->nama); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>

              <label class="mt-3">Jabatan</label>
              <select name="jabatan_id" class="form-control" required>
                <option value="">-- Pilih Jabatan --</option>
                <?php $__currentLoopData = $jabatans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jabatan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($jabatan->id); ?>"><?php echo e($jabatan->nama); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>

              <label class="mt-3">Status</label>
              <select name="status_id" class="form-control" required>
                <option value="1">Active</option>
                <option value="2">Non-Active</option>
              </select>

              <label class="mt-3">Unit</label>
              <select name="unit_id" class="form-control" required>
                <option value="">-- Pilih Unit --</option>
                <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($u->id); ?>"><?php echo e($u->nama_unit); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>

              <label class="mt-3">Kepala Unit</label>
              <select name="kepala_unit_id" class="form-control">
                <option value="">-- Pilih Kepala Unit --</option>
                <?php $__currentLoopData = $allUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($u->id); ?>"><?php echo e($u->nama); ?> - <?php echo e($u->unit->nama_unit ?? ''); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>

              <label class="mt-3">Supervisor Unit</label>
              <select name="supervisor_unit_id" class="form-control">
                <option value="">-- Pilih Supervisor --</option>
                <?php $__currentLoopData = $allUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($u->id); ?>"><?php echo e($u->nama); ?> - <?php echo e($u->unit->nama_unit ?? ''); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>

              <label class="mt-3">Manajer Unit</label>
              <select name="manager_unit_id" class="form-control">
                <option value="">-- Pilih Manajer --</option>
                <?php $__currentLoopData = $allUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($u->id); ?>"><?php echo e($u->nama); ?> - <?php echo e($u->unit->nama_unit ?? ''); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">❌ Batal</button>
          <button type="submit" class="btn btn-success">💾 Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ==== UI-ONLY OVERRIDES (L/P di dalam pill) ==== -->
<style>
  .sr-only{
    position:absolute!important; width:1px!important; height:1px!important;
    padding:0!important; margin:-1px!important; overflow:hidden!important;
    clip:rect(0,0,0,0)!important; white-space:nowrap!important; border:0!important;
  }

  /* sembunyiin radio bawaan */
  #tambah .form-check-input[name="gender_id"]{
    position:absolute; opacity:0; pointer-events:none;
  }

  /* gaya pill */
  #tambah .gender-pill{
    position:relative;
    display:inline-flex; align-items:center; justify-content:center;
    padding:.25rem; border-radius:999px;
    background:#ecfdf5;                 /* brand-50 */
    border:1px solid #a7f3d0;           /* brand-200 */
    min-width:64px; height:34px;
    margin:.15rem .35rem .15rem 0;
    cursor:pointer; user-select:none;
    transition:all .15s ease;
  }
  /* huruf L/P dari attribute */
  #tambah .gender-pill::after{
    content: attr(data-initial);
    display:inline-flex; align-items:center; justify-content:center;
    width:48px; height:26px; border-radius:999px;
    font-weight:800; letter-spacing:.2px;
    color:#0f172a;                       /* teks gelap saat idle */
    line-height:26px; text-align:center;
  }

  /* hover */
  #tambah .form-check-input[name="gender_id"] + .gender-pill:hover{
    background:#d1fae5; border-color:#34d399;
  }

  /* checked state */
  #tambah .form-check-input[name="gender_id"]:checked + .gender-pill{
    background: linear-gradient(135deg,#6ee7b7,#22c55e);
    border-color:#22c55e; box-shadow:0 4px 12px rgba(34,197,94,.25);
  }
  #tambah .form-check-input[name="gender_id"]:checked + .gender-pill::after{
    color:#fff;
  }

  /* jarak & rapi */
  #tambah .form-check-inline{ margin-right:.5rem; }
  #tambah label.mt-3{ margin-top:1rem !important; }

  /* BONUS: modal & input sedikit lebih halus (opsional, aman) */
  #tambah .modal-content.rounded-4{ border-radius:18px !important; overflow:hidden; }
  #tambah .modal-header.bg-success{
    background: linear-gradient(135deg,#6ee7b7,#22c55e)!important; border-bottom:none;
  }
  #tambah .modal-body .form-control{
    border-radius:10px; border:1px solid rgba(0,0,0,.12);
    transition: box-shadow .15s ease, border-color .15s ease;
  }
  #tambah .modal-body .form-control:focus{
    border-color:#6ee7b7; box-shadow:0 0 0 .2rem rgba(52,211,153,.18);
  }
</style>
<?php /**PATH C:\xampp\htdocs\project_form\resources\views/management_user/modal_tambah.blade.php ENDPATH**/ ?>