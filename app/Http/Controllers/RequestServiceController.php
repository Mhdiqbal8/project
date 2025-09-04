<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\KeteranganService;
use App\Models\Service;
use App\Models\TeknisiUmum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\RequestService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RequestServiceExport;


class RequestServiceController extends Controller
{
    private function abortIfMutu()
    {
        $user = Auth::user();
        if ($user && $user->hasRole('mutu')) {
            abort(403, 'Bagian Mutu hanya boleh melihat data.');
        }
    }

public function index_request()
{
    $user = Auth::user();
    $teknisi_umums = TeknisiUmum::all();

    // Semua unit di departemen user
    $unitIdsInDept = \App\Models\User::where('department_id', $user->department_id)
        ->pluck('unit_id')->filter()->unique()->values();

    $service_requests = RequestService::with([
        // >>> PENTING: ikutkan type_permohonan biar bisa deteksi URGENT
        'service:id,no_tiket,user_id,unit_tujuan_id,status_id,type_permohonan',
        'service.user:id,nama,unit_id,department_id',
        'service.user.unit:id,nama_unit',
        'service.user.department:id,nama',
        'service.status:id,status',
        'service.unitTujuan:id,nama_unit',
    ])
    // hanya yang belum beres
    ->whereIn('status_id', [6, 7])
    ->whereHas('service', function ($q) use ($user, $unitIdsInDept) {
        if (!in_array($user->jabatan_id, [3, 4])) {
            // staff: hanya unit tujuan = unit sendiri
            $q->where('unit_tujuan_id', $user->unit_id);
        } else {
            // SPV/Manager: semua unit tujuan satu departemen
            $q->whereIn('unit_tujuan_id', $unitIdsInDept);
        }
    })
    ->orderByDesc('created_at')
    ->get();

    // badge pending (butuh perhatian): 6 & 7
    $totalPendingRequestService = RequestService::whereIn('status_id', [6, 7])
        ->whereHas('service', function ($q) use ($user, $unitIdsInDept) {
            if (!in_array($user->jabatan_id, [3, 4])) {
                $q->where('unit_tujuan_id', $user->unit_id);
            } else {
                $q->whereIn('unit_tujuan_id', $unitIdsInDept);
            }
        })
        ->count();

    foreach ($service_requests as $req) {
        // siapkan label status (fallback ke relasi status->status)
        $req->status_name = config("status_label.{$req->status_id}")
            ?? optional($req->status)->status
            ?? '-';
    }

    return view('request_service.index_request', compact(
        'service_requests',
        'teknisi_umums',
        'totalPendingRequestService'
    ));
}


private function canActOnRequest($user, Service $service): bool
{
    // departemen dari unit tujuan (ambil via users di unit tsb)
    $deptTujuan = \App\Models\User::where('unit_id', $service->unit_tujuan_id)->value('department_id');

    // boleh aksi jika:
    // - unit user = unit tujuan, atau
    // - user SPV/Manager & department user = department unit tujuan
    if ($user->unit_id == $service->unit_tujuan_id) return true;

    return in_array($user->jabatan_id, [3,4]) && $deptTujuan && $user->department_id == $deptTujuan;
}

public function approve($id)
{
    $this->abortIfMutu();

    $service = Service::findOrFail($id);
    $user = Auth::user();
    if (!$this->canActOnRequest($user, $service)) {
        abort(403, 'Tidak berhak melakukan aksi pada tiket ini.');
    }

    // masuk ke unit tujuan
    $service->status_id = 6;
    $service->save();

    $this->updateRequestServiceStatus($service->id, 6);

    KeteranganService::create([
        'service_id' => $service->id,
        'user_id'    => $user->id,
        'keterangan' => 'Service disetujui (masuk unit tujuan).',
    ]);

    return back()->with('success', 'Service berhasil di-approve.');
}

