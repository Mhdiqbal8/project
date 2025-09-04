<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Laporan Service Bulanan</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    h3 { margin: 0 0 8px 0; }
    table { width:100%; border-collapse: collapse; }
    th, td { border:1px solid #999; padding:6px 8px; }
    th { background:#eee; }
  </style>
</head>
<body>
  <h3>Laporan Service Bulanan ({{ $start->isoFormat('MMMM Y') }})</h3>
  <p>Periode: {{ $start->format('d M Y') }} — {{ $end->format('d M Y') }}</p>

  <table style="margin-bottom:10px">
    <tr>
      <th>Total Ticket</th><th>Selesai</th><th>On Progress</th><th>Waiting</th><th>Closed/Rejected</th><th>% Selesai</th>
    </tr>
    <tr>
      <td>{{ number_format($total) }}</td>
      <td>{{ number_format($selesai) }}</td>
      <td>{{ number_format($onprogress) }}</td>
      <td>{{ number_format($waiting) }}</td>
      <td>{{ number_format($closed_rejected) }}</td>
      <td>{{ number_format($pct,1) }}%</td>
    </tr>
  </table>

  <table>
    <thead>
      <tr>
        <th style="width:40px">#</th>
        <th>Unit</th>
        <th style="width:90px">Total</th>
        <th style="width:90px">Selesai</th>
        <th style="width:90px">% Selesai</th>
      </tr>
    </thead>
    <tbody>
      @forelse($ranking as $i => $r)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $r->unit }}</td>
        <td style="text-align:right">{{ number_format($r->total) }}</td>
        <td style="text-align:right">{{ number_format($r->done) }}</td>
        <td style="text-align:right">{{ number_format($r->pct,1) }}%</td>
      </tr>
      @empty
      <tr><td colspan="5" style="text-align:center"><i>Tidak ada data</i></td></tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
