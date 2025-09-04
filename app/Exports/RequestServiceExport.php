<?php

namespace App\Exports;

use App\Models\Service;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RequestServiceExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Service::with(['user', 'unit', 'status'])
            ->whereIn('status_id', [6, 7, 8, 9])
            ->get()
            ->map(function ($item) {
                return [
                    'No Tiket'    => $item->no_tiket,
                    'Nama Pemohon'=> optional($item->user)->name,
                    'Departemen'  => optional($item->user->department)->nama_department ?? '-',
                    'Unit'        => optional($item->unit)->nama_unit ?? '-',
                    'Status'      => optional($item->status)->nama_status,
                    'Tanggal'     => $item->created_at->format('d-m-Y H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return ['No Tiket', 'Nama Pemohon', 'Departemen', 'Unit', 'Status', 'Tanggal'];
    }
}