   // use Carbon\Carbon; (opsional, kalau mau format)

// PATCH request_service/{id}/approve-progress
// PATCH request_service/{id}/approve-progress
public function approveProgress($id)
{
    $this->abortIfMutu();

    $rs = RequestService::with('service')->findOrFail($id);  // {id} = ID request_services
    $service = $rs->service;
    if (!$service) abort(404, 'Service tidak ditemukan untuk request ini.');

    $user = Auth::user();
    if (!$this->canActOnRequest($user, $service)) {
        abort(403, 'Tidak berhak melakukan aksi pada tiket ini.');
    }
    if ((int) $service->status_id !== 6) {
        return back()->with('error', 'Status tidak valid untuk On Progress.');
    }

    $now = now();

    // Update di tabel service (punya kolom tgl_teknisi)
    $service->update([
        'status_id'   => 7,
        'tgl_teknisi' => $now,
        'teknisi_id'  => $user->id,
        'keterangan'  => 'Service dimulai oleh teknisi: ' . ($user->nama ?? $user->name ?? '-'),
    ]);

    // Di request_services cukup status saja (kolom tgl_teknisi tidak ada)
    $rs->update([
        'status_id' => 7,
    ]);

    KeteranganService::create([
        'service_id' => $service->id,
        'user_id'    => $user->id,
        'keterangan' => '[PROGRESS] dimulai',
    ]);

    // Fungsi ini memang menerima service_id
    $this->updateRequestServiceStatus($service->id, 7);

    return back()->with('success', 'Tiket masuk On Progress.');
}

// PATCH request_service/{id}/approve-finish
public function approveFinish(Request $r, $id)
{
    $this->abortIfMutu();

    // wajib isi keterangan
    $r->validate([
        'keterangan' => 'required|string|min:5',
    ], [
        'keterangan.required' => 'Keterangan selesai wajib diisi.',
    ]);

    $rs = RequestService::with('service')->findOrFail($id);
    $service = $rs->service;
    if (!$service) abort(404, 'Service tidak ditemukan untuk request ini.');

    $user = Auth::user();
    if (!$this->canActOnRequest($user, $service)) {
        abort(403, 'Tidak berhak melakukan aksi pada tiket ini.');
    }
    if ((int) $service->status_id !== 7) {
        return back()->with('error', 'Status tidak valid untuk Selesai.');
    }

    // Simpan detail dari modal sebagai keterangan utama
    $service->update([
        'status_id'  => 9,
        'teknisi_id' => $user->id,
        'keterangan' => trim($r->keterangan),
    ]);

    // Sinkron status di request_services
    $rs->update(['status_id' => 9]);

    // Catat riwayat dengan detailnya
    KeteranganService::create([
        'service_id' => $service->id,
        'user_id'    => $user->id,
        'keterangan' => 'Service selesai: ' . trim($r->keterangan),
    ]);

    $this->updateRequestServiceStatus($service->id, 9);

    return back()->with('success', 'Service selesai. Detail pekerjaan tersimpan.');
}

public function onprogress(Request $request)
{
    $this->abortIfMutu();

    $service = Service::findOrFail($request->service_id);
    $user = Auth::user();
    if (!$this->canActOnRequest($user, $service)) {
        abort(403, 'Tidak berhak melakukan aksi pada tiket ini.');
    }

    $service->status_id = 7;
    $service->tgl_teknisi = now();

    if ($user->department_id == 4) {
        $service->teknisi_umum_id = $request->teknisi_umum_id;
    } else {
        $service->teknisi_id = $user->id;
    }

    $service->keterangan = $request->keterangan ?? 'Service dimulai oleh teknisi';
    $service->save();

    KeteranganService::create([
        'service_id' => $service->id,
        'user_id'    => $user->id,
        'keterangan' => $request->keterangan ?? 'Service dimulai oleh teknisi',
    ]);

    $this->updateRequestServiceStatus($service->id, 7);

    return back()->with('success', 'Service siap dikerjakan!');
}


