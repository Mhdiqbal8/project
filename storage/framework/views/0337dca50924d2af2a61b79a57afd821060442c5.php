

<?php $__env->startSection('content'); ?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold text-success mb-0">
      <i class="fas fa-users-cog me-2"></i> E-Personalia — Dashboard
    </h4>

    <?php ($u = auth()->user()); ?>
    <div class="d-flex gap-2">
      
      <?php if($u && $u->hasPrivilege('access_leave')): ?>
        <a href="#" class="btn btn-outline-primary btn-sm disabled" aria-disabled="true">
          <i class="fas fa-plane-departure me-1"></i> Ajukan Cuti
        </a>
      <?php endif; ?>

      
      <?php if($u && $u->hasPrivilege('access_attendance')): ?>
        <a href="#" class="btn btn-outline-secondary btn-sm disabled" aria-disabled="true">
          <i class="fas fa-file-upload me-1"></i> Import Absensi
        </a>
      <?php endif; ?>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="text-muted small">Total Karyawan</div>
              <div class="fs-3 fw-bold"><?php echo e(number_format($totalKaryawan)); ?></div>
            </div>
            <i class="fas fa-user-friends fa-2x text-secondary"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="text-muted small">Cuti Pending</div>
              <div class="fs-3 fw-bold"><?php echo e(number_format($cutiPending)); ?></div>
            </div>
            <i class="fas fa-file-signature fa-2x text-secondary"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="text-muted small">Hadir Hari Ini</div>
              <div class="fs-3 fw-bold"><?php echo e(number_format($hadirHariIni)); ?></div>
            </div>
            <i class="fas fa-clipboard-check fa-2x text-secondary"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="alert alert-info mt-4">
    <strong>Next:</strong> aktifkan modul <em>Master Karyawan</em> (<code>hr_employee_profiles</code>)
    & <em>Time-Off</em> (<code>hr_leave_requests</code>) supaya tombol “Ajukan Cuti” full jalan.
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\project_form\resources\views/hr/dashboard.blade.php ENDPATH**/ ?>