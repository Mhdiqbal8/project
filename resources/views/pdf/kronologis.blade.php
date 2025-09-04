<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Form Kronologis</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            margin: 40px;
        }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .logo {
            width: 80px;
            height: auto;
            margin-right: 15px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e7e34;
        }
        .info, .ttd {
            margin-top: 20px;
        }
        .info p, .ttd p {
            margin: 4px 0;
        }
        .ttd {
            text-align: right;
            margin-top: 50px;
        }
        .ttd img {
            height: 60px;
        }
        .line {
            border-top: 1px solid #ccc;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('storage/rskk-logo2.png') }}" class="logo">
        <div class="title">Form Kronologis</div>
    </div>

    <div class="info">
        <p><strong>Judul:</strong> {{ $form->judul }}</p>
        <p><strong>Tanggal:</strong> {{ $form->tanggal }}</p>
        <p><strong>Tipe Kronologis:</strong> {{ $form->tipe_kronologis }}</p>
        @if($form->tipe_kronologis == 'Medis')
            <p><strong>Nama Pasien:</strong> {{ $form->nama_pasien }}</p>
            <p><strong>No RM:</strong> {{ $form->no_rm }}</p>
            <p><strong>Usia:</strong> {{ $form->usia }}</p>
            <p><strong>Diagnosa:</strong> {{ $form->diagnosa }}</p>
            <p><strong>Ruangan:</strong> {{ $form->ruangan }}</p>
        @endif
        <div class="line"></div>
        <p><strong>Kronologis Masalah:</strong></p>
        <div>{!! nl2br(e($form->deskripsi)) !!}</div>
    </div>

    <div class="ttd">
        <p><strong>Petugas Pengisi</strong></p>

        {{-- DEBUG PATH --}}
        <p style="font-size: 10px; color: #999;">
            Path:
            {{ $form->creator && $form->creator->ttd_path
                ? public_path('storage/' . $form->creator->ttd_path)
                : 'Tidak ada path'
            }}
        </p>

        @if(!empty($form->creator->ttd_path) && file_exists(public_path('storage/' . $form->creator->ttd_path)))
            <img src="{{ public_path('storage/' . $form->creator->ttd_path) }}">
        @else
            <p><em>(TTD Tidak Tersedia)</em></p>
        @endif

        <p><u>{{ $form->creator->nama ?? '-' }}</u></p>
    </div>
</body>
</html>
