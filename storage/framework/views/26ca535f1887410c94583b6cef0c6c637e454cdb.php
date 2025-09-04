<?php $__env->startSection('content'); ?>
<!-- ===== Header ===== -->
<div class="header bg-primary pb-6">
  <div class="px-4">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Permohonan Service</h6>
        </div>
        <div class="col-lg-6 col-5 text-right">
          <a href="<?php echo e(route('request_service.export')); ?>" class="btn btn-success btn-sm">
            Export Excel
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ===== Content ===== -->
<div class="container-fluid mt--6">
  <div class="card shadow">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>No</th>
              <th>No Tiket</th>
              <th>Nama Pemohon</th>
              <th>Department</th>
              <th>Unit Asal</th>
              <th>Unit Tujuan</th>
              <th>Tgl Permohonan</th>
              <th>Status</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $service_requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <?php
                // PRIORITAS status dari request_services
                $statusId   = $sr->status_id ?? optional($sr->service)->status_id;
                $statusText = $sr->status_name ?? '-';

                $badge = match((int) $statusId) {
                  6 => 'info',       // Disetujui SPV/Manager (masuk antrean unit tujuan)
                  7 => 'primary',    // Sedang dikerjakan
                  8 => 'success',    // Closed
                  9 => 'secondary',  // Selesai & dilaporkan
                  10 => 'danger',    // Ditolak
                  default => 'light'
                };

                $user       = auth()->user();
                $unitTujuan = optional($sr->service)->unit_tujuan_id;
                $deptTujuan = \App\Models\User::where('unit_id', $unitTujuan)->value('department_id');

                // rule: boleh aksi kalau (unit user = unit tujuan) ATAU (SPV/Manager & departemen sama)
                $canApprove = in_array((int)$statusId, [6,7]) && (
                                $user->unit_id == $unitTujuan ||
                                (in_array($user->jabatan_id, [3,4]) && $deptTujuan && $user->department_id == $deptTujuan)
                              );

                // === URGENT FLAG ===
                // Ambil dari relasi service (utama). Kalau tabel request_services juga punya kolom sama, pakai fallback.
                $urgentFromService  = (int) (optional($sr->service)->type_permohonan ?? 0) === 1;
                $urgentFromRequest  = property_exists($sr, 'type_permohonan') ? ((int)$sr->type_permohonan === 1) : false;
                $isUrgent = $urgentFromService || $urgentFromRequest;
              ?>

              <tr <?php if($isUrgent): ?>
                    style="color:#dc3545;font-weight:600;background:rgba(220,53,69,.06);"
                  <?php endif; ?>>
                <td><?php echo e($loop->iteration); ?></td>
                <td><?php echo e(optional($sr->service)->no_tiket ?? '-'); ?></td>
                <td><?php echo e(optional(optional($sr->service)->user)->nama ?? '-'); ?></td>
                <td><?php echo e(optional(optional(optional($sr->service)->user)->department)->nama ?? '-'); ?></td>
                <td><?php echo e(optional(optional(optional($sr->service)->user)->unit)->nama_unit ?? '-'); ?></td>
                <td><?php echo e(optional(optional($sr->service)->unitTujuan)->nama_unit ?? '-'); ?></td>
                <td><?php echo e(optional($sr->created_at)->format('d-M-Y H:i')); ?> WIB</td>

                <td>
                  <span class="badge badge-<?php echo e($badge); ?> text-white"><?php echo e($statusText); ?></span>
                  <?php if($isUrgent): ?>
                    <span class="badge badge-danger ml-1">URGENT</span>
                  <?php endif; ?>
                </td>

                <td class="text-nowrap text-center">
                  <a href="<?php echo e(route('request_service.show', $sr->id)); ?>" class="btn btn-info btn-sm" title="Detail">
                    <i class="fas fa-eye"></i>
                  </a>

                  <?php if($canApprove): ?>
                    <?php if((int)$statusId === 6): ?>
                      
                      <button type="button" class="btn btn-warning btn-sm"
                              data-toggle="modal" data-target="#modalProgress<?php echo e($sr->id); ?>"
                              title="Mulai Dikerjakan">
                        <i class="fas fa-play"></i>
                      </button>
                      
                      <button type="button" class="btn btn-danger btn-sm"
                              data-toggle="modal" data-target="#modalTolak<?php echo e($sr->id); ?>"
                              title="Tolak">
                        <i class="fas fa-times"></i>
                      </button>

                    <?php elseif((int)$statusId === 7): ?>
                      
                      <button type="button" class="btn btn-success btn-sm"
                              data-toggle="modal" data-target="#modalSelesai<?php echo e($sr->id); ?>"
                              title="Selesai">
                        <i class="fas fa-check"></i>
                      </button>
                    <?php endif; ?>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr>
                <td colspan="9" class="text-center">Tidak ada data permohonan service</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>


<?php $__currentLoopData = $service_requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <?php echo $__env->make('request_service.partials.modal_action', ['sr' => $sr], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\project_form\resources\views/request_service/index_request.blade.php ENDPATH**/ ?>