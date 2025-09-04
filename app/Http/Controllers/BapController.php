<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BapForm;
use App\Models\BapStatusLog;
use App\Models\KronologisForm;
use App\Models\User;
use App\Models\Unit;
use App\Models\BapUnitTagLog; // log riwayat tag
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use App\Notifications\UnitTagged;
use App\Notifications\NewBapCreated; // Notifikasi BAP Baru / Update
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate; // <-- [BARU] buat cek izin kronologis

class BapController extends Controller
{
    private function abortIfMutu()
    {
        $user = auth()->user();
        if ($user && $user->hasRole('mutu')) {
            abort(403, 'Bagian Mutu hanya boleh melihat data.');
        }
    }

    // ========= Helper Notif Mutu =========
    /**
     * Ambil user Mutu via Spatie (role/permission).
     * Fallback ke whereHas kalau macro role()/permission() nggak tersedia.
     *
     * @return \Illuminate\Support\Collection<\App\Models\User>
     */
    private function mutuUsers(): \Illuminate\Support\Collection
{
    // 1) Spatie (kalau ada)
    try {
        $byRole = User::role('mutu')->get();
        $byPerm = User::permission('acc_mutu_bap')->get();
    } catch (\Throwable $e) {
        $byRole = User::whereHas('roles', fn($q) => $q->where('name','mutu'))->get();
        $byPerm = User::whereHas('permissions', fn($q) => $q->where('name','acc_mutu_bap'))->get();
    }

    // 2) PRIVILEGE CUSTOM (yang kepake di sistem lu)
    // ---- pilih SALAH SATU sesuai relasi di model User ----
    // a) kalau User punya relasi langsung ->akses()
    $byPriv = User::whereHas('akses', fn($q) => $q->where('kode','acc_mutu_bap'))->get();

    // b) kalau lewat pivot ->aksesUsers()->akses  (UNCOMMENT kalau ini yang dipakai)
    // $byPriv = User::whereHas('aksesUsers.akses', fn($q) => $q->where('kode','acc_mutu_bap'))->get();

    // 3) (opsional) fallback by department/unit bernama "Mutu"
    $byDept = User::whereHas('department', fn($q) => $q->where('nama','Mutu'))->get();
    $byUnit = User::whereHas('unit', fn($q) => $q->whereIn('nama_unit',['Mutu','Bagian Mutu']))->get();

    return $byRole->merge($byPerm)->merge($byPriv)->merge($byDept)->merge($byUnit)
                  ->unique('id')->values();
}

    private function notifyMutu(BapForm $form, string $message, string $title): void
    {
        $mutuUsers = $this->mutuUsers();

        if ($mutuUsers->isEmpty()) {
            Log::warning('Notif Mutu: tidak ada user dengan role=mutu/perm=acc_mutu_bap', ['bap_id' => $form->id]);
            return;
        }

        Notification::sendNow($mutuUsers, new NewBapCreated($form, $message, $title));

        Log::info('Notif Mutu terkirim', [
            'bap_id' => $form->id,
            'to_ids' => $mutuUsers->pluck('id')->all(),
            'title'  => $title,
        ]);
    }
    // ========= End Helper =========

    // ========= Helper Notif IT =========
    /**
     * Ambil user IT via Spatie (role/permission).
     * Fallback ke whereHas kalau macro role()/permission() nggak tersedia.
     *
     * @return \Illuminate\Support\Collection<\App\Models\User>
     */
    private function itUsers(): Collection
    {
        try {
            $byRole = User::role('it')->get();
            $byPerm = User::permission('approve_it')->get(); // sesuaikan jika nama permission beda
        } catch (\Throwable $e) {
            $byRole = User::whereHas('roles', fn ($q) => $q->where('name', 'it'))->get();
            $byPerm = User::whereHas('permissions', fn ($q) => $q->where('name', 'approve_it'))->get();
        }

        // Tambahan: identifikasi via department/unit jika dipakai di sistem lo
        $byDept = User::whereHas('department', fn($q) => $q->where('nama', 'IT'))->get();
        $byUnit = User::whereHas('unit', fn($q) => $q->where('nama', 'IT')->orWhere('nama_unit', 'IT'))->get();

        return $byRole->merge($byPerm)->merge($byDept)->merge($byUnit)->unique('id')->values();
    }

