

<?php $__env->startSection('content'); ?>
<?php
  use Illuminate\Support\Str;

  // map warna untuk action & method
  function actionBadgeClass($a){
    $a = strtolower($a ?? '');
    return match (true) {
      Str::contains($a, ['create','store','tag'])      => 'bg-success',
      Str::contains($a, ['approve','acc','final'])     => 'bg-primary',
      Str::contains($a, ['update','edit'])             => 'bg-warning text-dark',
      Str::contains($a, ['delete','destroy','reject']) => 'bg-danger',
      default                                          => 'bg-secondary'
    };
  }
  function methodBadgeClass($m){
    return match (strtoupper($m ?? '')) {
      'GET'    => 'bg-secondary',
      'POST'   => 'bg-primary',
      'PUT', 'PATCH' => 'bg-warning text-dark',
      'DELETE' => 'bg-danger',
      default  => 'bg-dark'
    };
  }
?>

<style>
  /* polish kecil biar enak dilihat */
  .card-sticky { position: sticky; top: 0; z-index: 2; }
  .table td, .table th { vertical-align: middle; }
  .nowrap { white-space: nowrap; }
  .muted-12 { font-size:.775rem; color:#6c757d; }
  .ip-chip { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }

  /* Hard block komponen DataTables kalau ada init global (darurat di halaman ini saja) */
  .dt-container .dataTables_info,
  .dt-container .dataTables_paginate,
  .dt-container .dataTables_length,
  .dt-container .dataTables_filter { display: none !important; }
</style>

<div class="container mt-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <h4 class="fw-bold mb-0">🗒️ Activity Logs</h4>
      <div class="text-muted">Pantau semua aksi pengguna—filter cepat & klik subject buat lompat ke detail.</div>
    </div>
  </div>

  
  <div class="card shadow-sm card-sticky mb-3">
    <div class="card-body">
      <form method="GET" id="filterForm" class="row g-2 align-items-end">
        <div class="col-12 col-md-3">
          <label class="form-label muted-12 mb-1">Cari</label>
          <input type="text" name="q" value="<?php echo e(request('q')); ?>" class="form-control"
            placeholder="User / action / deskripsi / URL / IP">
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label muted-12 mb-1">Dari</label>
          <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="form-control">
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label muted-12 mb-1">Sampai</label>
          <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="form-control">
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label muted-12 mb-1">Action</label>
          <select name="action" class="form-select">
            <option value="">Semua Action</option>
            <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($a); ?>" @selected(request('action')===$a)><?php echo e($a); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label muted-12 mb-1">User ID</label>
          <input type="number" name="user_id" value="<?php echo e(request('user_id')); ?>" class="form-control" placeholder="#id">
        </div>
        <div class="col-12 col-md-1 d-grid">
          <button class="btn btn-primary">Filter</button>
        </div>
      </form>

      
      <div class="d-flex gap-2 mt-2">
        <button class="btn btn-sm btn-outline-secondary quick-range" data-range="today">Hari ini</button>
        <button class="btn btn-sm btn-outline-secondary quick-range" data-range="7d">7 hari</button>
        <button class="btn btn-sm btn-outline-secondary quick-range" data-range="month">Bulan ini</button>
        <a href="<?php echo e(route('activity.index')); ?>" class="btn btn-sm btn-link text-danger ms-auto">Reset</a>
      </div>
    </div>
  </div>

  
  <div class="card shadow-sm">
    <div class="table-responsive dt-container">
      <table id="logsTable" class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th class="nowrap" style="width:160px">Waktu</th>
            <th style="width:200px">User</th>
            <th style="width:160px">Action</th>
            <th style="width:220px">Subject</th>
            <th>Deskripsi</th>
            <th style="width:260px">Request</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
              $hasSubject = !empty($log->subject_type);
              $type = $hasSubject ? class_basename($log->subject_type) : null;
              $id   = $log->subject_id ?? null;

              $link = null;
              if ($hasSubject) {
                $link = match ($type) {
                  'BapForm'        => route('bap.detail', $id),
                  'KronologisForm' => route('kronologis.view', $id),
                  'Service'        => (Route::has('service.show') ? route('service.show', $id) : null),
                  'RequestService' => (Route::has('request_service.show') ? route('request_service.show', $id) : null),
                  'LaporanKerja'   => (Route::has('laporan-kerja.show') ? route('laporan-kerja.show', $id) : null),
                  default          => null,
                };
              }

              $actionClass = actionBadgeClass($log->action);
              $methodClass = methodBadgeClass($log->method);
            ?>

            <tr>
              <td class="nowrap">
                <div><?php echo e($log->created_at->format('d-m-Y H:i')); ?></div>
                <div class="muted-12">#<?php echo e($log->id); ?></div>
              </td>

              <td>
                <div class="fw-semibold"><?php echo e($log->user->nama ?? $log->user->username ?? '—'); ?></div>
                <div class="muted-12">ID: <?php echo e($log->user_id ?? '—'); ?></div>
              </td>

              <td>
                <span class="badge <?php echo e($actionClass); ?>"><?php echo e($log->action); ?></span>
              </td>

              <td>
                <?php if($hasSubject): ?>
                  <?php if($link): ?>
                    <a href="<?php echo e($link); ?>" class="small text-decoration-none">
                      <?php echo e($type); ?> <span class="text-muted">#<?php echo e($id); ?></span>
                    </a>
                  <?php else: ?>
                    <span class="small"><?php echo e($type); ?> #<?php echo e($id); ?></span>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="text-muted">—</span>
                <?php endif; ?>
              </td>

              <td><?php echo e($log->description ?? '—'); ?></td>

              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="badge <?php echo e($methodClass); ?>"><?php echo e(strtoupper($log->method ?? '—')); ?></span>
                  <span class="ip-chip"><?php echo e($log->ip_address ?? '—'); ?></span>
                  <?php if($log->url): ?>
                    <button class="btn btn-sm btn-outline-secondary px-2 py-1 copy-url"
                            data-url="<?php echo e($log->url); ?>" title="Copy URL"
                            data-bs-toggle="tooltip" data-bs-title="Copy URL">
                      <i class="bi bi-clipboard"></i>
                    </button>
                  <?php endif; ?>
                </div>
                <div class="muted-12" title="<?php echo e($log->url ?? ''); ?>">
                  <?php echo e(Str::limit($log->url ?? '', 54)); ?>

                </div>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="6" class="text-center py-5">
                <div class="text-muted">
                  <div class="mb-1">Belum ada log untuk filter saat ini.</div>
                  <a href="<?php echo e(route('activity.index')); ?>" class="btn btn-sm btn-outline-primary">Tampilkan semua</a>
                </div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if($logs->hasPages()): ?>
      <div class="card-footer d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <div class="muted-12">
          Menampilkan <?php echo e($logs->firstItem()); ?>–<?php echo e($logs->lastItem()); ?> dari <?php echo e($logs->total()); ?>

        </div>
        <div class="ms-auto">
          <?php echo e($logs->onEachSide(1)->links('pagination::bootstrap-5')); ?>

        </div>
      </div>
    <?php endif; ?>
  </div>
</div>


<?php $__env->startPush('scripts'); ?>
<script>
(function(){
  // Jika ada init DataTables global, pastikan tabel ini tidak pakai
  if (window.jQuery && $.fn.DataTable && $.fn.DataTable.isDataTable('#logsTable')) {
    $('#logsTable').DataTable().destroy();
  }

  // Bootstrap tooltip
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

  // Quick range
  const form = document.getElementById('filterForm');
  const from = form.querySelector('input[name="from"]');
  const to   = form.querySelector('input[name="to"]');
  document.querySelectorAll('.quick-range').forEach(btn=>{
    btn.addEventListener('click', e=>{
      e.preventDefault();
      const now = new Date();
      const pad = n => String(n).padStart(2,'0');
      const toStr = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}`;
      let fromDate = new Date(now);
      const r = btn.dataset.range;
      if(r==='today'){ /* same day */ }
      else if(r==='7d'){ fromDate.setDate(now.getDate()-6); }
      else if(r==='month'){ fromDate = new Date(now.getFullYear(), now.getMonth(), 1); }
      const fromStr = `${fromDate.getFullYear()}-${pad(fromDate.getMonth()+1)}-${pad(fromDate.getDate())}`;
      from.value = fromStr; to.value = toStr; form.submit();
    });
  });

  // Debounce search typing
  let t=null;
  const q = form.querySelector('input[name="q"]');
  q.addEventListener('input', ()=>{
    clearTimeout(t);
    t = setTimeout(()=>form.submit(), 500);
  });

  // Copy URL
  document.querySelectorAll('.copy-url').forEach(btn=>{
    btn.addEventListener('click', async ()=>{
      try {
        await navigator.clipboard.writeText(btn.dataset.url);
        btn.setAttribute('data-bs-title','Copied!');
        bootstrap.Tooltip.getInstance(btn).show();
        setTimeout(()=>{
          btn.setAttribute('data-bs-title','Copy URL');
          bootstrap.Tooltip.getInstance(btn).hide();
        }, 900);
      } catch(e){}
    });
  });
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\project_form\resources\views/activity/index.blade.php ENDPATH**/ ?>