   public function onprogress_it($id)
{
    $this->abortIfMutu();

    $service = Service::findOrFail($id);
    $user = Auth::user();
    if (!$this->canActOnRequest($user, $service)) {
        abort(403, 'Tidak berhak melakukan aksi pada tiket ini.');
    }

    $service->status_id = 7;
    $service->teknisi_id = $user->id;
    $service->tgl_teknisi = now();
    $service->keterangan = 'Service dimulai oleh teknisi IT';
    $service->save();

    $this->updateRequestServiceStatus($service->id, 7);

    return back()->with('success', 'Service siap dikerjakan!');
}

    public function selesai(Request $request)
{
    $this->abortIfMutu();

    $service = Service::findOrFail($request->service_id);
    $user = Auth::user();
    if (!$this->canActOnRequest($user, $service)) {
        abort(403, 'Tidak berhak melakukan aksi pada tiket ini.');
    }

    $service->status_id = 9;
    $service->teknisi_id = $user->id;
    $service->tgl_teknisi = now();
    $service->keterangan = $request->keterangan ?? 'Service selesai oleh teknisi.';
    $service->save();

    KeteranganService::create([
        'service_id' => $service->id,
        'user_id'    => $user->id,
        'keterangan' => $request->keterangan ?? 'Service selesai oleh teknisi.',
    ]);

    $this->updateRequestServiceStatus($service->id, 9);

    return back()->with('success', 'Service selesai dikerjakan.');
}


public function closed(Request $request, $id)
{
    $this->abortIfMutu();

    // id = ID dari request_services
    $rs = RequestService::with('service')->findOrFail($id);
    $service = $rs->service;
    if (!$service) {
        abort(404, 'Service tidak ditemukan untuk request ini.');
    }

    $user = Auth::user();
    if (!$this->canActOnRequest($user, $service)) {
        abort(403, 'Tidak berhak melakukan aksi pada tiket ini.');
    }

    // Update service -> Closed (8)
    $service->update([
        'status_id'  => 8,
        'teknisi_id' => $user->id,
        'tgl_teknisi'=> now(),
        'keterangan' => $request->keterangan ?? 'Service ditutup / tidak diproses.',
    ]);

    // Sinkron status di request_services juga
    $rs->update([
        'status_id'  => 8,
        // kalau mau simpan catatan di kolom keterangan RS juga:
        // 'keterangan' => $request->keterangan,
    ]);

    // Log riwayat
    KeteranganService::create([
        'service_id' => $service->id,
        'user_id'    => $user->id,
        'keterangan' => '[CLOSED] ' . ($request->keterangan ?? 'Service ditutup / tidak diproses.'),
    ]);

    // Jaga2, fungsimu tetap menyamakan status berdasarkan service_id
    $this->updateRequestServiceStatus($service->id, 8);

    return back()->with('success', 'Service ditutup / tidak diproses.');
}

  public function reject(Request $r)
{
    $this->abortIfMutu();

    $req = RequestService::findOrFail($r->request_service_id);
    $service = $req->service ?? Service::find($r->service_id);

    DB::transaction(function () use ($req, $service, $r) {
        // 10 = Ditolak
        $req->update(['status_id' => 10]);

        if ($service) {
            $service->update(['status_id' => 10]);
            KeteranganService::create([
                'service_id' => $service->id,
                'user_id'    => Auth::id(),
                'keterangan' => '[REJECT] ' . trim($r->keterangan),
            ]);
        }
    });

    return back()->with('success', 'Permohonan berhasil ditolak.');
}



