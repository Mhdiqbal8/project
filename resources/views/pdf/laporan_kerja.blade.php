<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Harian Kerja</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
            margin: 30px;
        }
        .title {
            text-align: center;
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: bold;
            color: #198754;
        }
        .section {
            margin-bottom: 15px;
        }
        .section label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
            vertical-align: top;
        }
        .box {
            border: 1px solid #999;
            padding: 8px;
            margin-top: 5px;
            min-height: 50px;
        }
        .ttd {
            margin-top: 40px;
            text-align: center;
        }
        img.ttd-img {
            height: 80px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            color: #aaa;
        }
    </style>
</head>
<body>

    <div class="title">
        Laporan Harian Kerja
    </div>

    <div class="section">
        <label>Tanggal:</label> {{ $laporan->tanggal }}
    </div>

    <div class="section">
        <label>Jam Masuk:</label> {{ $laporan->jam_in ?? '-' }}
    </div>

    <div class="section">
        <label>Jam Pulang:</label> {{ $laporan->jam_out ?? '-' }}
    </div>

    <div class="section">
        <label>Nama Petugas:</label> {{ $laporan->user?->nama ?? '-' }}
    </div>

    <div class="section">
        <label>Jabatan:</label> {{ $laporan->user?->jabatan?->nama ?? '-' }}
    </div>

    <div class="section">
        <label>Kegiatan IN:</label>
        <div class="box">
            {!! nl2br(e($laporan->kegiatan_in)) ?: '<em>Tidak ada kegiatan IN.</em>' !!}
        </div>
    </div>

    <div class="section">
        <label>Kegiatan OUT:</label>
        <div class="box">
            {!! nl2br(e($laporan->kegiatan_out)) ?: '<em>Tidak ada kegiatan OUT.</em>' !!}
        </div>
    </div>

    <div class="section">
        <label>Catatan Staff:</label>
        <div class="box">
            {!! nl2br(e($laporan->komentar_staff)) ?: '<em>Tidak ada komentar.</em>' !!}
        </div>
    </div>

    {{-- TTD --}}
    @if($ttdPath)
        <div class="ttd">
            <p style="font-weight: bold; margin-bottom: 5px;">Tanda Tangan Petugas</p>
            <img src="{{ $ttdPath }}" class="ttd-img">
            <p style="margin-top: 5px; font-weight: bold;">{{ $laporan->user?->nama ?? '' }}</p>
            <p style="font-size: 11px;">{{ $laporan->user?->jabatan?->nama ?? '' }}</p>
        </div>
    @else
        <p class="ttd"><em>Belum ada tanda tangan.</em></p>
    @endif

    <div class="footer">
        Dicetak pada {{ date('d-m-Y H:i') }}
    </div>

</body>
</html>
