<?php
    // ================= Helper =================
    $noBap = $form->no_bap ?? ('BAP#' . $form->id);

    // perbaikan bisa string/json/array
    $perbaikan = is_array($form->perbaikan) ? $form->perbaikan : json_decode($form->perbaikan ?? '[]', true);
    if (!is_array($perbaikan)) $perbaikan = [];

    function fmt($dt) {
        return $dt ? \Carbon\Carbon::parse($dt)->format('d-m-Y H:i') : '-';
    }

    /**
     * Base64 logo dari public/rskk-logo2.png (fallback ke storage)
     */
    function logoImg($file = 'rskk-logo2.png') {
        $paths = [
            public_path($file),
            storage_path('app/public/logo/'.$file),
            storage_path('app/public/'.$file),
        ];
        foreach ($paths as $full) {
            if (is_file($full)) {
                $mime = function_exists('mime_content_type') ? @mime_content_type($full) : 'image/png';
                $data = @file_get_contents($full);
                if ($data !== false) return 'data:'.$mime.';base64,'.base64_encode($data);
            }
        }
        return '';
    }

    /**
     * Base64 TTD: storage/app/public/{ttd_path}
     * contoh: ttd_users/abc.png
     */
    function ttdImg($user) {
        if (!$user) return '';
        $rel = trim($user->ttd_path ?? '');
        if ($rel === '') return '';
        $rel = ltrim(str_replace(['\\','//'], '/', $rel), '/');

        $full = storage_path('app/public/' . $rel);
        if (!is_file($full)) return '';

        $allow = ['image/png', 'image/jpeg', 'image/jpg'];
        $mime  = function_exists('mime_content_type') ? @mime_content_type($full) : null;
        if (!$mime) { $info = @getimagesize($full); $mime = $info['mime'] ?? null; }
        if (!$mime || !in_array(strtolower($mime), $allow, true)) return '';

        $data = @file_get_contents($full);
        if ($data === false) return '';

        return 'data:' . $mime . ';base64,' . base64_encode($data);
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo e($noBap); ?></title>
    <style>
        /* =================== LAYOUT KOMPAK & RAPIH (1 LEMBAR) =================== */
        @page  { margin: 8mm 12mm 8mm 12mm; } /* dirapetin biar muat 1 halaman */
        html, body { margin:0; padding:0; }
        * { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 10px; line-height: 1.35; color:#222; }

        /* Header */
        .header { page-break-inside: avoid; margin-bottom:8px; }
        .title   { font-weight:800; font-size:14px; margin:0 0 2px 0; letter-spacing:.2px; }
        .subtitle{ font-size:10px; margin:0; color:#444; }
        .brand-logo { height:55px; } /* sedikit lebih kecil */
        .divider { height:1px; background:#cfcfcf; margin-top:6px; }

        /* Umum */
        .section     { margin-top:8px; page-break-inside: avoid; }
        .mb4 { margin-bottom:4px; }
        .muted { color:#666; }
        .no-break { page-break-inside: avoid; }

        /* Tabel */
        .table { width:100%; border-collapse: collapse; table-layout: fixed; }
        .table th, .table td { border:0.8px solid #333; padding:5px; vertical-align: top; word-wrap: break-word; }
        .table th { background:#f2f2f2; font-weight:700; }

        /* KV (label-nilai) */
        .kv th { width:32%; }

        /* List Perbaikan */
        .list td:first-child { width:5%; text-align:center; }
        .list td:last-child  { width:95%; }

        /* Grid tanda tangan */
        .grid-2 { width:100%; border-collapse: collapse; table-layout: fixed; page-break-inside: avoid; }
        .grid-2 td { width:50%; border:0.8px solid #333; padding:6px; vertical-align: top; }

        .sign { text-align:center; height:90px; padding-top:6px; }
        .sign img { height:38px; margin:2px 0; }
        .sign-name { margin-top:4px; font-weight:700; }
        .sign-role { font-size:9px; color:#333; }
        .sign-date { font-size:9px; color:#666; margin-top:2px; }
    </style>
</head>
<body>

    
    <div class="header">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="text-align:left; vertical-align:middle; padding-left:4px;">
                    <div class="title">FORM BERITA ACARA PERBAIKAN (BAP)</div>
                    <div class="subtitle">Nomor: <strong><?php echo e($noBap); ?></strong></div>
                </td>
                <td style="text-align:right; width:150px; vertical-align:top; padding-right:4px;">
                    <?php $logo = logoImg(); ?>
                    <?php if($logo): ?>
                        <img class="brand-logo" src="<?php echo e($logo); ?>" alt="RSKK">
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        <div class="divider"></div>
    </div>

    
    <table class="table kv no-break">
        <tr>
            <th>Dibuat oleh</th>
            <td><?php echo e(optional($form->creator)->nama ?? '-'); ?></td>
        </tr>
        <tr>
            <th>Jabatan</th>
            <td><?php echo e(optional(optional($form->creator)->jabatan)->nama ?? '-'); ?></td>
        </tr>
        <tr>
            <th>Tanggal Dibuat</th>
            <td><?php echo e(fmt($form->created_at)); ?></td>
        </tr>
        <tr>
            <th>Divisi Tujuan</th>
            <td><?php echo e($form->divisi_verifikasi ?? '-'); ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?php echo e($form->status ?? '-'); ?></td>
        </tr>
    </table>

    
    <div class="section no-break">
        <div class="mb4"><strong>Perbaikan Sistem</strong></div>
        <?php if(count($perbaikan)): ?>
            <table class="table list">
                <?php $__currentLoopData = $perbaikan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($i+1); ?></td>
                        <td><?php echo e($p); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </table>
        <?php else: ?>
            <table class="table"><tr><td>-</td></tr></table>
        <?php endif; ?>
    </div>

    
    <div class="section no-break">
        <div class="mb4"><strong>Tindakan Medis</strong></div>
        <table class="table"><tr><td><?php echo e($form->tindakan_medis ?? '-'); ?></td></tr></table>
    </div>

    <div class="section no-break">
        <div class="mb4"><strong>Lain-lain</strong></div>
        <table class="table"><tr><td><?php echo e($form->lain_lain ?? '-'); ?></td></tr></table>
    </div>

    <div class="section no-break">
        <div class="mb4"><strong>Permasalahan Lain</strong></div>
        <table class="table"><tr><td><?php echo e($form->permasalahan_lain ?? '-'); ?></td></tr></table>
    </div>

    
    <div class="section no-break">
        <div class="mb4"><strong>Status Verifikasi</strong></div>
        <table class="table kv">
            <tr>
                <th>Kepala Unit</th>
                <td>
                    <?php echo e(optional($form->kepalaUnitUser)->nama ?? '-'); ?>

                    <span class="muted"> | <?php echo e(fmt($form->kepala_unit_approved_at)); ?></span>
                </td>
            </tr>
            <tr>
                <th>Supervision</th>
                <td>
                    <?php echo e(optional($form->supervisionUser)->nama ?? '-'); ?>

                    <span class="muted"> | <?php echo e(fmt($form->supervision_approved_at)); ?></span>
                </td>
            </tr>
            <tr>
                <th>Manager</th>
                <td>
                    <?php echo e(optional($form->managerUser)->nama ?? '-'); ?>

                    <span class="muted"> | <?php echo e(fmt($form->manager_approved_at)); ?></span>
                </td>
            </tr>
            <tr>
                <th>Mutu</th>
                <td>
                    <?php echo e(optional($form->mutuUser)->nama ?? '-'); ?>

                    <span class="muted"> | <?php echo e(fmt($form->mutu_approved_at)); ?></span>
                </td>
            </tr>
            <tr>
                <th>Final (Unit Terkait)</th>
                <td>
                    <?php echo e(optional($form->finalUser)->nama ?? '-'); ?>

                    <span class="muted"> | <?php echo e(fmt($form->final_approved_at)); ?></span>
                </td>
            </tr>
        </table>
    </div>

    
    <div class="section no-break">
        <div class="mb4"><strong>Tanda Tangan</strong></div>

        <table class="grid-2">
            <tr>
                
                <td class="sign">
                    <?php $img = ttdImg($form->creator); ?>
                    <?php if($img): ?><img src="<?php echo e($img); ?>"><?php endif; ?>
                    <div class="sign-name"><?php echo e(optional($form->creator)->nama ?? '(...........)'); ?></div>
                    <div class="sign-role">Staff (Pembuat)</div>
                    <div class="sign-date">Tgl: <?php echo e(fmt($form->created_at)); ?></div>
                </td>

                
                <td class="sign">
                    <?php $img = ttdImg($form->supervisionUser); ?>
                    <?php if($img): ?><img src="<?php echo e($img); ?>"><?php endif; ?>
                    <div class="sign-name"><?php echo e(optional($form->supervisionUser)->nama ?? '(...........)'); ?></div>
                    <div class="sign-role">Supervision</div>
                    <div class="sign-date">Tgl: <?php echo e(fmt($form->supervision_approved_at)); ?></div>
                </td>
            </tr>

            <tr>
                
                <td class="sign">
                    <?php $img = ttdImg($form->managerUser); ?>
                    <?php if($img): ?><img src="<?php echo e($img); ?>"><?php endif; ?>
                    <div class="sign-name"><?php echo e(optional($form->managerUser)->nama ?? '(...........)'); ?></div>
                    <div class="sign-role">Manager</div>
                    <div class="sign-date">Tgl: <?php echo e(fmt($form->manager_approved_at)); ?></div>
                </td>

                
                <td class="sign">
                    <?php $img = ttdImg($form->mutuUser); ?>
                    <?php if($img): ?><img src="<?php echo e($img); ?>"><?php endif; ?>
                    <div class="sign-name"><?php echo e(optional($form->mutuUser)->nama ?? '(...........)'); ?></div>
                    <div class="sign-role">Mutu</div>
                    <div class="sign-date">Tgl: <?php echo e(fmt($form->mutu_approved_at)); ?></div>
                </td>
            </tr>

            <tr>
                
                <td class="sign" colspan="2">
                    <?php $img = ttdImg($form->finalUser); ?>
                    <?php if($img): ?><img src="<?php echo e($img); ?>"><?php endif; ?>
                    <div class="sign-name"><?php echo e(optional($form->finalUser)->nama ?? '(...........)'); ?></div>
                    <div class="sign-role">Final (Unit Terkait)</div>
                    <div class="sign-date">Tgl: <?php echo e(fmt($form->final_approved_at)); ?></div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\project_form\resources\views/sistem_sdm/bap/detail_bap_pdf.blade.php ENDPATH**/ ?>