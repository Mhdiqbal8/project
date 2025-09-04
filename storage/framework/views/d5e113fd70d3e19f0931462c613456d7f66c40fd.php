

<?php $__env->startSection('content'); ?>
<style>
  /* ====== Soft Theme & Readability ====== */
  :root{
    --bg-soft: #f7f9fc;
    --card: #ffffff;
    --text: #1f2937;
    --muted: #6b7280;
    --primary: #2563eb;
    --primary-600:#1d4ed8;
    --success:#16a34a;
    --warning:#f59e0b;
    --danger:#dc2626;
    --info:#0ea5e9;
    --ring:#e5e7eb;
  }

  body{ background: var(--bg-soft); }
  .container{ max-width: 1050px; }

  /* Typography lebih besar & ramah mata */
  h3,h4,h5{ color: var(--text); letter-spacing:.2px }
  .lead-small{ font-size: 1.05rem; color: var(--muted); }
  .soft-text{ color: var(--muted); }

  /* Card modern */
  .card-soft{
    background: var(--card);
    border: 1px solid var(--ring);
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0,0,0,.04);
  }

  /* Badge lembut */
  .badge{
    font-weight: 600;
    border-radius: 999px;
    padding:.4rem .7rem;
    letter-spacing:.2px;
  }
  .badge-soft-success{ background: #e8f8ee; color: var(--success); border:1px solid #d2f1dd }
  .badge-soft-warning{ background: #fff4e5; color: #b45309; border:1px solid #ffe8c7 }
  .badge-soft-info{ background: #e8f6ff; color: #0369a1; border:1px solid #cfeaff }
  .badge-soft-secondary{ background:#eef2f7; color:#4b5563; border:1px solid #e5e7eb }

  .badge-status-warning{ background: #fff4e5; color:#7c4a00; border:1px solid #ffe1b3 }
  .badge-status-info{ background: #e8f6ff; color:#035b91; border:1px solid #cfeaff }
  .badge-status-success{ background:#e9fbf0; color:#0f7a34; border:1px solid #c7f1d8 }
  .badge-status-secondary{ background:#eef2f7; color:#4b5563; border:1px solid #e5e7eb }

  /* Action bar */
  .action-bar{
    display:flex; gap:.5rem; align-items:center; flex-wrap:wrap;
  }

  /* Section header */
  .section-title{
    display:flex; align-items:center; gap:.6rem; margin-bottom:.75rem;
    font-weight:700; font-size:1.1rem;
  }

  /* List kronologis */
  .kron-item{ display:flex; justify-content:space-between; align-items:center; gap:1rem }
  .kron-item .meta{ font-size:.92rem; color:var(--muted) }

  /* Unit modal */
  .unit-box{ max-height: 420px; overflow:auto; border:1px dashed var(--ring); border-radius:12px; padding: .75rem; }

  /* Subtle divider */
  .divider{ height:1px; background:#edf0f4; margin:1rem 0 }

  /* Spacing helpers */
  .gap-xs>*{ margin-right:.4rem; margin-bottom:.4rem }
  .rounded-xl{ border-radius: 16px !important; }

  /* Buttons tone */
  .btn-primary{
    background: var(--primary);
    border-color: var(--primary);
  }
  .btn-primary:hover{ background: var(--primary-600); border-color: var(--primary-600) }

  /* Make icons a little bigger for seniors */
  .icon-lg{ font-size:1.1rem }

  /* ——— Fix ukuran tombol Download PDF ——— */
.btn-download{
  padding:.35rem .8rem;     /* lebih ramping */
  font-size:.86rem;         /* teks sedikit lebih kecil */
  font-weight:600;
  border-radius:.55rem;     /* sudut halus */
  display:inline-flex;
  align-items:center;
  gap:.4rem;                /* jarak icon-teks */
  line-height:1.1;
}
.btn-download i{ font-size:.95rem }

</style>

<div class="container mt-4">

  
  <?php if(session('success')): ?>
    <div class="alert alert-success rounded-xl shadow-sm"><?php echo e(session('success')); ?></div>
  <?php endif; ?>
  <?php if(session('error')): ?>
    <div class="alert alert-danger rounded-xl shadow-sm"><?php echo e(session('error')); ?></div>
  <?php endif; ?>

  
  <div class="d-flex justify-content-between align-items-start mb-3">
    <div>
      <h3 class="fw-bold mb-1">Detail Form BAP</h3>
      <div class="lead-small">Ringkasan status & tindakan</div>
    </div>
    <div class="action-bar">
      <a href="<?php echo e(route('bap.index')); ?>" class="btn btn-outline-secondary btn-sm rounded-xl">
        <i class="fas fa-arrow-left mr-1 icon-lg"></i> Kembali
      </a>
      <?php $authUser = auth()->user(); ?>
      <?php
        $isMutu = function_exists('isUserMutu')
          ? isUserMutu()
          : ($authUser?->hasRole('mutu') ?? false);

        $isIT = (method_exists($authUser,'isIT') && $authUser->isIT())
             || (method_exists($authUser,'hasRole') && $authUser->hasRole('it'))
             || strtolower(optional($authUser->unit)->nama ?? optional($authUser->unit)->nama_unit ?? '') === 'it'
             || (method_exists($authUser,'hasAccess') && $authUser->hasAccess('approve_it'));

        $mutuAcc = (bool) ($form->mutu_approved_at);

        $status = $form->status ?? '-';
        $statusClass = match (true) {
            str_contains(strtolower($status), 'selesai') => 'badge-status-success',
            str_contains(strtolower($status), 'menunggu it') => 'badge-status-info',
            str_contains(strtolower($status), 'menunggu unit') => 'badge-status-warning',
            default => 'badge-status-secondary'
        };

        $canSeeMutuRead = $isMutu || $isIT;
      ?>
     <?php if (! ($isMutu)): ?>
  <a href="<?php echo e(route('bap.cetak', $form->id)); ?>"
     class="btn btn-primary btn-download" target="_blank">
    <i class="fas fa-file-download"></i> Download PDF
  </a>
<?php endif; ?>

    </div>
  </div>

  
  <?php if($isIT && !$mutuAcc): ?>
    <div class="alert alert-warning rounded-xl d-flex align-items-center">
      <i class="fas fa-lock mr-2"></i>
      <div><strong>Menunggu ACC Mutu.</strong> Kronologis akan dapat diakses IT setelah Mutu menyetujui BAP ini.</div>
    </div>
  <?php endif; ?>

  <?php if($isMutu): ?>
    <?php if($form->manager_approved_at): ?>
      <div class="mb-3 d-flex gap-xs">
        <button class="btn btn-sm btn-outline-info rounded-xl" data-toggle="modal" data-target="#tagUnitModal">
          🔖 Tag Unit
        </button>
        <?php if($form->taggedUnits->count()): ?>
          <form action="<?php echo e(route('bap.accMutu', $form->id)); ?>" method="POST" class="d-inline">
            <?php echo csrf_field(); ?>
            <button class="btn btn-sm btn-success rounded-xl">✅ ACC Mutu</button>
          </form>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-warning rounded-xl mb-3">
        Menunggu approval dari Manager sebelum bisa melakukan <strong>Tag Unit</strong>.
      </div>
    <?php endif; ?>
  <?php else: ?>
    <?php echo $__env->make('sistem_sdm.bap.partials.approval-buttons', ['form' => $form, 'user' => $authUser, 'units' => $units], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <?php endif; ?>

  
  <div class="card card-soft p-4 mb-4">
    <div class="section-title">
      📌 <span>Informasi Umum</span>
    </div>

    
    <div class="mb-3 d-flex flex-wrap gap-xs">
      <span class="badge <?php echo e($statusClass); ?>"><?php echo e($status); ?></span>
      <?php if($mutuAcc): ?>
        <span class="badge badge-soft-success">ACC Mutu</span>
      <?php else: ?>
        <span class="badge badge-soft-warning">Belum ACC Mutu</span>
      <?php endif; ?>
    </div>

    <div class="row">
      <div class="col-md-6 mb-2">
        <div class="soft-text">Dibuat oleh</div>
        <div class="h6 mb-0"><?php echo e(optional($form->creator)->nama ?? '-'); ?></div>
      </div>
      <div class="col-md-6 mb-2">
        <div class="soft-text">Jabatan</div>
        <div class="h6 mb-0"><?php echo e(optional(optional($form->creator)->jabatan)->nama ?? '-'); ?></div>
      </div>
      <div class="col-md-6 mb-2">
        <div class="soft-text">Tanggal Dibuat</div>
        <div class="h6 mb-0"><?php echo e($form->created_at->format('d-m-Y H:i')); ?></div>
      </div>
      <div class="col-md-6 mb-2">
        <div class="soft-text">Divisi Tujuan</div>
        <div class="h6 mb-0"><?php echo e($form->divisi_verifikasi); ?></div>
      </div>
    </div>

    <div class="divider"></div>

    <div class="section-title">🔧 <span>Perbaikan Sistem</span></div>
    <?php
      $perbaikan = is_array($form->perbaikan) ? $form->perbaikan : json_decode($form->perbaikan ?? '[]', true);
    ?>
    <?php if(!empty($perbaikan)): ?>
      <ul class="mb-3">
        <?php $__currentLoopData = $perbaikan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li class="mb-1"><?php echo e($item); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    <?php else: ?>
      <p class="soft-text mb-3">Tidak ada item perbaikan.</p>
    <?php endif; ?>

    <div class="row">
      <div class="col-md-4"><div class="soft-text">Tindakan Medis</div><div><?php echo e($form->tindakan_medis ?? '-'); ?></div></div>
      <div class="col-md-4"><div class="soft-text">Lain-lain</div><div><?php echo e($form->lain_lain ?? '-'); ?></div></div>
      <div class="col-md-4"><div class="soft-text">Permasalahan Lain</div><div><?php echo e($form->permasalahan_lain ?? '-'); ?></div></div>
    </div>

    <div class="divider"></div>

    <div class="section-title">✅ <span>Verifikasi & Penyelesaian</span></div>
    <?php if($form->kepala_unit_approved_at): ?>
      <p class="mb-1">
        <span class="soft-text">Kepala Unit:</span>
        <strong><?php echo e(optional($form->kepalaUnitUser)->nama); ?></strong>
        <span class="soft-text">(<?php echo e(optional(optional($form->kepalaUnitUser)->jabatan)->nama); ?>)</span>
      </p>
    <?php elseif($form->supervision_approved_at): ?>
      <p class="mb-1">
        <span class="soft-text">Supervision:</span>
        <strong><?php echo e(optional($form->supervisionUser)->nama); ?></strong>
        <span class="soft-text">(<?php echo e(optional(optional($form->supervisionUser)->jabatan)->nama); ?>)</span>
      </p>
    <?php endif; ?>
    <p class="mb-1">
      <span class="soft-text">Manager:</span>
      <strong><?php echo e(optional($form->managerUser)->nama ?? '-'); ?></strong>
      <span class="soft-text">(<?php echo e(optional(optional($form->managerUser)->jabatan)->nama ?? 'Manager'); ?>)</span>
    </p>
    <?php
      function showTtd($user) {
          $ttd = optional($user)->ttd_path;
          return $ttd && file_exists(public_path('storage/' . $ttd))
              ? '<img src="' . asset('storage/' . $ttd) . '" height="60">'
              : '<em class="soft-text">(TTD tidak tersedia)</em>';
      }
      $finalReady =
        $form->final_user_id &&
        $form->final_approved_at &&
        $form->finalUser &&
        $form->finalUser->ttd_path &&
        file_exists(public_path('storage/' . $form->finalUser->ttd_path));
      $finalUserIsCreator = $form->final_user_id === $form->user_id;
    ?>
    <!-- <p class="mb-0">
      <span class="soft-text">Finalisasi:</span>
      <?php if($finalReady): ?>
        <strong><?php echo e(optional($form->finalUser)->nama); ?></strong>
        <span class="soft-text">(<?php echo e(optional(optional($form->finalUser)->jabatan)->nama ?? '-'); ?>)</span>
      <?php else: ?>
        <em class="soft-text">- (Unit Terkait)</em>
      <?php endif; ?>
    </p> -->
  </div>

  
  <?php
    use Illuminate\Support\Facades\Gate;
    $visibleKron = $form->kronologis->filter(function($k) use ($authUser) {
      return Gate::forUser($authUser)->allows('view-kronologis', $k);
    })->values();
  ?>

  <div class="card card-soft p-4 mb-5">
    <div class="d-flex align-items-center justify-content-between mb-2">
      <div class="section-title">📝 <span>Kronologis</span>
        <?php if($isIT && !$mutuAcc): ?>
          <small class="soft-text ml-1"><i class="fas fa-lock mx-1"></i>(Terkunci untuk IT sampai ACC Mutu)</small>
        <?php endif; ?>
      </div>

      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('write-kronologis', $form)): ?>
        <?php if(!$form->final_approved_at): ?>
          <a href="<?php echo e(route('kronologis.create', $form->id)); ?>"
             class="btn btn-sm btn-outline-primary rounded-xl">
            + Tambah Kronologis
          </a>
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <?php if($visibleKron->count()): ?>
      <ul class="list-group list-group-flush">
        <?php $__currentLoopData = $visibleKron; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kron): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li class="list-group-item kron-item">
            <div>
              <a href="<?php echo e(route('kronologis.view', $kron->id)); ?>" class="text-dark text-decoration-none">
                <?php if($kron->tipe_kronologis === 'Medis'): ?>
                  🧑 <strong><?php echo e($kron->nama_pasien ?? '-'); ?></strong> | RM: <?php echo e($kron->no_rm ?? '-'); ?> |
                <?php else: ?>
                  🧾 <strong><?php echo e($kron->judul); ?></strong> |
                <?php endif; ?>
                Tgl: <?php echo e(optional($kron->created_at)->format('d-m-Y')); ?>

              </a>
              <div class="meta">✍️ Dibuat oleh: <strong><?php echo e($kron->creator->nama ?? '-'); ?></strong></div>
            </div>

            <div class="d-flex align-items-center gap-xs">
              <span class="badge badge-soft-success">SUDAH ISI</span>

              <?php if($canSeeMutuRead): ?>
                <span class="badge <?php echo e($kron->mutu_checked_at ? 'badge-soft-info' : 'badge-soft-secondary'); ?>">
                  <?php echo e($kron->mutu_checked_at ? 'DIBACA MUTU' : 'BELUM DIBACA MUTU'); ?>

                </span>
              <?php endif; ?>

              <?php if(
                !$isMutu
                && ($kron->user_id == auth()->id())
                && is_null($kron->mutu_checked_at)
                && (($kron->status ?? 'Pending') === 'Pending')
              ): ?>
                <a href="<?php echo e(route('kronologis.edit', $kron->id)); ?>"
                   class="btn btn-sm btn-outline-warning rounded-xl" title="Edit">
                  <i class="fas fa-pen"></i>
                </a>
              <?php endif; ?>
            </div>
          </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    <?php else: ?>
      <p class="soft-text mb-0">
        <?php if($isIT && !$mutuAcc): ?>
          Belum bisa ditampilkan untuk IT. Menunggu ACC Mutu.
        <?php else: ?>
          Belum ada kronologis yang bisa Anda lihat.
        <?php endif; ?>
      </p>
    <?php endif; ?>
  </div>

</div>


<?php
  $unitsSorted = ($units ?? collect())->sortBy('nama_unit', SORT_NATURAL | SORT_FLAG_CASE);
?>

<div class="modal fade" id="tagUnitModal" tabindex="-1" aria-labelledby="tagUnitModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form action="<?php echo e(route('bap.tagUnit', $form->id)); ?>" method="POST">
      <?php echo csrf_field(); ?>
      <div class="modal-content rounded-xl">
        <div class="modal-header">
          <h5 class="modal-title" id="tagUnitModalLabel">🔖 Tag Unit Terkait</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <p class="mb-2">
            Pilih unit-unit yang wajib mengisi kronologis untuk form ini.
            <br><small class="soft-text">Perubahan akan direkam di Riwayat Tag (siapa & kapan).</small>
          </p>

          <div class="unit-box">
            <div class="row g-2 row-cols-1 row-cols-sm-2 row-cols-md-3">
              <?php $__empty_1 = true; $__currentLoopData = $unitsSorted; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col mb-2">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="unit_ids[]" value="<?php echo e($unit->id); ?>" id="unit<?php echo e($unit->id); ?>"
                           <?php echo e($form->taggedUnits->contains('id', $unit->id) ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="unit<?php echo e($unit->id); ?>"><?php echo e($unit->nama_unit); ?></label>
                  </div>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col"><em class="soft-text">Tidak ada data unit.</em></div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary rounded-xl" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary rounded-xl">Simpan Tag</button>
        </div>
      </div>
    </form>
  </div>
</div>


<div class="modal fade" id="tagLogModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content rounded-xl">
      <div class="modal-header">
        <h5 class="modal-title">🕘 Riwayat Tag Unit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead>
              <tr>
                <th style="width:130px">Waktu</th>
                <th style="width:90px">Aksi</th>
                <th>Unit</th>
                <th>Oleh</th>
              </tr>
            </thead>
            <tbody id="tagLogTableBody">
              <tr><td colspan="4" class="soft-text">Memuat…</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary btn-sm rounded-xl" data-dismiss="modal">Tutup</button>
        <button class="btn btn-outline-primary btn-sm rounded-xl" id="reloadTagLogs">Muat ulang</button>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function(){
  const tbody  = document.getElementById('tagLogTableBody');
  const reload = document.getElementById('reloadTagLogs');
  const modal  = document.getElementById('tagLogModal');
  let loaded   = false;

  async function loadLogs(force=false){
    if(loaded && !force) return;
    tbody.innerHTML = '<tr><td colspan="4" class="soft-text">Memuat…</td></tr>';
    try {
      const url = `<?php echo e(route('bap.tagLogs', $form->id)); ?>`;
      const res = await fetch(url, { headers:{'X-Requested-With':'XMLHttpRequest'} });
      const { data } = await res.json();
      if(Array.isArray(data) && data.length){
        tbody.innerHTML = data.map(r => `
          <tr>
            <td>${r.time ?? '-'}</td>
            <td><span class="badge ${r.action==='ADD'?'badge-soft-success':'badge-soft-secondary'}">${r.action}</span></td>
            <td>${r.unit ?? '-'}</td>
            <td>${r.by ?? '-'}</td>
          </tr>
        `).join('');
      }else{
        tbody.innerHTML = '<tr><td colspan="4" class="soft-text">Belum ada riwayat.</td></tr>';
      }
      loaded = true;
    } catch(e){
      console.error(e);
      tbody.innerHTML = '<tr><td colspan="4" class="text-danger">Gagal memuat data.</td></tr>';
    }
  }

  $('#tagLogModal').on('shown.bs.modal', () => loadLogs());
  if(reload){ reload.addEventListener('click', ()=>{ loaded=false; loadLogs(true); }); }
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\project_form\resources\views/sistem_sdm/bap/detail_bap.blade.php ENDPATH**/ ?>