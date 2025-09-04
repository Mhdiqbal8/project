


<?php $__env->startSection('content'); ?>
<div class="container mt-4">

  
  <?php
    // fallback aman kalau controller belum kirim
    $month = $month ?? now()->month;
    $year  = $year  ?? now()->year;
    $start = ($start ?? \Carbon\Carbon::create($year, $month, 1)->startOfDay());
    $end   = ($end   ?? (clone $start)->endOfMonth()->endOfDay());
    $startQ = $start->copy()->startOfMonth()->format('Y-m-d');
    $endQ   = $start->copy()->endOfMonth()->format('Y-m-d');
  ?>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold text-primary mb-0">Laporan Service per Bulan</h4>
    <div class="d-flex gap-2">
      
      <a href="<?php echo e(route('laporan.service')); ?>?start_date=<?php echo e($startQ); ?>&end_date=<?php echo e($endQ); ?>"
         class="btn btn-outline-secondary btn-sm">
        « Kembali (Detail by Tanggal)
      </a>

      <a href="<?php echo e(route('laporan.service.bulanan.pdf', request()->only(['month','year']))); ?>"
         class="btn btn-danger btn-sm" target="_blank" rel="noopener">
        Export PDF
      </a>
    </div>
  </div>

  
  <form method="GET" action="<?php echo e(route('laporan.service.bulanan')); ?>" class="card mb-3 shadow-sm">
    <div class="card-body row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label">Bulan</label>
        <select name="month" class="form-select">
          <?php for($i=1; $i<=12; $i++): ?>
            <option value="<?php echo e($i); ?>" @selected($i == $month)">
              <?php echo e(\Carbon\Carbon::create(null, $i, 1)->isoFormat('MMMM')); ?>

            </option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Tahun</label>
        <input type="number" name="year" class="form-control" value="<?php echo e($year); ?>" min="2020" max="<?php echo e(now()->year); ?>">
      </div>
      <div class="col-md-3">
        <button class="btn btn-primary w-100">Terapkan</button>
      </div>
      <div class="col-md-3 text-end">
        <div class="small text-muted">Periode:</div>
        <div class="fw-semibold"><?php echo e($start->format('d M Y')); ?> &mdash; <?php echo e($end->format('d M Y')); ?></div>
      </div>
    </div>
  </form>

  
  <?php
    $total           = (int)($total           ?? 0);
    $selesai         = (int)($selesai         ?? 0);
    $onprogress      = (int)($onprogress      ?? 0);
    $waiting         = (int)($waiting         ?? 0);
    $closed_rejected = (int)($closed_rejected ?? 0);
    $pct             = (float)($pct           ?? ($total ? ($selesai/$total*100) : 0));
    $ranking         = ($ranking ?? collect());
    $timelineLabels  = ($timelineLabels ?? collect());
    $timelinePct     = ($timelinePct ?? collect());
  ?>

  <div class="row g-3 mb-3">
    <div class="col-md-3">
      <div class="card shadow-sm h-100"><div class="card-body">
        <div class="text-muted small">Total Ticket</div>
        <div class="h4 mb-0"><?php echo e(number_format($total)); ?></div>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm h-100"><div class="card-body">
        <div class="text-muted small">Selesai</div>
        <div class="h4 mb-0"><?php echo e(number_format($selesai)); ?></div>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm h-100"><div class="card-body">
        <div class="text-muted small">On Progress</div>
        <div class="h4 mb-0"><?php echo e(number_format($onprogress)); ?></div>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm h-100"><div class="card-body">
        <div class="text-muted small">% Selesai</div>
        <div class="h4 mb-0"><?php echo e(number_format($pct, 1)); ?>%</div>
      </div></div>
    </div>
  </div>

  
  <?php $top = $ranking->first(); ?>
  <?php if($total > 0): ?>
  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-start gap-2">
            <div style="font-size:28px;line-height:1">👑</div>
            <div>
              <div class="text-muted small">Unit Teratas (Ticket)</div>
              <div class="h5 mb-1"><?php echo e($top->unit ?? '-'); ?></div>
              <div class="fw-semibold"><?php echo e(number_format($top->total ?? 0)); ?> Ticket</div>
            </div>
          </div>
          <div class="text-muted small mt-3">Periode <?php echo e($start->isoFormat('MMMM Y')); ?></div>
        </div>
      </div>
    </div>

    <div class="col-md-5">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h6 class="mb-2">Ticket per Unit</h6>
          <canvas id="chartRanking" height="140" aria-label="Grafik Ticket per Unit" role="img"></canvas>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h6 class="mb-2">Komposisi Status</h6>
          <canvas id="chartDonut" height="140" aria-label="Donut Status Ticket" role="img"></canvas>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  
  <div class="card shadow-sm">
    <div class="card-body">
      <h6 class="mb-3">Ranking Unit (terbanyak ticket)</h6>
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th style="width:60px;">#</th>
              <th>Unit</th>
              <th class="text-end">Total Ticket</th>
              <th class="text-end">Selesai</th>
              <th class="text-end">% Selesai</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $ranking; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr class="<?php echo e($i === 0 ? 'table-success row-top' : ''); ?>">
                <td><?php echo e($i+1); ?></td>
                <td>
                  <?php if($i === 0): ?><span class="badge bg-warning text-dark me-1">Top</span><?php endif; ?>
                  <?php echo e($r->unit); ?>

                </td>
                <td class="text-end fw-semibold"><?php echo e(number_format($r->total)); ?></td>
                <td class="text-end"><?php echo e(number_format($r->done)); ?></td>
                <td class="text-end"><?php echo e(number_format($r->pct,1)); ?>%</td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr><td colspan="5" class="text-center text-muted">Tidak ada data pada periode ini.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  
  <div class="card shadow-sm mt-3">
    <div class="card-body">
      <h6 class="mb-2">Timeline % Selesai (6 bulan terakhir)</h6>
      <canvas id="chartTimeline" height="110" aria-label="Line Chart Timeline % Selesai" role="img"></canvas>
      <div class="text-muted small mt-2">Menunjukkan persentase ticket yang selesai tiap bulan.</div>
    </div>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .row-top td { border-top-width: 2px; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // ===== Data dari Controller (fallback aman) =====
  const labels   = <?php echo json_encode(($ranking ?? collect())->pluck('unit'), 15, 512) ?>;
  const dataBar  = <?php echo json_encode(($ranking ?? collect())->pluck('total'), 15, 512) ?>;
  const compLbls = ['Selesai','On Progress','Menunggu/Disetujui','Ditolak/Closed'];
  const compVals = [
    <?php echo e((int) ($selesai ?? 0)); ?>,
    <?php echo e((int) ($onprogress ?? 0)); ?>,
    <?php echo e((int) ($waiting ?? 0)); ?>,
    <?php echo e((int) ($closed_rejected ?? 0)); ?>,
  ];
  const tlLabels = <?php echo json_encode($timelineLabels ?? [], 15, 512) ?>;
  const tlPct    = <?php echo json_encode($timelinePct ?? [], 15, 512) ?>;

  // ===== Bar Chart: Ranking Unit =====
  const elBar = document.getElementById('chartRanking');
  if (elBar && dataBar && dataBar.length) {
    new Chart(elBar.getContext('2d'), {
      type: 'bar',
      data: { labels, datasets: [{ label: 'Total Ticket', data: dataBar, borderWidth: 1 }] },
      options: {
        responsive: true,
        scales: {
          y: { beginAtZero: true, ticks: { precision: 0 } },
          x: { ticks: { autoSkip: true, maxRotation: 45 } }
        },
        plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } }
      }
    });
  }

  // ===== Donut: Komposisi Status =====
  const elDonut = document.getElementById('chartDonut');
  if (elDonut) {
    new Chart(elDonut.getContext('2d'), {
      type: 'doughnut',
      data: { labels: compLbls, datasets: [{ data: compVals }] },
      options: { responsive: true, plugins: { legend: { position: 'bottom' } }, cutout: '65%' }
    });
  }

  // ===== Line Chart: Timeline % Selesai =====
  const elTl = document.getElementById('chartTimeline');
  if (elTl) {
    new Chart(elTl.getContext('2d'), {
      type: 'line',
      data: {
        labels: tlLabels,
        datasets: [{ label: '% Selesai', data: tlPct, tension: 0.3, borderWidth: 2, pointRadius: 3 }]
      },
      options: {
        responsive: true,
        scales: { y: { min: 0, max: 100, ticks: { stepSize: 20, callback: v => v + '%' } } },
        plugins: { legend: { display: true }, tooltip: { callbacks: { label: ctx => ctx.parsed.y + '%' } } }
      }
    });
  }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\project_form\resources\views/laporan/service_bulanan.blade.php ENDPATH**/ ?>