<?php
    $startNumber = ($formHistories->currentPage() - 1) * $formHistories->perPage();
?>



<?php $__env->startSection('content'); ?>
<style>
  /* ===== Soft Theme ===== */
  :root{
    --bg-soft:#f7f9fc;
    --card:#ffffff;
    --text:#1f2937;
    --muted:#6b7280;
    --primary:#2563eb;
    --primary-600:#1d4ed8;
    --ring:#e5e7eb;
    --success:#16a34a;
    --warning:#f59e0b;
    --info:#0ea5e9;
  }

  body { background: var(--bg-soft); }
  /* Full width container */
  .container{ max-width: 100% !important; }

  /* Heading */
  .page-title{
    display:flex; align-items:center; gap:.6rem;
    color:#065f46; margin-bottom:.2rem
  }
  .page-sub{ color:var(--muted); margin-bottom:1rem }

  /* Card */
  .card-soft{
    background:var(--card);
    border:1px solid var(--ring);
    border-radius:16px;
    box-shadow:0 8px 24px rgba(0,0,0,.04);
  }

  /* Tabs */
  .nav-tabs{ border:0 }
  .nav-tabs .nav-link{
    border:none; color:var(--muted); font-weight:600;
    border-radius:999px; padding:.45rem .9rem;
  }
  .nav-tabs .nav-link.active{
    color:#0f172a; background:#eef6ff; border:1px solid #dbeafe;
  }

  /* Rekap bar */
  .rekap{
    display:flex; justify-content:space-between; align-items:center; gap:1rem;
    font-size:.95rem; color:#0f172a;
  }
  .rekap .meta{ color:var(--muted) }

  /* Filters */
  .filter .form-control{
    border-radius:999px; border:1px solid var(--ring); box-shadow:none;
  }
  .filter .btn-primary{
    background:var(--primary); border-color:var(--primary);
    border-radius:999px; font-weight:600;
  }
  .filter .btn-primary:hover{ background:var(--primary-600); border-color:var(--primary-600) }
  .btn-reset{ border-radius:999px }

  /* ===== Table: no horizontal scroll, fit all ===== */
  .table-wrap{ border-radius:14px; overflow:hidden }
  .table{
    margin:0;
    table-layout: fixed;     /* Paksa tiap kolom ikut bagi ruang */
    width: 100%;
    font-size: .92rem;       /* Sedikit kecil biar muat */
  }
  .table thead th{
    background:#f3f6fb !important;
    color:#0f172a;
    border-bottom:1px solid var(--ring)!important;
    vertical-align:middle;
    padding:.55rem .6rem;
    font-weight:700;
    white-space: normal;
  }
  .table tbody td{
    vertical-align:top;
    padding:.55rem .6rem;
    white-space: normal;     /* IJINKAN WRAP */
    word-break: break-word;  /* Patahkan kalau kepanjangan */
    overflow-wrap: anywhere; /* Force wrap kalau perlu */
  }

  /* Kolom spesifik: sempitkan angka & aksi, biar kolom judul dapat ruang */
  th.col-no, td.col-no { width: 46px; text-align:center; }
  th.col-aksi, td.col-aksi { width: 110px; text-align:center; }
  th.col-tgl,  td.col-tgl  { width: 110px; text-align:center; }
  th.col-status, td.col-status { width: 130px; text-align:center; }
  /* kolom judul fleksibel, biar ambil sisa lebar */
  .col-judul{ white-space: normal !important; }

  /* Badges soft & ramping */
  .badge{ border-radius:999px; font-weight:600; padding:.28rem .5rem; }
  .b-soft-success{ background:#e8f8ee; color:#0f7a34; border:1px solid #d2f1dd }
  .b-soft-info{ background:#e8f6ff; color:#035b91; border:1px solid #cfeaff }
  .b-soft-primary{ background:#eef6ff; color:#0b4bc2; border:1px solid #dbeafe }
  .b-soft-warning{ background:#fff4e5; color:#7c4a00; border:1px solid #ffe1b3 }
  .b-soft-secondary{ background:#eef2f7; color:#374151; border:1px solid #e5e7eb }
  .b-dark{ background:#111827; color:#f9fafb }

  /* Action buttons compact */
  .btn-circle{ border-radius:999px; padding:.3rem .48rem; line-height:1; }
  .btn-circle i{ font-size:.85rem }

  /* Pagination info */
  .paging-info{ color:var(--muted); font-size:.9rem }

  /* Compact tweaks per breakpoint untuk cegah scroll */
  @media (max-width: 1600px){
    .table{ font-size:.90rem }
  }
  @media (max-width: 1366px){
    .table{ font-size:.88rem }
    th.col-aksi, td.col-aksi { width: 100px; }
  }
  @media (max-width: 1200px){
    .table{ font-size:.86rem }
    th.col-status, td.col-status { width: 120px; }
    th.col-tgl, td.col-tgl { width: 100px; }
  }
  @media (max-width: 992px){
    .table{ font-size:.84rem }
    .btn-circle{ padding:.28rem .44rem }
  }

  /* PDF action: outline merah biar kontras */
.btn-outline-danger.btn-circle{ border-width:2px; }
.btn-outline-danger.btn-circle:hover{ background:#dc2626; color:#fff; }
/* biar ikon ngikut warna tombol (bukan dipaksa putih) */
.btn-circle i{ color: inherit; }
</style>

<div class="container mt-4">

  <h3 class="page-title">
    <i class="fas fa-file-alt text-success"></i>
    Formulir BAP & Kronologis
  </h3>
  <!-- <div class="page-sub">Kelola dan pantau pengajuan dengan tampilan yang nyaman & tanpa geser.</div> -->

  <!-- Tabs Navigasi -->
  <ul class="nav nav-tabs mb-3">
    <?php if (! (auth()->user()->hasRole('mutu'))): ?>
      <li class="nav-item">
        <a class="nav-link <?php echo e(request()->is('bap/form-bap') ? 'active' : ''); ?>" href="<?php echo e(route('bap.form_bap')); ?>">
          📝 TAMBAH BAP
        </a>
      </li>
    <?php endif; ?>
  </ul>

  <!-- Statistik Rekap -->
  <div class="card-soft p-3 mb-3">
    <div class="rekap">
      <div>
        <strong>Total Hari Ini:</strong> <?php echo e($totalToday); ?>

        <span class="mx-2">|</span>
        <strong>Selesai:</strong> <?php echo e($totalSelesai); ?>

        <span class="mx-2">|</span>
        <strong>Pending:</strong> <?php echo e($totalPending); ?>

      </div>
      <div class="meta">
        Update terakhir: <?php echo e(now()->format('d/m/Y H:i')); ?>

      </div>
    </div>
  </div>

  <!-- Filter -->
  <form method="GET" action="<?php echo e(route('bap.index')); ?>" class="row g-2 align-items-center mb-4 filter">
    <div class="col-6 col-md-2">
      <input type="date" name="start_date" class="form-control" value="<?php echo e(request('start_date')); ?>">
    </div>
    <div class="col-6 col-md-2">
      <input type="date" name="end_date" class="form-control" value="<?php echo e(request('end_date')); ?>">
    </div>
    <div class="col-12 col-md-3">
      <input type="text" name="keyword" class="form-control" placeholder="🔍 Cari judul / user..." value="<?php echo e(request('keyword')); ?>">
    </div>
    <div class="col-6 col-md-2">
      <select name="status" class="form-control">
        <option value="">-- Semua Status --</option>
        <option value="Dibuat" <?php echo e(request('status') == 'Dibuat' ? 'selected' : ''); ?>>Dibuat</option>
        <option value="Disetujui Unit" <?php echo e(request('status') == 'Disetujui Unit' ? 'selected' : ''); ?>>Disetujui Kepala Unit</option>
        <option value="Disetujui Supervision" <?php echo e(request('status') == 'Disetujui Supervision' ? 'selected' : ''); ?>>Disetujui Supervision</option>
        <option value="Disetujui Manager" <?php echo e(request('status') == 'Disetujui Manager' ? 'selected' : ''); ?>>Disetujui Manager</option>
        <option value="Selesai" <?php echo e(request('status') == 'Selesai' ? 'selected' : ''); ?>>Selesai</option>
      </select>
    </div>
    <div class="col-6 col-md-2">
      <select name="unit_id" id="filter-unit" class="form-control">
        <option value="">-- Semua Unit --</option>
        <?php $__currentLoopData = ($units ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($u->id); ?>" <?php echo e((string)$u->id === (string)request('unit_id') ? 'selected' : ''); ?>><?php echo e($u->nama_unit); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div class="col-6 col-md-1 d-grid">
      <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i> Filter</button>
    </div>
    <?php if(request()->hasAny(['start_date','end_date','keyword','status','unit_id'])): ?>
      <div class="col-12 mt-1">
        <a href="<?php echo e(route('bap.index')); ?>" class="btn btn-sm btn-outline-secondary btn-reset">Reset</a>
      </div>
    <?php endif; ?>
  </form>

  <!-- Tabel Data -->
  <div class="table-wrap card-soft">
    <table class="table table-striped table-bordered table-sm align-middle">
      <thead class="text-center">
        <tr>
          <th class="col-no">No</th>
          <th>Dibuat Oleh</th>
          <th>Divisi Verifikasi</th>
          <th class="col-judul">Judul / Pasien</th>
          <th class="col-tgl">Tanggal Dibuat</th>
          <th class="col-status">Status</th>
          <th>Terakhir Disetujui Oleh</th>
          <th class="col-aksi">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $formHistories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td class="col-no"><?php echo e($loop->iteration + $startNumber); ?></td>
            <td><?php echo e($history->creator->nama ?? '-'); ?></td>
            <td class="text-center"><?php echo e($history->divisi_verifikasi ?? '-'); ?></td>
            <td class="text-start col-judul"><?php echo e($history->judul ?? '-'); ?></td>
            <td class="text-center col-tgl"><?php echo e($history->created_at ? \Carbon\Carbon::parse($history->created_at)->format('d/m/Y') : '-'); ?></td>
            <td class="text-center col-status">
              <?php
                $statusLabel = $history->status ?? 'Pending';
                $badgeClass = 'b-soft-secondary';
                if (str_contains($statusLabel, 'Selesai'))        $badgeClass = 'b-soft-success';
                elseif (str_contains($statusLabel, 'Dikerjakan')) $badgeClass = 'b-soft-primary';
                elseif (str_contains($statusLabel, 'Disetujui'))  $badgeClass = 'b-soft-info';
                elseif (str_contains($statusLabel, 'Dibuat'))     $badgeClass = 'b-soft-warning';
                elseif (str_contains($statusLabel, 'Pending'))    $badgeClass = 'b-soft-warning';
              ?>
              <span class="badge <?php echo e($badgeClass); ?>"><?php echo e($statusLabel); ?></span>
            </td>
            <td class="text-center">
              <?php if(!empty($history->latest_approval)): ?>
                <?php
                  $badgeColor = 'b-soft-success';
                  if (str_contains($history->latest_approval, 'Unit Terkait'))  $badgeColor = 'b-dark';
                  elseif (str_contains($history->latest_approval, 'Manager'))   $badgeColor = 'b-soft-primary';
                  elseif (str_contains($history->latest_approval, 'Kepala Unit')) $badgeColor = 'b-soft-info';
                  elseif (str_contains($history->latest_approval, 'Supervision')) $badgeColor = 'b-soft-warning';

                  $matches = [];
                  preg_match('/^(.*\)) (.*)$/', $history->latest_approval, $matches);
                  $namaJabatan = $matches[1] ?? $history->latest_approval;
                  $tanggalJam  = $matches[2] ?? null;
                ?>
                <span class="badge <?php echo e($badgeColor); ?> text-wrap d-inline-block" style="line-height:1.2; white-space:normal;">
                  <?php echo e($namaJabatan); ?>

                  <?php if($tanggalJam): ?>
                    <br><small class="text-muted"><?php echo e($tanggalJam); ?></small>
                  <?php endif; ?>
                </span>
              <?php else: ?>
                <span class="text-muted">Belum ada approval</span>
              <?php endif; ?>
            </td>
            <td class="col-aksi">
              <a href="<?php echo e(route('bap.detail', $history->id)); ?>" class="btn btn-info btn-circle mb-1" title="Lihat">
                <i class="fas fa-eye text-white"></i>
              </a>
              <a href="<?php echo e(route('bap.cetak', $history->id)); ?>" target="_blank"
   class="btn btn-outline-danger btn-circle mb-1" title="PDF">
  <i class="fas fa-file-pdf"></i>
</a>
              <?php if(!auth()->user()->hasRole('mutu') && $history->status == 'Pending' && $history->user_id == auth()->id() && user_can('edit_bap')): ?>
                <a href="<?php echo e(route('bap.edit', $history->id)); ?>" class="btn btn-warning btn-circle mb-1" title="Edit">
                  <i class="fas fa-edit text-white"></i>
                </a>
                <form action="<?php echo e(route('bap.destroy', $history->id)); ?>" method="POST" class="d-inline"
                      onsubmit="return confirm('Yakin hapus form ini?')">
                  <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                  <button class="btn btn-danger btn-circle mb-1" title="Hapus"><i class="fas fa-trash text-white"></i></button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="8" class="text-center text-muted py-4">Belum ada data.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="paging-info">
      Menampilkan <?php echo e($formHistories->firstItem()); ?> sampai <?php echo e($formHistories->lastItem()); ?> dari total <?php echo e($formHistories->total()); ?> data
    </div>
    <div>
      <?php echo e($formHistories->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5')); ?>

    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\project_form\resources\views/sistem_sdm/index.blade.php ENDPATH**/ ?>