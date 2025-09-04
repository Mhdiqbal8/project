<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Inventaris;
use App\Models\JenisInventaris;
use App\Models\KeteranganService;
use App\Models\Service;
use App\Models\Status;
use App\Models\User;
use App\Models\RequestService;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Exports\LaporanExcel;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\NewServiceRequest;
use Illuminate\Support\Facades\Notification;

class ServiceController extends Controller
{
    private function abortIfMutu()
    {
        $user = Auth::user();
        if ($user && $user->hasRole('mutu')) {
            abort(403, 'Bagian Mutu hanya boleh melihat data.');
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user()->load('akses');
        $query = Service::with(['user.department', 'user.jabatan', 'status']);

        // === Filter Berdasarkan Role ===
        if (!($user->isSuperAdmin() || $user->hasRole('Super-Admin') || $user->hasRole('mutu'))) {
            $query->where(function ($q) use ($user) {
                if ($user->jabatan_id == 2) {
                    // Kepala Unit → bisa lihat semua form dari unit yang dia pimpin
                    $unitIds = \App\Models\Unit::where('kepala_unit_id', $user->id)->pluck('id');
                    $q->whereHas('user', function ($q2) use ($unitIds) {
                        $q2->whereIn('unit_id', $unitIds);
                    });
                } elseif (in_array($user->jabatan_id, [3, 4])) {
                    // SPV & Manager → bisa lihat semua form dari departemen yang sama
                    $q->whereHas('user', function ($q2) use ($user) {
                        $q2->where('department_id', $user->department_id);
                    });
                } else {
                    // Staff → hanya form unit sendiri dan form yang dia buat
                    $q->whereHas('user', function ($q2) use ($user) {
                        $q2->where('unit_id', $user->unit_id);
                    })->orWhere('user_id', $user->id);
                }
            });
        }

        // === Filter: Belum Approve
        if ($request->belum_approve == '1') {
            $query->whereIn('status_id', [3, 4, 5]);
        }

        // === Filter: Search Nama / Tiket
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('no_tiket', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($q2) use ($request) {
                        $q2->where('nama', 'like', '%' . $request->search . '%');
                    });
            });
        }

        // === Filter: Tanggal
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // === Pagination: jumlah per halaman
        $limit = in_array($request->limit, [5, 10, 25, 50, 100]) ? $request->limit : 10;

        $services = $query->orderByDesc('created_at')->paginate($limit)->appends($request->query());

        // Inject label status
        foreach ($services as $s) {
            $s->status_name = config("status_label.{$s->status_id}") ?? (optional($s->status)->status ?? '-');
        }

        return view('service.index', [
            'services'          => $services,
            'departments'       => Department::all(),
            'jenis_inventariss' => JenisInventaris::orderBy('jenis_inventaris')->get(),
            'statuses'          => Status::all(),
            'akses'             => $user->akses->pluck('kode')->toArray(),
            // hanya tampilkan IT & Maintenance
            'units'             => Unit::whereIn('nama_unit', ['IT', 'Maintenance'])
                                       ->orderBy('nama_unit')
                                       ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->abortIfMutu();

        $validated = $request->validate([
            'jenis_inventaris_id' => 'required|exists:jenis_inventaris,id',
            'inventaris_id'       => 'required|exists:inventaris,id',
            // kunci agar hanya unit tujuan IT/Maintenance yang lolos
            'unit_tujuan_id'      => [
                'required',
                Rule::exists('units', 'id')->where(function ($q) {
                    $q->whereIn('nama_unit', ['IT', 'Maintenance']);
                }),
            ],
            'service'             => 'required|string|max:255',
            'keterangan'          => 'nullable|string|max:1000',
        ]);

        $jenis   = JenisInventaris::find($validated['jenis_inventaris_id']);
        $prefix  = strtoupper(substr($jenis->jenis_inventaris, 0, 3));
        $no_tiket = $this->generateNoTiket($validated['jenis_inventaris_id'], $prefix);

        $service = Service::create([
            'user_id'             => Auth::id(),
            'no_tiket'            => $no_tiket,
            'jenis_inventaris_id' => $validated['jenis_inventaris_id'],
            'inventaris_id'       => $validated['inventaris_id'],
            'unit_tujuan_id'      => $validated['unit_tujuan_id'],
            'service'             => $validated['service'],
            'keterangan'          => $validated['keterangan'] ?? null,
            'status_id'           => 3,
        ]);

        KeteranganService::create([
            'service_id' => $service->id,
            'user_id'    => Auth::id(),
            'keterangan' => '[CREATE] Permohonan dibuat: ' . ($validated['keterangan'] ?? '-'),
        ]);

        // Kirim notifikasi ke SPV/Manager departemen pemohon (seperti semula)
        $user = Auth::user();
        $jabatanSPVManager = [3, 4]; // 3 = SPV, 4 = Manager

        $recipients = User::whereIn('jabatan_id', $jabatanSPVManager)
            ->where('department_id', $user->department_id)
            ->get();

        Notification::send($recipients, new NewServiceRequest($service, 'service'));

        return redirect()->route('service.index')->with('success', 'Data permohonan service berhasil disimpan & notifikasi terkirim.');
    }

    private function generateNoTiket($jenisInventarisId, $prefix)
    {
        $last = Service::where('jenis_inventaris_id', $jenisInventarisId)
            ->orderBy('id', 'desc')
            ->first();

        $lastNumber = 0;
        if ($last && preg_match('/(\d+)$/', $last->no_tiket, $matches)) {
            $lastNumber = (int)$matches[1];
        }

        return $prefix . '-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    // APPROVE BIASA
    public function approve($id)
    {
        $this->abortIfMutu();

        $service = Service::with('user.unit')->findOrFail($id);
        $user = Auth::user();

        if ($service->status_id >= 6) {
            return back()->with('error', 'Permohonan ini sudah disetujui.');
        }
        if (!in_array($user->jabatan_id, [3, 4])) {
            return back()->with('error', 'Hanya Supervisor atau Manager yang dapat menyetujui.');
        }
        if ($user->department_id !== $service->user->department_id) {
            abort(403, 'Anda hanya dapat menyetujui permohonan dari departemen yang sama.');
        }
        $sudahApprove = KeteranganService::where('service_id', $service->id)
            ->where('keterangan', 'like', '%[APPROVE]%')
            ->exists();
        if ($sudahApprove) {
            return back()->with('error', 'Permohonan ini sudah disetujui oleh pihak lain.');
        }

        // Update status
        $service->status_id = 6;
        $service->save();

        // Sinkron ke request_services
        RequestService::updateOrCreate(
            ['service_id' => $service->id],
            [
                'user_id'   => $service->user_id,
                'status_id' => 6,
            ]
        );

        // Log
        KeteranganService::create([
            'service_id' => $service->id,
            'user_id'    => $user->id,
            'keterangan' => '[APPROVE] Disetujui oleh ' . $user->nama,
        ]);

        // === NOTIF ===
        $roleName = ($user->jabatan_id == 3) ? 'Supervisor' : 'Manager';

        // 1) Notif ke pemohon (pembuat tiket) - target: SERVICE
        Notification::send(
            $service->user,
            new NewServiceRequest(
                $service,
                'service',
                "Permohonan {$service->no_tiket} sudah di-approve {$roleName}.",
                'Tiket Disetujui'
            )
        );

        // 2) Notif ke Unit Tujuan - target: REQUEST SERVICE
        $usersUnitTujuan = User::where('unit_id', $service->unit_tujuan_id)->get();
        Notification::send(
            $usersUnitTujuan,
            new NewServiceRequest(
                $service,
                'request_service',
                "Tiket {$service->no_tiket} disetujui {$roleName} dan masuk ke daftar Request Service.",
                'Tiket Masuk ke Unit'
            )
        );

        return back()->with('success', 'Permohonan berhasil disetujui oleh ' . $user->nama);
    }

    // APPROVE MODAL
    public function approveModal(Request $request)
    {
        $this->abortIfMutu();

        $request->validate([
            'service_id'       => 'required|exists:service,id',
            'type_permohonan'  => 'required|in:0,1',
            'keterangan'       => 'nullable|string|max:255',
        ]);

        $service = Service::findOrFail($request->service_id);
        $user = Auth::user();

        if (!$user->isSuperAdmin() && $user->department_id !== optional($service->user)->department_id) {
            return back()->with('failed', 'Tidak memiliki akses ke departemen ini');
        }

        $service->status_id = 6;
        $service->type_permohonan = $request->type_permohonan;
        $service->save();

        RequestService::updateOrCreate(
            ['service_id' => $service->id],
            [
                'user_id'   => $service->user_id,
                'status_id' => 6,
            ]
        );

        KeteranganService::create([
            'service_id' => $service->id,
            'user_id'    => $user->id,
            'keterangan' => '[APPROVE MODAL] ' . ($request->keterangan ?: '-'),
        ]);

        // === NOTIF ===
$approver = Auth::user();
$roleName = ($approver->jabatan_id == 3) ? 'Supervisor' : 'Manager';
$approverName = $approver->nama;

// ke pemohon
Notification::send(
    $service->user,
    new NewServiceRequest(
        $service,
        'service',
        "Permohonan {$service->no_tiket} sudah di-approve {$roleName} ({$approverName}).",
        'Tiket Disetujui'
    )
);

// ke unit tujuan
$usersUnitTujuan = User::where('unit_id', $service->unit_tujuan_id)->get();
Notification::send(
    $usersUnitTujuan,
    new NewServiceRequest(
        $service,
        'request_service',
        "Tiket {$service->no_tiket} disetujui {$roleName} ({$approverName}) dan masuk ke daftar Request Service.",
        'Tiket Masuk ke Unit'
    )
);


        return back()->with('success', 'Permohonan berhasil di-approve.');
    }

    // APPROVE URGENT
    public function approveUrgent(Request $request)
    {
        $this->abortIfMutu();

        $request->validate([
            'service_id'        => 'required|exists:service,id',
            'keterangan_urgent' => 'required|string|max:255',
        ]);

        $service = Service::findOrFail($request->service_id);

        $service->status_id = 6;
        $service->save();

        RequestService::updateOrCreate(
            ['service_id' => $service->id],
            [
                'user_id'   => $service->user_id,
                'status_id' => 6,
            ]
        );

        KeteranganService::create([
            'service_id' => $service->id,
            'user_id'    => Auth::id(),
            'keterangan' => '[APPROVE URGENT] ' . $request->keterangan_urgent,
        ]);

        // === NOTIF ===
        $approver = Auth::user();
        $roleName = ($approver->jabatan_id == 3) ? 'Supervisor' : 'Manager';

        // ke pemohon
        Notification::send(
            $service->user,
            new NewServiceRequest(
                $service,
                'service',
                "Permohonan {$service->no_tiket} sudah di-approve {$roleName} (URGENT).",
                'Tiket Disetujui'
            )
        );

        // ke unit tujuan
        $usersUnitTujuan = User::where('unit_id', $service->unit_tujuan_id)->get();
        Notification::send(
            $usersUnitTujuan,
            new NewServiceRequest(
                $service,
                'request_service',
                "Tiket {$service->no_tiket} disetujui {$roleName} (URGENT) dan masuk ke daftar Request Service.",
                'Tiket Masuk ke Unit'
            )
        );

        return redirect()->back()->with('success', 'Service disetujui secara URGENT.');
    }

    public function show(Service $service)
    {
        $service->load([
            'user.unit.department', // penting biar department bisa diakses dari unit
            'user.jabatan',
            'status',
            'inventaris.jenis_inventaris'
        ]);

        $all = KeteranganService::where('service_id', $service->id)
            ->orderBy('created_at', 'asc')
            ->get();

        $lastCreate = null;
        foreach ($all as $item) {
            if (Str::contains($item->keterangan, '[CREATE]')) {
                $lastCreate = $item->created_at;
            }
        }

        $filtered = $all->filter(fn ($item) => $item->created_at >= $lastCreate);

        $historySteps = collect([
            '[CREATE]' => null,
            '[APPROVE]' => null,
            '[APPROVE MODAL]' => null,
            '[APPROVE URGENT]' => null,
            'dimulai' => null,
            'selesai' => null,
            '[REJECT]' => null,
        ]);

        foreach ($filtered->reverse() as $item) {
            $raw = $item->keterangan;

            if (Str::contains($raw, '[CREATE]') && !$historySteps['[CREATE]']) {
                $historySteps['[CREATE]'] = $item;
            } elseif (Str::contains($raw, '[APPROVE]') && !$historySteps['[APPROVE]']) {
                $historySteps['[APPROVE]'] = $item;
            } elseif (Str::contains($raw, '[APPROVE MODAL]') && !$historySteps['[APPROVE MODAL]']) {
                $historySteps['[APPROVE MODAL]'] = $item;
            } elseif (Str::contains($raw, '[APPROVE URGENT]') && !$historySteps['[APPROVE URGENT]']) {
                $historySteps['[APPROVE URGENT]'] = $item;
            } elseif (Str::contains($raw, 'dimulai') && !$historySteps['dimulai']) {
                $historySteps['dimulai'] = $item;
            } elseif (Str::contains($raw, 'selesai') && !$historySteps['selesai']) {
                $historySteps['selesai'] = $item;
            } elseif (Str::contains($raw, '[REJECT]') && !$historySteps['[REJECT]']) {
                $historySteps['[REJECT]'] = $item;
            }
        }

        return view('service.show', [
            'service' => $service,
            'keterangan_service' => $historySteps->filter(),
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $this->abortIfMutu();

        $service->update([
            'user_id' => Auth::user()->id,
            'status_id' => Auth::user()->status->id,
            'jenis_inventaris_id' => $request->jenis_inventaris_id,
            'created_at' => $request->created_at,
            'service' => $request->service,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('service.index')->with('success', 'Data berhasil diupdate!');
    }

    // DESTROY (perbaiki delete relasi)
    public function destroy(Service $service)
    {
        $this->abortIfMutu();

        DB::beginTransaction();

        try {
            // hapus request_service berdasar service_id (BUKAN id)
            RequestService::where('service_id', $service->id)->delete();
            $service->delete();

            DB::commit();
            return back()->with('success', 'Data Service berhasil dihapus!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('failed', 'Data Service gagal dihapus, cek kembali data!');
        }
    }

    // REJECT
    public function reject(Request $request)
    {
        $this->abortIfMutu();

        $request->validate([
            'service_id' => 'required|exists:service,id',
            'keterangan' => 'required|string|max:255',
        ]);

        $service = Service::findOrFail($request->service_id);
        $user = Auth::user();

        if (!$user->isSuperAdmin() && $user->department_id !== optional($service->user)->department_id) {
            return back()->with('failed', 'Tidak memiliki akses ke departemen ini');
        }

        $service->status_id = 10;
        $service->save();

        RequestService::updateOrCreate(
            ['service_id' => $service->id],
            [
                'user_id'   => $service->user_id,
                'status_id' => 10,
            ]
        );

        KeteranganService::create([
            'service_id' => $service->id,
            'user_id'    => $user->id,
            'keterangan' => '[REJECT] ' . $request->keterangan,
        ]);

        return back()->with('success', 'Service berhasil ditolak!');
    }

    public function getInventaris(Request $request)
    {
        $request->validate([
            'id_jenis' => 'required|exists:jenis_inventaris,id'
        ]);

        $inventaris = Inventaris::where('jenis_inventaris_id', $request->id_jenis)
            ->orderBy('nama')
            ->get();

        return response()->json([
            'response' => 'success',
            'val_inventaris' => $inventaris
        ]);
    }

    public function check_data_service()
    {
        $user = Auth::user();
        $user_ids = User::where('department_id', $user->department_id)->pluck('id')->toArray();
        $get_data_service = 0;

        if (in_array($user->jabatan_id, [2, 3])) {
            $status = $user->jabatan_id == 2 ? [3] : [3, 4];
            $get_data_service = Service::whereIn('user_id', $user_ids)
                ->whereIn('status_id', $status)
                ->count();
        }

        return response()->json(['response' => 'success', 'total_data_service' => $get_data_service]);
    }

    public function export_service_excel()
    {
        return Excel::download(new LaporanExcel, 'laporan_service.xlsx');
    }

    public function getInventarisByJenis($id)
    {
        $inventaris = Inventaris::where('jenis_inventaris_id', $id)->get(['id', 'nama']);
        return response()->json($inventaris);
    }

    public function approve_form(Request $request)
    {
        $service = Service::findOrFail($request->service_id);
        $user = Auth::user();

        // Validasi jabatan & unit
        if ($user->jabatan_id == 3 && $service->spv_approved_at === null) {
            $service->spv_approved_at = now();
        } elseif ($user->jabatan_id == 4 && $service->manager_approved_at === null) {
            $service->manager_approved_at = now();
        } else {
            return back()->with('error', 'Anda tidak berhak menyetujui permohonan ini.');
        }

        $service->status_id = 6;
        $service->save();

        // Buat request_service jika belum ada
        if (!RequestService::where('service_id', $service->id)->exists()) {
            RequestService::create([
                'service_id'     => $service->id,
                'unit_tujuan_id' => $service->unit_tujuan_id,
                'status_id'      => 6
            ]);
        }

        // === NOTIF ===
        $roleName = ($user->jabatan_id == 3) ? 'Supervisor' : 'Manager';

        // ke pemohon
        Notification::send(
            $service->user,
            new NewServiceRequest(
                $service,
                'service',
                "Permohonan {$service->no_tiket} sudah di-approve {$roleName}.",
                'Tiket Disetujui'
            )
        );

        // ke unit tujuan
        $unitUsers = User::where('unit_id', $service->unit_tujuan_id)->get();
        Notification::send(
            $unitUsers,
            new NewServiceRequest(
                $service,
                'request_service',
                "Tiket {$service->no_tiket} disetujui {$roleName} dan masuk ke daftar Request Service.",
                'Tiket Masuk ke Unit'
            )
        );

        return back()->with('success', 'Permohonan berhasil disetujui');
    }
}
