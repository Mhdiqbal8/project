<?php $__env->startSection('content'); ?>
<div class="header bg-primary pb-6">
  <div class="px-4">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Detail Service</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item"><a href="<?php echo e(url('home')); ?>"><i class="fas fa-home"></i></a></li>
              <li class="breadcrumb-item"><a href="<?php echo e(url('service')); ?>">Service</a></li>
              <li class="breadcrumb-item active" aria-current="page">Detail</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid mt--6">
  <div class="row">
    <div class="col">
      <div class="card shadow">
        <div class="card-header border-0">
          <h3 class="mb-0 text-dark">Detail Service</h3>
        </div>

        <div class="p-4 bg-light">
          <?php
            $userLogin        = auth()->user();
            $pemohon          = optional($service->user);
            $pemohonDept      = $pemohon->department_id;
            $pemohonUnitId    = $pemohon->unit_id;

            // Hak approve (lintas unit tapi 1 departemen)
            $canApproveSpv     = $userLogin->jabatan_id == 3 && $userLogin->department_id == $pemohonDept;
            $canApproveManager  = $userLogin->jabatan_id == 4 && $userLogin->department_id == $pemohonDept;

            // Kepala unit hanya view
            $isKepalaUnit = $userLogin->jabatan_id == 2;
            $isUnitYangDipimpin = \App\Models\Unit::where('id', $pemohonUnitId)
                ->where('kepala_unit_id', $userLogin->id)
                ->exists();

            // Sudah ada approve?
            $sudahApprove = $service->keterangan_service->contains(function ($k) {
              return str_contains($k->keterangan, '[APPROVE]');
            });
          ?>

          <?php if($isKepalaUnit && $isUnitYangDipimpin): ?>
            <div class="alert alert-info">
              Anda adalah Kepala Unit dari <strong><?php echo e($pemohon->unit->nama_unit ?? 'Unit'); ?></strong> dan dapat melihat permohonan ini. Namun, tidak memiliki akses untuk menyetujui.
            </div>
          <?php endif; ?>

          <div class="form-group">
            <label>Nama Pemohon</label>
            <input type="text" class="form-control" value="<?php echo e($pemohon->nama ?? '-'); ?>" readonly>
          </div>

          <div class="form-group">
            <label>Jabatan</label>
            <input type="text" class="form-control" value="<?php echo e($pemohon->jabatan->nama ?? '-'); ?>" readonly>
          </div>

          <div class="form-group">
            <label>Dari Departemen</label>
            <input type="text" class="form-control" value="<?php echo e($pemohon->department->nama ?? '-'); ?>" readonly>
          </div>

          <div class="form-group">
            <label>Tanggal Permohonan</label>
            <input type="text" class="form-control" value="<?php echo e(optional($service->created_at)->format('D, d M Y H:i')); ?> WIB" readonly>
          </div>

          <div class="form-group">
            <label>Jenis Inventaris</label>
            <input type="text" class="form-control" value="<?php echo e($service->inventaris->jenis_inventaris->jenis_inventaris ?? '-'); ?>" readonly>
          </div>

          <div class="form-group">
            <label>Inventaris</label>
            <input type="text" class="form-control" value="<?php echo e($service->inventaris->nama ?? '-'); ?>" readonly>
          </div>

          <div class="form-group">
            <label>Service/Perbaikan</label>
            <textarea class="form-control bg-success bg-opacity-25" rows="4" readonly><?php echo e($service->service); ?></textarea>
          </div>

          <div class="form-group">
            <label>Perkiraan Biaya</label>
            <input type="text" class="form-control" value="Rp <?php echo e(number_format($service->biaya_service ?? 0, 0, ',', '.')); ?>" readonly>
          </div>

          <div class="form-group">
            <label>Keterangan</label>
            <textarea class="form-control bg-success bg-opacity-25" rows="4" readonly><?php echo e($service->keterangan); ?></textarea>
          </div>

          <div class="form-group">
            <label>Status</label><br>
            <?php
              $statusText = config("status_label.{$service->status_id}", optional($service->status)->status ?? '-');
              $badgeClass = match($service->status_id) {
                3, 4 => 'badge-info',
                5 => 'badge-warning',
                6, 7 => 'badge-success',
                8 => 'badge-secondary',
                default => 'badge-danger'
              };
            ?>
            <span class="badge <?php echo e($badgeClass); ?>"><?php echo e($statusText); ?></span>
            <?php if(($service->type_permohonan ?? 0) == 1): ?>
              <span class="badge badge-danger ml-1">URGENT</span>
            <?php endif; ?>
          </div>

          <?php if($service->teknisi_id): ?>
            <div class="form-group">
              <label>Teknisi</label>
              <input type="text" class="form-control" value="<?php echo e($service->teknisi->nama ?? '-'); ?>" readonly>
            </div>
          <?php endif; ?>

          <?php if($service->teknisi_umum_id): ?>
            <div class="form-group">
              <label>Teknisi Umum</label>
              <input type="text" class="form-control" value="<?php echo e($service->teknisi_umum->nama ?? '-'); ?>" readonly>
            </div>
          <?php endif; ?>

          <?php if($keterangan_service && $keterangan_service->count()): ?>
            <div class="mt-4">
              <h5>Keterangan Lanjutan:</h5>
              <table class="table table-bordered bg-white">
                <thead class="thead-light">
                  <tr>
                    <th>User</th>
                    <th>Keterangan</th>
                    <th>Tanggal</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $__currentLoopData = $keterangan_service; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                      <td><?php echo e($val->user->nama ?? '-'); ?></td>
                      <td>
                        <?php $keterangan = $val->keterangan; ?>

                        <?php if(Str::contains($keterangan, '[APPROVE MODAL]')): ?>
                          ✅ Disetujui oleh <?php echo e($val->user->nama ?? '-'); ?> - <?php echo e(Str::after($keterangan, '[APPROVE MODAL]')); ?>

                        <?php elseif(Str::contains($keterangan, '[APPROVE URGENT]')): ?>
                          ✅ Disetujui sebagai Urgent - <?php echo e(Str::after($keterangan, '[APPROVE URGENT]')); ?>

                        <?php elseif(Str::contains($keterangan, '[APPROVE]')): ?>
                          ✅ Disetujui oleh <?php echo e(trim(Str::after($keterangan, '[APPROVE]'))); ?>

                        <?php elseif(Str::contains($keterangan, '[REJECT]')): ?>
                          ❌ Ditolak - <?php echo e(Str::after($keterangan, '[REJECT]')); ?>

                        <?php elseif(Str::contains($keterangan, 'Service dimulai')): ?>
                          🛠️ <?php echo e($keterangan); ?>

                        <?php elseif(Str::contains($keterangan, 'Service selesai')): ?>
                          🏋️ <?php echo e($keterangan); ?>

                        <?php else: ?>
                          <?php echo e($keterangan); ?>

                        <?php endif; ?>
                      </td>
                      <td><?php echo e($val->created_at->format('D, d M Y H:i')); ?></td>
                    </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>

        
        <div class="card-footer d-flex justify-content-end">
          <?php if (! (auth()->user()->hasRole('mutu'))): ?>

            
            <?php if(!$sudahApprove && $service->status_id == 3): ?>
              <?php if($canApproveSpv): ?>
                <button type="button"
                        class="btn btn-success btn-lg mr-2"
                        data-toggle="modal"        
                        data-target="#modalApprove<?php echo e($service->id); ?>"> 
                  ✅ Approve SPV
                </button>
              <?php elseif($canApproveManager): ?>
                <button type="button"
                        class="btn btn-success btn-lg mr-2"
                        data-toggle="modal"
                        data-target="#modalApprove<?php echo e($service->id); ?>">
                  ✅ Approve Manager
                </button>
              <?php endif; ?>
            <?php endif; ?>

          <?php endif; ?>

          <a href="<?php echo e(route('service.index')); ?>" class="btn btn-secondary btn-lg">🔙 Kembali</a>
        </div>

        
        <?php echo $__env->make('service._modal_approve', ['service' => $service], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('service._modal_tolak', ['service' => $service], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        

      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\project_form\resources\views/service/show.blade.php ENDPATH**/ ?>