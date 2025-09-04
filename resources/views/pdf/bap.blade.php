<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 30px;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #28a745;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .logo {
            width: 120px;
        }
        h2 {
            margin: 0;
            font-size: 18px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h4 {
            background: #e9f8f0;
            padding: 6px;
            border-left: 4px solid #28a745;
        }
        .section p, .section li {
            margin: 5px 0;
        }
        .ttd {
            text-align: center;
            margin-top: 40px;
        }
        .ttd img {
            height: 60px;
        }
        table.ttd-table {
            width: 100%;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('storage/rskk-logo2.png') }}" alt="Logo" class="logo">
        <h2>Formulir BAP</h2>
    </div>

    <div class="section">
        <h4>📌 Informasi Umum</h4>
        <p><strong>Dibuat Oleh:</strong> {{ $form->creator->nama ?? '-' }}</p>
        <p><strong>Tanggal Dibuat:</strong> {{ $form->created_at->format('d-m-Y H:i') }}</p>
        <p><strong>Divisi Tujuan:</strong> {{ $form->divisi_verifikasi ?? '-' }}</p>
        <p><strong>Status:</strong> {{ $form->status }}</p>
    </div>

    <div class="section">
        <h4>🔧 Perbaikan Sistem</h4>
        @php $list = is_array($form->perbaikan) ? $form->perbaikan : json_decode($form->perbaikan, true); @endphp
        @if (!empty($list))
            <ul>
                @foreach ($list as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        @else
            <p><em>Tidak ada perbaikan yang dipilih.</em></p>
        @endif
        <p><strong>Tindakan Medis:</strong> {{ $form->tindakan_medis ?? '-' }}</p>
        <p><strong>Lain-lain:</strong> {{ $form->lain_lain ?? '-' }}</p>
    </div>

    <div class="section">
        <h4>❗ Permasalahan Lain</h4>
        <p>{{ $form->permasalahan_lain ?? '-' }}</p>
    </div>

    <div class="section">
        <h4>✅ Verifikasi & Penyelesaian</h4>
        <p><strong>Disetujui oleh Unit:</strong> {{ $form->itUser->nama ?? '-' }}</p>
        <p><strong>Tanggal Verifikasi:</strong> {{ optional($form->it_approved_at)->format('d-m-Y H:i') ?? '-' }}</p>
        <p><strong>Disetujui oleh Manager:</strong> {{ $form->managerUser->nama ?? '-' }}</p>
        <p><strong>Tanggal ACC Manager:</strong> {{ optional($form->manager_approved_at)->format('d-m-Y H:i') ?? '-' }}</p>
        <p><strong>Kendala / Penyelesaian:</strong> {{ $form->kendala ?? '-' }}</p>
    </div>

    <div class="section">
        <h4>✍️ Tanda Tangan</h4>
        <table class="ttd-table">
            <tr>
                <td>
                    <strong>Petugas Pengisi</strong><br>
                    @if (!empty($form->creator->ttd_path))
                        <img src="{{ public_path('storage/' . $form->creator->ttd_path) }}">
                    @else
                        <em>(TTD tidak tersedia)</em>
                    @endif
                    <br><u>{{ $form->creator->nama ?? '-' }}</u>
                </td>
                <td>
                    <strong>Kepala Unit</strong><br>
                    @if (!empty($form->itUser->ttd_path))
                        <img src="{{ public_path('storage/' . $form->itUser->ttd_path) }}">
                    @else
                        <em>(TTD tidak tersedia)</em>
                    @endif
                    <br><u>{{ $form->itUser->nama ?? '-' }}</u>
                </td>
                <td>
                    <strong>Manager</strong><br>
                    @if (!empty($form->managerUser->ttd_path))
                        <img src="{{ public_path('storage/' . $form->managerUser->ttd_path) }}">
                    @else
                        <em>(TTD tidak tersedia)</em>
                    @endif
                    <br><u>{{ $form->managerUser->nama ?? '-' }}</u>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