    public function show($id)
{
    // Ambil data RequestService beserta relasi-relasi yang dibutuhkan
    $request = RequestService::with([
        'service.user.department',
        'service.user.jabatan',
        'service.user.unit',
        'unitTujuan',
        'status',
        'teknisi_umum',
        'service.inventaris.jenis_inventaris',
        'service.keterangan_service.user',
    ])->findOrFail($id);

    // DEBUG STATUS DULU
    // dd([
    //     'status_dari_request_services' => $request->status_id,
    //     'status_dari_service' => optional($request->service)->status_id,
    // ]);

    // Ambil teknisi umum jika ada
    $teknisi_umum = $request->teknisi_umum_id
        ? TeknisiUmum::find($request->teknisi_umum_id)
        : null;

    // Ambil histori service dari service_id yang nyambung ke model Service
    $all = KeteranganService::where('service_id', $request->service_id)
        ->orderBy('created_at', 'asc')
        ->get();

    $lastCreate = null;
    foreach ($all as $item) {
        if (Str::contains($item->keterangan, '[CREATE]')) {
            $lastCreate = $item->created_at;
        }
    }

    $filtered = $lastCreate
        ? $all->filter(fn ($item) => $item->created_at >= $lastCreate)
        : $all;

    $historySteps = collect([
        '[CREATE]' => null,
        'APPROVE MODAL' => null,
        'dimulai' => null,
        'selesai' => null,
        'REJECT' => null,
    ]);

    foreach ($filtered->reverse() as $item) {
        $raw = $item->keterangan;

        if (Str::contains($raw, '[CREATE]') && !$historySteps['[CREATE]']) {
            $historySteps['[CREATE]'] = $item;
        } elseif (Str::contains($raw, 'APPROVE MODAL') && !$historySteps['APPROVE MODAL']) {
            $historySteps['APPROVE MODAL'] = $item;
        } elseif (Str::contains($raw, 'dimulai') && !$historySteps['dimulai']) {
            $historySteps['dimulai'] = $item;
        } elseif (Str::contains($raw, 'selesai') && !$historySteps['selesai']) {
            $historySteps['selesai'] = $item;
        } elseif (Str::contains($raw, 'REJECT') && !$historySteps['REJECT']) {
            $historySteps['REJECT'] = $item;
        }
    }

    $filteredHistory = $historySteps->filter();

    return view('request_service.show', [
        'request' => $request,
        'keterangan_service' => $filteredHistory,
        'teknisi_umum' => $teknisi_umum
    ]);
}


   public function lonceng_request_service()
{
    $user = Auth::user();

    $unitIdsInDept = \App\Models\User::where('department_id', $user->department_id)
        ->pluck('unit_id')->filter()->unique()->values();

    $total = RequestService::whereIn('status_id', [6,7])
        ->whereHas('service', function ($q) use ($user, $unitIdsInDept) {
            if (!in_array($user->jabatan_id, [3,4])) {
                $q->where('unit_tujuan_id', $user->unit_id);
            } else {
                $q->whereIn('unit_tujuan_id', $unitIdsInDept);
            }
        })
        ->count();

    return response()->json([
        'response'   => 'success',
        'total_data' => $total
    ]);
}


    public function exportExcel()
{
    return Excel::download(new RequestServiceExport, 'request_service.xlsx');
}

  private function updateRequestServiceStatus($serviceId, $statusId)
{
    $requestService = RequestService::where('service_id', $serviceId)->first();
    if ($requestService) {
        $requestService->status_id = $statusId;
        $requestService->save();
    }
}

public function createRequest(Request $request)
{
    DB::beginTransaction();
    try {
        $user = Auth::user();

        $service = new Service();
        $service->no_tiket = Str::upper(Str::random(6));
        $service->user_id = $user->id;
        $service->unit_tujuan_id = $request->unit_tujuan_id ?: ($user->unit_id ?: 1);
        $service->inventaris_id = $request->inventaris_id; // Tambah inventaris ke service
        $service->status_id = 6;
        $service->save();

        $requestService = new RequestService();
        $requestService->judul = $request->judul;
        $requestService->deskripsi = $request->service;
        $requestService->status_id = 6;
        $requestService->unit_tujuan_id = $service->unit_tujuan_id;
        $requestService->service_id = $service->id;
        $requestService->inventaris_id = $request->inventaris_id; // Tambah inventaris ke request
        $requestService->keterangan = $request->keterangan;
        $requestService->save();

        KeteranganService::create([
            'service_id' => $service->id,
            'keterangan' => 'Form dibuat oleh ' . $user->nama,
        ]);

        DB::commit();
        return redirect()->back()->with('success', 'Permohonan Service berhasil dibuat');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Gagal membuat permohonan: ' . $e->getMessage());
    }
}





}
