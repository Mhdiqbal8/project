<?php

namespace App\Exports;

use App\Models\LaporanKerja;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanKerjaExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return LaporanKerja::with(['user'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Shift',
            'Nama User',
            'Jam In',
            'Jam Out',
            'Total Komentar',
            'Status Komentar'
        ];
    }

    public function map($laporan): array
    {
        $totalKomentar = $laporan->komentar->count();
        $totalBeres = $laporan->komentar->where('is_beres', true)->count();

        $statusKomentar = '-';
        if ($totalKomentar > 0 && $totalBeres < $totalKomentar) {
            $statusKomentar = '🔴 Ada Komentar';
        } elseif ($totalKomentar > 0) {
            $statusKomentar = '✅ Sudah Dibahas';
        }

        return [
            $laporan->id,
            $laporan->tanggal,
            $laporan->shift ?? '-',
            $laporan->user?->nama ?? '-',
            ($laporan->jam_in_mulai || $laporan->jam_in_selesai)
                ? ($laporan->jam_in_mulai ?? '-') . ' - ' . ($laporan->jam_in_selesai ?? '-')
                : 'Tidak Ada Jam In',
            ($laporan->jam_out_mulai || $laporan->jam_out_selesai)
                ? ($laporan->jam_out_mulai ?? '-') . ' - ' . ($laporan->jam_out_selesai ?? '-')
                : 'Tidak Ada Jam Out',
            $totalKomentar,
            $statusKomentar
        ];
    }
}