    /**
     * Kirim notifikasi ke semua user IT, pakai sendNow biar langsung masuk DB.
     */
    private function notifyIT(BapForm $form, string $message, string $title): void
    {
        $itUsers = $this->itUsers();

        if ($itUsers->isEmpty()) {
            Log::warning('Notif IT: tidak ada user IT terdeteksi', ['bap_id' => $form->id]);
            return;
        }

        Notification::sendNow($itUsers, new NewBapCreated($form, $message, $title));

        Log::info('Notif IT terkirim', [
            'bap_id' => $form->id,
            'to_ids' => $itUsers->pluck('id')->all(),
            'title'  => $title,
        ]);
    }
    // ========= End Helper IT =========

    public function index(Request $request)
    {
        // ✅ default-kan ke BAP (biar index cuma tampil BAP)
        $filterJenis      = $request->input('jenis_form', 'BAP');
        $statusFilter     = $request->input('status');
        $startDate        = $request->input('start_date');
        $endDate          = $request->input('end_date');
        $keyword          = $request->input('keyword');
        $creatorId        = $request->input('creator_id');
        $divisiVerifikasi = $request->input('divisi_verifikasi');
        $unitId           = $request->input('unit_id');

        $bapForms        = collect();
        $kronologisForms = collect();

        // dropdown creator & divisi
        $users = User::select('id', 'nama')->orderBy('nama')->get();
        $divisions = BapForm::select('divisi_verifikasi')
            ->distinct()
            ->whereNotNull('divisi_verifikasi')
            ->pluck('divisi_verifikasi');
        $units = Unit::orderBy('nama_unit')->get(['id','nama_unit']);

        // ================= BAP ONLY (default) =================
        if ($filterJenis === 'BAP') {
            $query = BapForm::with([
                    'creator',
                    'latestLog.user',
                    'kepalaUnitUser',
                    'supervisionUser',
                    'managerUser',
                    'finalUser'
                ])
                // ⬇️ Filter visibilitas (IT/Mutu/SuperAdmin bebas; lainnya by dept/role/tag)
                ->visibleTo(auth()->user());

            // tanggal
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            } elseif ($startDate) {
                $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
            } elseif ($endDate) {
                $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
            }

            // keyword (judul / nama creator)
            if ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('judul', 'like', '%' . $keyword . '%')
                      ->orWhereHas('creator', function ($qc) use ($keyword) {
                          $qc->where('nama', 'like', '%' . $keyword . '%');
                      });
                });
            }

            // filter creator
            if ($creatorId) {
                $query->where('user_id', $creatorId);
            }

            // filter divisi verifikasi
            if ($divisiVerifikasi) {
                $query->where('divisi_verifikasi', $divisiVerifikasi);
            }

            // filter UNIT lewat relasi creator (users.unit_id)
            if ($unitId) {
                $query->whereHas('creator', fn($u) => $u->where('unit_id', $unitId));
            }

            $bapForms = $query->get()->map(function ($item) {
                $item->form_type = 'BAP';
                $item->status = $item->latestLog->aktivitas ?? $item->status ?? 'Pending';

                if ($item->latestLog && $item->latestLog->user) {
                    $item->latest_approval =
                        $item->latestLog->user->nama
                        . ' (' . ($item->latestLog->user->jabatan->nama ?? '-') . ') '
                        . $item->latestLog->created_at->format('d-m-Y H:i');
                } else {
                    $item->latest_approval = null;
                }

                return $item;
            });
        }

        // ================= KRONOLOGIS (hanya jika diminta) =================
        if ($filterJenis === 'Kronologis') {
            $query = KronologisForm::with([
                'creator',
                'managerUser',
                'finalUser'
            ]);

            // tanggal
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            } elseif ($startDate) {
                $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
            } elseif ($endDate) {
                $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
            }

            // keyword (judul / nama creator)
            if ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('judul', 'like', '%' . $keyword . '%')
                      ->orWhereHas('creator', function ($qc) use ($keyword) {
                          $qc->where('nama', 'like', '%' . $keyword . '%');
                      });
                });
            }

            // filter creator
            if ($creatorId) {
                $query->where('user_id', $creatorId);
            }

            // filter UNIT
            if ($unitId) {
                $query->whereHas('creator', fn($u) => $u->where('unit_id', $unitId));
            }

            $kronologisForms = $query->get()->map(function ($item) {
                $item->form_type = 'Kronologis';
                $item->latest_approval = null;

                if ($item->final_approved_at && $item->finalUser) {
                    $item->latest_approval = optional($item->finalUser)->nama
                        . ' (' . optional($item->finalUser->jabatan)->nama . ') '
                        . Carbon::parse($item->final_approved_at)->format('d-m-Y H:i');
                } elseif ($item->manager_approved_at && $item->managerUser) {
                    $item->latest_approval = optional($item->managerUser)->nama
                        . ' (Manager) '
                        . Carbon::parse($item->manager_approved_at)->format('d-m-Y H:i');
                }

                $item->status = $item->status ?? 'Pending';
                return $item;
            });
        }

        // gabung sesuai jenis yang dipilih
        $formHistories = $bapForms->concat($kronologisForms)->sortByDesc('created_at')->values();

        // filter status (client side)
        if ($statusFilter) {
            $formHistories = $formHistories->filter(function ($item) use ($statusFilter) {
                return str_contains(strtolower($item->status), strtolower($statusFilter));
            })->values();
        }

        // paginate manual collection
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage     = 10;
        $paged       = $formHistories->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $formHistoriesPaginated = new LengthAwarePaginator(
            $paged,
            $formHistories->count(),
            $perPage,
            $currentPage,
            [
                'path'  => $request->url(),
                'query' => $request->query()
            ]
        );

        // rekap berdasarkan data yang ditampilkan saja
        $source = $formHistories; // ini sudah BAP-only saat default
        $totalToday   = $source->where('created_at', '>=', now()->startOfDay())->count();
        $totalSelesai = $source->where('status', 'Selesai')->count();
        $totalPending = $source->filter(fn ($i) => $i->status !== 'Selesai')->count();

        return view('sistem_sdm.index', [
            'formHistories' => $formHistoriesPaginated,
            'totalToday'    => $totalToday,
            'totalSelesai'  => $totalSelesai,
            'totalPending'  => $totalPending,
            'users'         => $users,
            'divisions'     => $divisions,
            'units'         => $units,
        ]);
    }

    public function storeBap(Request $request)
    {
        $this->abortIfMutu();

        $request->validate([
            'judul'               => 'nullable|string|max:255',
            'divisi_verifikasi'   => 'required|string|max:255',
            'perbaikan'           => 'nullable|array',
            'tindakan_medis'      => 'nullable|string|max:255',
            'lain_lain'           => 'nullable|string|max:255',
            'permasalahan_lain'   => 'nullable|string',
        ]);

        $user = auth()->user();
        $jabatan = strtolower(optional($user->jabatan)->nama ?? '');
        $isManager = str_contains($jabatan, 'manager') || str_contains($jabatan, 'manajer');

        $finalUser = User::whereHas('jabatan', function ($q) {
                $q->whereIn('nama', ['Supervision', 'Manager', 'Manajer', 'Kepala Unit']);
            })
            ->whereHas('department', function ($q) use ($request) {
                $q->where('nama', $request->divisi_verifikasi);
            })
            ->first();

        if (!$finalUser) {
            return back()->with('error', 'Tidak ditemukan user final approver di unit tujuan. Silakan pastikan ada Manager / Supervision di divisi tujuan.');
        }

        $form = BapForm::create([
            'user_id'             => $user->id,
            'judul'               => $request->filled('judul')
                                        ? $request->judul
                                        : 'Form BAP - ' . now()->format('d-m-Y H:i'),
            'deskripsi'           => '-',
            'status'              => $isManager ? 'Menunggu Unit Terkait' : 'Pending',
            'jenis_form'          => 'BAP',
            'divisi_verifikasi'   => $request->divisi_verifikasi,
            'perbaikan'           => $request->perbaikan ?: null, // sudah casts: array
            'tindakan_medis'      => $request->tindakan_medis,
            'lain_lain'           => $request->lain_lain,
            'permasalahan_lain'   => $request->permasalahan_lain,
            'manager_user_id'     => $isManager ? $user->id : null,
            'manager_approved_at' => $isManager ? now() : null,
            'final_user_id'       => $finalUser->id
        ]);

        BapStatusLog::create([
            'bap_form_id' => $form->id,
            'aktivitas'   => 'Dibuat',
            'user_id'     => $user->id,
            'keterangan'  => 'Form BAP dibuat oleh ' . $user->nama,
        ]);

        // 📝 activity log
        if (function_exists('activity_log')) {
            activity_log('bap.create', $form, "Buat BAP #{$form->id}", [
                'divisi_verifikasi' => $form->divisi_verifikasi,
                'is_manager' => $isManager,
            ]);
        }

        // 🔔 Notifikasi BAP masuk ke SPV, Manager, dan Kepala Unit (departemen pembuat)
$noBap = $form->no_bap ?? ('BAP#' . $form->id);

// yang lama: SPV + Manager berdasarkan jabatan_id
$recipients = User::whereIn('jabatan_id', [3, 4]) // 3=SPV, 4=Manager (biarkan)
    ->where('department_id', $user->department_id)
    ->whereKeyNot($user->id)
    ->get();

// ⬇️ TAMBAHAN: Kepala Unit berdasarkan nama jabatan (biar gak tebak ID)
$kepalaUnit = User::whereHas('jabatan', fn($q) => $q->where('nama', 'Kepala Unit'))
    ->where('department_id', $user->department_id)
    ->whereKeyNot($user->id)
    ->get();

// gabungkan & hilangkan duplikat
$recipients = $recipients->merge($kepalaUnit)->unique('id')->values();

if ($recipients->isNotEmpty()) {
    Notification::sendNow(
        $recipients,
        new NewBapCreated(
            $form,
            "Form {$noBap} telah dibuat oleh {$user->nama} dan menunggu persetujuan.",
            'BAP Baru Masuk'
        )
    );
}

        return redirect()->route('bap.index')->with('success', 'Form BAP berhasil disimpan!');
    }

    public function approveKepalaUnit($id)
{
    $this->abortIfMutu();

    $form = BapForm::findOrFail($id);
    $user = auth()->user();

    if (
        strtolower($user->jabatan->nama) === 'kepala unit' &&
        $user->department_id === $form->creator->department_id
    ) {
        $form->kepala_unit_user_id = $user->id;
        $form->kepala_unit_approved_at = now();
        $form->save();

        BapStatusLog::create([
            'bap_form_id' => $form->id,
            'aktivitas'   => 'Disetujui Unit',
            'user_id'     => $user->id,
            'keterangan'  => 'Form BAP disetujui oleh Kepala Unit',
        ]);

        // 📝 activity log
        if (function_exists('activity_log')) {
            activity_log('bap.approve_kepala_unit', $form, "Kepala Unit approve BAP #{$form->id}");
        }

        // 🔔 [BARU] Kabarin pembuat BAP
        if ($form->creator) {
            $noBap = $form->no_bap ?? ('BAP#' . $form->id);
            Notification::sendNow(
                $form->creator,
                new NewBapCreated(
                    $form,
                    "BAP kamu sudah di-approve Kepala Unit ({$user->nama}).",
                    'BAP Disetujui Kepala Unit'
                )
            );
        }

        // 🔔 [BARU] Kabarin SPV & Manager supaya lanjut approve
        $nextApprovers = User::whereIn('jabatan_id', [3, 4]) // 3 = SPV, 4 = Manager
            ->where('department_id', $form->creator->department_id)
            ->whereKeyNot($user->id) // jangan kirim ke KU yang baru approve
            ->get();

        if ($nextApprovers->isNotEmpty()) {
            $noBap = $form->no_bap ?? ('BAP#' . $form->id);
            Notification::sendNow(
                $nextApprovers,
                new NewBapCreated(
                    $form,
                    "BAP {$noBap} sudah di-approve Kepala Unit ({$user->nama}). Mohon lanjutkan approval.",
                    'BAP Menunggu Approval Berikutnya'
                )
            );
        }

        return back()->with('success', 'Form berhasil di-approve Kepala Unit.');
    }

    return back()->with('error', 'Anda tidak berhak approve sebagai Kepala Unit.');
}


    public function approveSupervision($id)
    {
        $this->abortIfMutu();

        $form = BapForm::findOrFail($id);
        $user = auth()->user();

        if (
            strtolower($user->jabatan->nama) === 'supervision' &&
            $user->department_id === $form->creator->department_id
        ) {
            $form->supervision_user_id = $user->id;
            $form->supervision_approved_at = now();
            $form->save();

            BapStatusLog::create([
                'bap_form_id' => $form->id,
                'aktivitas'   => 'Disetujui Supervision',
                'user_id'     => $user->id,
                'keterangan'  => 'Form BAP disetujui oleh Supervision',
            ]);

            // 📝 activity log
            if (function_exists('activity_log')) {
                activity_log('bap.approve_supervision', $form, "Supervision approve BAP #{$form->id}");
            }

            // 🔔 Notif ke pembuat BAP (guard)
            if ($form->creator) {
                Notification::sendNow(
                    $form->creator,
                    new NewBapCreated(
                        $form,
                        "BAP telah di-approve Supervision ({$user->nama}).",
                        'BAP Disetujui Supervision'
                    )
                );
            }

            // 👉 Kirim ke Mutu HANYA jika Manager sudah approve
            $form->loadMissing('managerUser');
            if ($form->manager_approved_at) {
                $noBap   = $form->no_bap ?? ('BAP#' . $form->id);
                $mgrName = optional($form->managerUser)->nama ?: 'Manager';
                $this->notifyMutu(
                    $form,
                    "Form {$noBap} sudah di-approve SPV ({$user->nama}) dan {$mgrName}. Mohon verifikasi Mutu (cek, tag unit, approve).",
                    'BAP Menunggu Verifikasi Mutu'
                );
            }

            return back()->with('success', 'Form berhasil di-approve Supervision.');
        }

        return back()->with('error', 'Anda tidak berhak approve sebagai Supervision.');
    }

    public function approve($id)
    {
        $this->abortIfMutu();

        $form = BapForm::findOrFail($id);
        $user = auth()->user();

        // PROSES APPROVAL MANAGER
        if (
            user_can('acc_manager_bap') &&
            !$form->manager_approved_at
        ) {
            $form->manager_user_id = $user->id;
            $form->manager_approved_at = now();
            $form->status = 'Menunggu Unit Terkait';
            $form->save();

            BapStatusLog::create([
                'bap_form_id' => $form->id,
                'aktivitas'   => 'Disetujui Manager',
                'user_id'     => $user->id,
                'keterangan'  => 'Form BAP disetujui oleh Manager',
            ]);

            // 📝 activity log
            if (function_exists('activity_log')) {
                activity_log('bap.approve_manager', $form, "Manager approve BAP #{$form->id}");
            }

            // 🔔 Notif ke pembuat BAP (guard)
            if ($form->creator) {
                Notification::sendNow(
                    $form->creator,
                    new NewBapCreated(
                        $form,
                        "BAP telah di-approve Manager ({$user->nama}).",
                        'BAP Disetujui Manager'
                    )
                );
            }

          // 👉 Kirim ke Mutu jika SPV **ATAU** Kepala Unit sudah approve
$form->loadMissing(['supervisionUser','kepalaUnitUser']);

if ($form->supervision_approved_at || $form->kepala_unit_approved_at) {
    $noBap = $form->no_bap ?? ('BAP#' . $form->id);

    // buat teks siapa yang sudah approve sebelum Manager
    $byWho = [];
    if ($form->supervision_approved_at) {
        $byWho[] = 'SPV (' . (optional($form->supervisionUser)->nama ?: '-') . ')';
    }
    if ($form->kepala_unit_approved_at) {
        $byWho[] = 'Kepala Unit (' . (optional($form->kepalaUnitUser)->nama ?: '-') . ')';
    }

    $this->notifyMutu(
        $form,
        "Form {$noBap} sudah di-approve Manager ({$user->nama}) setelah " . implode(' & ', $byWho) . ". Mohon verifikasi Mutu (cek, tag unit, approve).",
        'BAP Menunggu Verifikasi Mutu'
    );
}


            return redirect()->route('bap.detail', $form->id)
                ->with('success', 'Form berhasil di-approve Manager. Silakan lanjutkan Finalisasi di Unit Terkait.');
        }

        // PROSES APPROVAL FINAL
        if (
            user_can('acc_final_bap') &&
            !$form->final_approved_at &&
            $form->manager_approved_at &&
            $form->mutu_approved_at
        ) {
            $form->final_user_id = $user->id;
            $form->final_approved_at = now();
            $form->status = 'Selesai';
            $form->save();

            BapStatusLog::create([
                'bap_form_id' => $form->id,
                'aktivitas'   => 'Selesai',
                'user_id'     => $user->id,
                'keterangan'  => 'Form BAP telah selesai oleh unit terkait',
            ]);

            // 📝 activity log
            if (function_exists('activity_log')) {
                activity_log('bap.finalize', $form, "Final approve (Unit Terkait) BAP #{$form->id}");
            }

            // 🔔 Notif ke pembuat BAP (guard)
            if ($form->creator) {
                Notification::sendNow(
                    $form->creator,
                    new NewBapCreated(
                        $form,
                        "BAP selesai oleh Unit Terkait ({$user->nama}).",
                        'BAP Selesai'
                    )
                );
            }

            return redirect()->route('bap.detail', $form->id)
                ->with('success', 'Form berhasil di-finalisasi oleh Unit Terkait.');
        }

        return back()->with('error', 'Approval tidak diizinkan atau sudah dilakukan.');
    }

    public function detail($id)
    {
       $form = BapForm::with([
            'creator.jabatan',
            'kepalaUnitUser.jabatan',
            'supervisionUser.jabatan',
            'managerUser.jabatan',
            'finalUser.jabatan',
            'mutuUser',
            'kronologis.creator',
            'taggedUnits',
            'tagLogs.actor',
            'tagLogs.unit',
        ])->findOrFail($id);

        // ⬇️ WAJIB: cegat akses yang tak berhak
        $this->authorize('view-bap', $form);

        // [BARU] Flag untuk tombol “Tambah/Isi Kronologis” (IT, Mutu, SPV/Manager sesuai Gate)
        $canWriteKronologis = Gate::allows('write-kronologis', $form);

        // (opsional) log view
        if (function_exists('activity_log')) {
            activity_log('bap.view', $form, "Lihat detail BAP #{$form->id}");
        }

        $user = auth()->user();
        $units = \App\Models\Unit::all();

        return view('sistem_sdm.bap.detail_bap', compact('form', 'user', 'units', 'canWriteKronologis'));
    }

    public function tagLogs(Request $request, $id)
    {
        $form = BapForm::findOrFail($id);

        $logs = BapUnitTagLog::with(['unit','actor'])
            ->where('bap_form_id', $form->id)
            ->when($request->unit_id, fn($q) => $q->where('unit_id', $request->unit_id))
            ->orderByDesc('id')
            ->get()
            ->map(fn($log) => [
                'id'     => $log->id,
                'unit'   => $log->unit?->nama_unit ?? '-',
                'by'     => $log->actor?->nama ?? '-',
                'action' => $log->action,
                'time'   => optional($log->created_at)->format('d-m-Y H:i'),
            ]);

        return response()->json(['data' => $logs]);
    }

    public function updateKendala(Request $request, $id)
    {
        $this->abortIfMutu();

        $form = BapForm::findOrFail($id);
        $user = auth()->user();

        if (
            !user_can('acc_final_bap') ||
            $form->final_approved_at ||
            $user->department_id !== optional($form->finalUser)->department_id
        ) {
            return back()->with('error', 'Anda tidak diizinkan memperbarui kendala atau form sudah difinalisasi.');
        }

        $request->validate([
            'kendala' => 'required|string',
        ]);

        $form->kendala = $request->kendala;
        $form->final_user_id = $user->id;
        $form->final_approved_at = now();
        $form->status = 'Selesai';
        $form->save();

        BapStatusLog::create([
            'bap_form_id' => $form->id,
            'aktivitas'   => 'Selesai',
            'user_id'     => $user->id,
            'keterangan'  => 'Kendala diperbarui oleh unit terkait',
        ]);

        // 📝 activity log
        if (function_exists('activity_log')) {
            activity_log('bap.update_kendala', $form, "Update kendala & finalisasi BAP #{$form->id}");
        }

        return back()->with('success', 'Kendala berhasil diperbarui oleh Unit Terkait.');
    }

    public function cetak($id)
    {
        $form = BapForm::with([
            'creator.jabatan',
            'itUser.jabatan',
            'managerUser.jabatan',
            'kepalaUnitUser.jabatan',
            'supervisionUser.jabatan',
            'finalUser.jabatan',
            'mutuUser.jabatan',
            'taggedUnits',
            'kronologis.creator',
        ])->findOrFail($id);

        // ⬇️ WAJIB: cegat akses PDF bagi yang tak berhak
        $this->authorize('view-bap', $form);

        // 📝 activity log (cetak)
        if (function_exists('activity_log')) {
            activity_log('bap.print', $form, "Cetak PDF BAP #{$form->id}");
        }

        // kirim juga user login & (opsional) daftar unit kalau dipakai di blade
        $user  = auth()->user()->loadMissing('jabatan', 'unit', 'department');
        $units = \App\Models\Unit::all();

        $pdf = Pdf::loadView('sistem_sdm.bap.detail_bap_pdf', [
            'form'  => $form,
            'user'  => $user,
            'units' => $units,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('BAP-' . ($form->no_bap ?? $form->id) . '.pdf');
    }

    public function update(Request $request, $id)
    {
        $this->abortIfMutu();

        $form = BapForm::findOrFail($id);

        if ($form->status !== 'Pending' || $form->user_id !== auth()->id()) {
            return back()->with('error', 'Form tidak bisa diupdate.');
        }

        $request->validate([
            'judul'               => 'nullable|string|max:255',
            'divisi_verifikasi'   => 'required|string|max:255',
            'perbaikan'           => 'nullable|array',
            'tindakan_medis'      => 'nullable|string|max:255',
            'lain_lain'           => 'nullable|string|max:255',
            'permasalahan_lain'   => 'nullable|string',
        ]);

        $form->judul = $request->filled('judul') ? $request->judul : $form->judul;
        $form->divisi_verifikasi = $request->divisi_verifikasi;
        $form->perbaikan = $request->perbaikan ?: null; // jangan json_encode
        $form->tindakan_medis = $request->tindakan_medis;
        $form->lain_lain = $request->lain_lain;
        $form->permasalahan_lain = $request->permasalahan_lain;
        $form->save();

        // 📝 activity log
        if (function_exists('activity_log')) {
            activity_log('bap.update', $form, "Update BAP #{$form->id}");
        }

        return redirect()->route('bap.detail', $form->id)->with('success', 'Form BAP berhasil diperbarui.');
    }

    public function tagUnit(Request $request, $id)
    {
        $form = BapForm::with('taggedUnits')->findOrFail($id);

        if (!user_can('acc_mutu_bap')) {
            return back()->with('error', 'Akses ditolak.');
        }

        $validated = $request->validate([
            'unit_ids'   => 'required|array',
            'unit_ids.*' => 'exists:units,id',
        ]);

        $userId = auth()->id();
        $newIds = collect($validated['unit_ids'])->unique()->values()->all();
        $currentIds = $form->taggedUnits->pluck('id')->all();

        $toAttach = array_diff($newIds, $currentIds);
        $toDetach = array_diff($currentIds, $newIds);

        DB::transaction(function () use ($form, $toAttach, $toDetach, $userId) {
            // attach baru + simpan tagged_by di pivot
            if (!empty($toAttach)) {
                $attachData = [];
                foreach ($toAttach as $uid) {
                    $attachData[$uid] = ['tagged_by' => $userId];
                }
                $form->taggedUnits()->attach($attachData);

                foreach ($toAttach as $uid) {
                    BapUnitTagLog::create([
                        'bap_form_id' => $form->id,
                        'unit_id'     => $uid,
                        'tagged_by'   => $userId,
                        'action'      => 'ADD',
                    ]);
                }
            }

            // detach yang dihapus + catat log
            if (!empty($toDetach)) {
                $form->taggedUnits()->detach($toDetach);

                foreach ($toDetach as $uid) {
                    BapUnitTagLog::create([
                        'bap_form_id' => $form->id,
                        'unit_id'     => $uid,
                        'tagged_by'   => $userId,
                        'action'      => 'REMOVE',
                    ]);
                }
            }
        });

        // 📝 activity log ringkas
        if (function_exists('activity_log')) {
            activity_log('bap.tag_units', $form, "Perbarui tag unit BAP #{$form->id}", [
                'added'   => array_values($toAttach),
                'removed' => array_values($toDetach),
            ]);
        }

        // kirim notifikasi hanya untuk unit yang BARU ditag (opsional)
        if (!empty($toAttach)) {
            $users = User::whereIn('unit_id', $toAttach)->get();
            Notification::sendNow($users, new UnitTagged($form));
            foreach ($users as $u) {
                Log::info('✅ Notifikasi dikirim ke', [
                    'nama'   => $u->nama,
                    'unit'   => optional($u->unit)->nama_unit,
                    'bap_id' => $form->id,
                ]);
            }
        }

        return back()->with('success', 'Tag unit berhasil diperbarui & riwayat tersimpan.');
    }

    public function destroy($id)
    {
        $this->abortIfMutu();

        $bap = BapForm::find($id);
        $kronologis = KronologisForm::find($id);

        if ($bap) {
            // 📝 activity log (sebelum delete)
            if (function_exists('activity_log')) {
                activity_log('bap.delete', $bap, "Hapus BAP #{$bap->id}");
            }

            $bap->delete();
            return redirect()->route('bap.index')->with('success', 'Form BAP berhasil dihapus.');
        } elseif ($kronologis) {
            // 📝 activity log (fallback jika route ini dipakai hapus kron)
            if (function_exists('activity_log')) {
                activity_log('kronologis.delete_via_bap', $kronologis, "Hapus Kronologis #{$kronologis->id} via BAPController");
            }

            $kronologis->delete();
            return redirect()->route('bap.index')->with('success', 'Form Kronologis berhasil dihapus.');
        }

        return redirect()->route('bap.index')->with('error', 'Form tidak ditemukan.');
    }

    public function accMutu($id)
    {
        $form = BapForm::with(['taggedUnits', 'kronologis.creator'])->findOrFail($id);
        $user = auth()->user();

        if (!user_can('acc_mutu_bap')) {
            return back()->with('error', 'Anda tidak memiliki akses sebagai Mutu.');
        }

        if ($form->mutu_approved_at) {
            return back()->with('error', 'Form sudah di-approve oleh Mutu sebelumnya.');
        }

        if ($form->taggedUnits->isEmpty()) {
            return back()->with('error', 'Silakan tag unit-unit terkait terlebih dahulu sebelum menyetujui sebagai Mutu.');
        }

        $form->mutu_user_id = $user->id;
        $form->mutu_approved_at = now();
        $form->status = 'Menunggu IT';
        $form->save();

        BapStatusLog::create([
            'bap_form_id' => $form->id,
            'aktivitas'   => 'Disetujui Mutu',
            'user_id'     => $user->id,
            'keterangan'  => 'Form BAP disetujui oleh Mutu & dirilis ke IT.',
        ]);

        // 📝 activity log
        if (function_exists('activity_log')) {
            activity_log('bap.acc_mutu', $form, "ACC Mutu BAP #{$form->id}");
        }

        // 🔔 Notifikasi ke IT — sekarang boleh lihat
        $noBap = $form->no_bap ?? ('BAP#' . $form->id);
        $this->notifyIT(
            $form,
            "Form {$noBap} telah di-ACC Mutu dan siap ditindaklanjuti oleh IT.",
            'BAP Dirilis ke IT'
        );

        return back()->with('success', 'Form berhasil di-approve oleh Mutu dan telah dirilis ke IT.');
    }

    public function formBap()
    {
        return view('sistem_sdm.bap.form_bap');
    }
}



