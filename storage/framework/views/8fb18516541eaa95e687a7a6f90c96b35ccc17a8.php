<?php
    use Carbon\Carbon;

    // helper mini buat format tanggal tanpa jam
    $fmtDate = function ($dt) {
        return $dt ? Carbon::parse($dt)->format('d-m-Y') : '-';
    };
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #1a8c82;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header img { height: 60px; }
        .title { text-align: right; }
        .title h2 {
            margin: 0;
            font-size: 16px;
            color: #1a8c82;
        }
        .section { margin-bottom: 20px; }
        .section-title {
            font-weight: bold;
            margin-bottom: 6px;
            font-size: 13px;
            border-left: 4px solid #1a8c82;
            padding-left: 10px;
            color: #1a8c82;
        }
        .table { width: 100%; border-collapse: collapse; }
        .table td { padding: 5px; vertical-align: top; }
        .ttd-block { text-align: right; margin-top: 40px; }
        .ttd-block img { height: 60px; margin-bottom: 5px; }
        .footer {
            font-size: 11px;
            border-top: 1px dashed #aaa;
            padding-top: 10px;
            margin-top: 40px;
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="<?php echo e(public_path('storage/rskk-logo2.png')); ?>" alt="Logo RSKK">
        <div class="title">
            <h2>FORM KRONOLOGIS PASIEN</h2>
            <p>Rumah Sakit Keluarga Kita</p>
            <p>Dicetak: <?php echo e(Carbon::now()->format('d-m-Y')); ?></p>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Informasi Pasien</div>
        <table class="table">
            <tr>
                <td><strong>Tanggal Kejadian</strong></td>
                
                <td>: <?php echo e($fmtDate($form->tanggal ?? null)); ?></td>
                <td><strong>Nama Pasien</strong></td>
                <td>: <?php echo e($form->nama_pasien ?? '-'); ?></td>
            </tr>
            <tr>
                <td><strong>No. RM</strong></td>
                <td>: <?php echo e($form->no_rm ?? '-'); ?></td>
                <td><strong>Diagnosa</strong></td>
                <td>: <?php echo e($form->diagnosa ?? '-'); ?></td>
            </tr>
            <tr>
                <td><strong>Ruangan</strong></td>
                <td>: <?php echo e($form->ruangan ?? '-'); ?></td>
                <td><strong>Usia</strong></td>
                <td>: <?php echo e(isset($form->usia) && $form->usia !== '' ? $form->usia.' tahun' : '-'); ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Masalah / Kronologis</div>
        <div>
            <?php echo !empty(trim(strip_tags($form->deskripsi ?? ''))) ? ($form->deskripsi) : ($form->masalah ?? '-'); ?>

        </div>
    </div>

    <div class="ttd-block">
        <p><strong>Petugas Pengisi</strong></p>

        <?php
            $ttdPath = $form->creator->ttd_path ?? null;
            $ttdFull = $ttdPath ? public_path('storage/'.$ttdPath) : null;
        ?>

        <?php if($ttdFull && file_exists($ttdFull)): ?>
            <img src="<?php echo e($ttdFull); ?>" alt="TTD Petugas">
        <?php else: ?>
            <p><em>(TTD Tidak Tersedia)</em></p>
        <?php endif; ?>

        <p><u><?php echo e($form->creator->nama ?? '-'); ?></u></p>
    </div>

    <div class="footer">
        Dicetak otomatis melalui sistem Aplikasi Form RSKK. Dokumen ini bersifat internal.
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\project_form\resources\views/sistem_sdm/kronologis/kronologis_pdf.blade.php ENDPATH**/ ?>