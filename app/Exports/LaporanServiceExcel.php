<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanServiceExcel implements FromCollection,WithHeadings
{

    public function __construct(String $start_date, $end_date)
    {
      $this->start_date = $start_date;
      $this->end_date = $end_date;
    }

    public function collection()
    {
      $dept = Auth::user()->department_id;
      // Join table service to users
      $user = User::where('department_id', $dept)->get();


      foreach($user as $userId){
        $user_id[] = $userId->id;
      }
      $service = Service::join('users as a', 'service.user_id', '=', 'a.id')
                        ->join('users as aa', 'service.teknisi_id', '=', 'aa.id')
                        ->join('department as b', 'a.department_id', '=', 'b.id')
                        ->join('inventaris as c', 'service.inventaris_id', '=', 'c.id')
                        ->join('jenis_inventaris as d', 'c.jenis_inventaris_id', '=',  'd.id')
                        ->whereIn('service.user_id', $user_id)
                        // ->whereBetween('service.created_at', [$this->start_date, $this->end_date])
                        ->whereDate('service.created_at', '>=' ,$this->start_date)
                        ->whereDate('service.created_at', '<=' ,$this->end_date)
                        ->where('service.status_id', 8)
                        ->select('a.nama as pemohon','b.department', 'd.jenis_inventaris', 'c.nama as inventaris', 'service.service',
                         'service.biaya_service', DB::raw('DATE_FORMAT(service.created_at, "%d-%m-%Y") as tgl_permohonan'), 'service.keterangan', 'aa.nama as nama_teknisi')
                        ->orderBy('service.created_at')
                        ->get();
      return $service;
    }

    public function headings(): array
    {
        return [
            'Nama Pemohon',
            'Department/Bagian',
            'Jenis Service',
            'Inventaris',
            'Service',
            'Perkiraan Biaya',
            'Tanggal Permohonan Service',
            'Keterangan',
            'Teknisi'
        ];
    }

    public function map($transaction): array
    {
        return [
        ];
    }
}
