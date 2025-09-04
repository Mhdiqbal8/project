<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class LaporanExcel implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $dept = Auth::user()->department_id;
        // Join table service to users
        $user = User::where('department_id', $dept)->get();


        foreach($user as $userId){
          $user_id[] = $userId->id;
        }
        $service = Service::join('users as a', 'service.user_id', '=', 'a.id')
                          ->join('department as b', 'a.department_id', '=', 'b.id')
                          ->join('inventaris as c', 'service.inventaris_id', '=', 'c.id')
                          ->join('jenis_inventaris as d', 'c.jenis_inventaris_id', '=',  'd.id')
                          ->whereIn('service.user_id', $user_id)
                          ->select('a.nama as pemohon','b.department', 'd.jenis_inventaris', 'c.nama as inventaris', 'service.service',
                           'service.biaya_service', 'service.created_at', 'service.keterangan')
                          ->orderBy('service.created_at')
                          ->get();
        return $service;
    }

    public function headings(): array
    {
        return [
            'Nama Pemohon',
            'Department/Bagian',
            'Jenis Inventaris',
            'Inventaris',
            'Service',
            'Perkiraan Biaya',
            'Tanggal Permohonan Service',
            'Keterangan'
        ];
    }

    public function map($transaction): array
    {
        return [
        ];
    }
}
