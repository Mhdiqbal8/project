<?php
  $top = $ranking->first();
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Laporan BAP - <?php echo e($start->isoFormat('MMMM Y')); ?></title>
  <style>
    @page  { margin: 20mm 16mm; }
    body  { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#111; }
    h1,h2,h3,h4,h5 { margin:0 0 8px 0; }
    .muted { color:#666; }
    .grid  { display: table; width:100%; table-layout: fixed; }
    .col   { display: table-cell; vertical-align: top; padding: 8px; }
    .card  { border:1px solid #ddd; border-radius:6px; padding:10px; }
    .kpi   { font-size: 20px; font-weight: 700; }
    .small { font-size: 11px; }
    .mb-2  { margin-bottom: 8px; }
    .mb-3  { margin-bottom: 12px; }
    .mb-4  { margin-bottom: 16px; }
    table { width:100%; border-collapse: collapse; }
    th, td { border:1px solid #ddd; padding:6px 8px; }
    th { background:#f6f7f9; text-align:left; }
    .text-right { text-align: right; }
    .badge { display:inline-block; background:#ffe082; color:#222; padding:2px 6px; border-radius:4px; font-size:10px; vertical-align: middle; }
    .tr-top { background:#f2fbf3; }
    .footer { position: fixed; bottom: -10mm; left:0; right:0; text-align:center; font-size:10px; color:#666; }
  </style>
</head>
<body>

  
  <h2>Laporan BAP per Bulan</h2>
  <div class="muted mb-3">Periode: <?php echo e($start->format('d M Y')); ?> — <?php echo e($end->format('d M Y')); ?></div>

  
  <div class="grid mb-3">
    <div class="col">
      <div class="card">
        <div class="small muted">Total BAP</div>
        <div class="kpi"><?php echo e(number_format($total)); ?></div>
      </div>
    </div>
    <div class="col">
      <div class="card">
        <div class="small muted">Selesai (final)</div>
        <div class="kpi"><?php echo e(number_format($selesai)); ?></div>
      </div>
    </div>
    <div class="col">
      <div class="card">
        <div class="small muted">% Selesai</div>
        <div class="kpi"><?php echo e(number_format($pct,1)); ?>%</div>
      </div>
    </div>
  </div>

  
  <?php if($total > 0 && $top): ?>
    <div class="card mb-3">
      <strong>Unit Teratas:</strong> <?php echo e($top->unit); ?>

      <span class="badge">Top</span>
      <div class="small muted">Total: <?php echo e(number_format($top->total)); ?> BAP (<?php echo e($start->isoFormat('MMMM Y')); ?>)</div>
    </div>
  <?php endif; ?>

  
  <h4 class="mb-2">Ranking Unit (terbanyak)</h4>
  <table class="mb-4">
    <thead>
      <tr>
        <th style="width:36px;">#</th>
        <th>Unit</th>
        <th style="width:120px;" class="text-right">Total BAP</th>
      </tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $ranking; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="<?php echo e($i === 0 ? 'tr-top' : ''); ?>">
          <td><?php echo e($i+1); ?></td>
          <td>
            <?php if($i===0): ?><span class="badge">Top</span> <?php endif; ?>
            <?php echo e($r->unit); ?>

          </td>
          <td class="text-right"><strong><?php echo e(number_format($r->total)); ?></strong></td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="3" class="small muted" style="text-align:center;">Tidak ada data pada periode ini.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="footer">
    Dicetak: <?php echo e(now()->format('d M Y H:i')); ?> · Periode <?php echo e($start->isoFormat('MMMM Y')); ?>

  </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\project_form\resources\views/laporan/bap_pdf.blade.php ENDPATH**/ ?>