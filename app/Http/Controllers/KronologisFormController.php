<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KronologisForm;
use App\Models\BapForm;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class KronologisFormController extends Controller
{
    public function create($bap_id)
    {
        $bap = BapForm::findOrFail($bap_id);

        // boleh buat kronologis hanya jika lolos gate
        if (Gate::denies('write-kronologis', $bap)) {
            abort(403, 'Anda tidak berhak menulis kronologis untuk BAP ini.');
        }

        return view('sistem_sdm.kronologis.form_kronologis', compact('bap'));
    }

    public function store(Request $request, $bap_id)
    {
        $bap = BapForm::findOrFail($bap_id);

        if (Gate::denies('write-kronologis', $bap)) {
            abort(403, 'Anda tidak berhak menulis kronologis untuk BAP ini.');
        }

        $request->validate([
            'tipe_kronologis' => 'required|in:Medis,Non-Medis',
            'judul'           => 'required|string|max:255',
            'tanggal'         => 'required|date',
            'deskripsi'       => 'required|string',
        ]);

        $tipe = $request->tipe_kronologis;

        if ($tipe === 'Medis') {
            $request->validate([
                'nama_pasien' => 'required|string|max:255',
                'no_rm'       => 'required|string|max:50',
                'diagnosa'    => 'required|string|max:255',
                'ruangan'     => 'required|string|max:255',
                'usia'        => 'required|integer|min:0',
            ]);
        }

        $user = Auth::user();

        // deteksi jabatan
        $jabatanStr = strtolower(
            optional($user->jabatan)->nama
            ?? optional($user->jabatan)->jabatan
            ?? ''
        );
        $isManager = str_contains($jabatanStr, 'manager');
        $isSpv     = str_contains($jabatanStr, 'spv') || str_contains($jabatanStr, 'supervisor');

        $k = KronologisForm::create([
            'bap_form_id'     => $bap_id,
            'user_id'         => $user->id,
            'tipe_kronologis' => $tipe,
            'judul'           => $request->judul,
            'tanggal'         => $request->tanggal,
            'deskripsi'       => $request->deskripsi,

            // field khusus medis
            'nama_pasien' => $tipe === 'Medis' ? $request->nama_pasien : null,
            'no_rm'       => $tipe === 'Medis' ? $request->no_rm : null,
            'diagnosa'    => $tipe === 'Medis' ? $request->diagnosa : null,
            'ruangan'     => $tipe === 'Medis' ? $request->ruangan : null,
            'usia'        => $tipe === 'Medis' ? $request->usia : null,

            // status awal
            'status'              => $isManager ? 'Telah Disetujui oleh Manager' : ($isSpv ? 'Diverifikasi SPV' : 'Pending'),
            'spv_user_id'         => $isSpv ? $user->id : null,
            'spv_approved_at'     => $isSpv ? now() : null,
            'manager_user_id'     => $isManager ? $user->id : null,
            'manager_approved_at' => $isManager ? now() : null,
        ]);

        // log
        if (function_exists('activity_log')) {
            activity_log(
                'kronologis.create',
                $k,
                "Buat kronologis #{$k->id} untuk BAP #{$bap_id}",
                ['bap_form_id' => $bap_id, 'tipe' => $tipe]
            );
        }

        return redirect()
            ->route('bap.detail', $bap_id)
            ->with('success', 'Form Kronologis berhasil ditambahkan.');
    }

    public function approve(Request $request, $id)
    {
        $form = KronologisForm::with('bapForm')->findOrFail($id);
        $user = auth()->user();

        if (Gate::denies('view-kronologis', $form)) {
            return back()->with('error', 'Anda tidak berhak mengakses kronologis ini.');
        }
        if ($user->id === $form->user_id) {
            return back()->with('error', 'Tidak bisa approve form yang Anda buat.');
        }

        $jabatanStr = strtolower(
            optional($user->jabatan)->nama
            ?? optional($user->jabatan)->jabatan
            ?? ''
        );

        DB::transaction(function () use ($form, $user, $jabatanStr) {
            if (!$form->spv_approved_at && (str_contains($jabatanStr, 'spv') || str_contains($jabatanStr, 'supervisor'))) {
                $form->spv_user_id     = $user->id;
                $form->spv_approved_at = now();
                $form->status          = 'Diverifikasi SPV';
            } elseif (!$form->manager_approved_at && str_contains($jabatanStr, 'manager')) {
                $form->manager_user_id     = $user->id;
                $form->manager_approved_at = now();
                $form->status              = 'Telah Disetujui oleh Manager';
            } elseif (
                !$form->final_approved_at &&
                function_exists('user_can') && user_can('acc_final_kronologis')
            ) {
                if (!$form->manager_approved_at) {
                    abort(400, 'Form belum disetujui oleh Manager.');
                }
                $form->final_user_id     = $user->id;
                $form->final_approved_at = now();
                $form->status            = 'Selesai';
            } else {
                abort(400, 'Approval tidak diizinkan atau sudah dilakukan.');
            }

            $form->save();

            // update status BAP bila semua kronologis selesai
            $bap = $form->bapForm->fresh('kronologis');
            $allDone = $bap->kronologis->every(fn ($k) => $k->status === 'Selesai');
            if ($bap->status === 'Telah Disetujui oleh Manager' && $allDone) {
                $bap->status = 'Selesai (Manager ACC)';
                $bap->save();
            }
        });

        if (function_exists('activity_log')) {
            activity_log('kronologis.approve', $form, "Approve kronologis #{$form->id}", [
                'status' => $form->status,
            ]);

            $bap = $form->bapForm()->with('kronologis')->first();
            if ($bap && $bap->status === 'Selesai (Manager ACC)') {
                activity_log('bap.autoflag_selesai', $bap, "Semua kronologis selesai untuk BAP #{$bap->id}");
            }
        }

        return back()->with('success', 'Form Kronologis berhasil di-approve.');
    }

    public function detail($id)
    {
        $form = KronologisForm::with([
            'creator.jabatan',
            'spvUser.jabatan',
            'managerUser.jabatan',
            'finalUser.jabatan',
            'bapForm'
        ])->findOrFail($id);

        Gate::authorize('view-kronologis', $form);

        if (function_exists('activity_log')) {
            activity_log('kronologis.view', $form, "Lihat detail Kronologis #{$form->id}");
        }

        $bapForm = $form->bapForm;

        return view('sistem_sdm.kronologis.detail_kronologis', compact('form', 'bapForm'));
    }

    public function edit($id)
    {
        $form = KronologisForm::findOrFail($id);

        Gate::authorize('view-kronologis', $form);

        if ($form->status !== 'Pending' || $form->user_id !== auth()->id()) {
            return back()->with('error', 'Form tidak bisa diedit.');
        }

        return view('sistem_sdm.kronologis.edit_kronologis', compact('form'));
    }

    public function update(Request $request, $id)
    {
        $form = KronologisForm::findOrFail($id);

        Gate::authorize('view-kronologis', $form);

        if ($form->status !== 'Pending' || $form->user_id !== auth()->id()) {
            return back()->with('error', 'Form tidak bisa diupdate.');
        }

        $request->validate([
            'tipe_kronologis' => 'required|in:Medis,Non-Medis',
            'judul'           => 'required|string|max:255',
            'tanggal'         => 'required|date',
            'deskripsi'       => 'required|string',
        ]);

        $tipe = $request->tipe_kronologis;

        if ($tipe === 'Medis') {
            $request->validate([
                'nama_pasien' => 'required|string|max:255',
                'no_rm'       => 'required|string|max:50',
                'diagnosa'    => 'required|string|max:255',
                'ruangan'     => 'required|string|max:255',
                'usia'        => 'required|integer|min:0',
            ]);
        }

        $form->tipe_kronologis = $tipe;
        $form->judul           = $request->judul;
        $form->tanggal         = $request->tanggal;
        $form->deskripsi       = $request->deskripsi;

        $form->nama_pasien = $tipe === 'Medis' ? $request->nama_pasien : null;
        $form->no_rm       = $tipe === 'Medis' ? $request->no_rm : null;
        $form->diagnosa    = $tipe === 'Medis' ? $request->diagnosa : null;
        $form->ruangan     = $tipe === 'Medis' ? $request->ruangan : null;
        $form->usia        = $tipe === 'Medis' ? $request->usia : null;

        $form->save();

        if (function_exists('activity_log')) {
            activity_log('kronologis.update', $form, "Update kronologis #{$form->id}");
        }

        return redirect()->route('kronologis.view', $form->id)
                         ->with('success', 'Form Kronologis berhasil diperbarui.');
    }

    public function cetak($id)
    {
        $form = KronologisForm::with([
            'creator.jabatan',
            'spvUser.jabatan',
            'managerUser.jabatan',
            'finalUser.jabatan',
            'bapForm'
        ])->findOrFail($id);

        Gate::authorize('view-kronologis', $form);

        if (function_exists('activity_log')) {
            activity_log('kronologis.print', $form, "Cetak PDF Kronologis #{$form->id}");
        }

        $pdf = Pdf::loadView('sistem_sdm.kronologis.kronologis_pdf', compact('form'));
        return $pdf->stream('Kronologis-' . $form->id . '.pdf');
    }

    public function destroy($id)
    {
        $form = KronologisForm::findOrFail($id);

        Gate::authorize('view-kronologis', $form);

        if ($form->status !== 'Pending' || $form->user_id !== auth()->id()) {
            return back()->with('error', 'Form tidak bisa dihapus.');
        }

        if (function_exists('activity_log')) {
            activity_log('kronologis.delete', $form, "Hapus kronologis #{$form->id}");
        }

        $form->delete();
        return back()->with('success', 'Form Kronologis berhasil dihapus.');
    }

    public function mutuCheck(Request $request, $id)
    {
        $user = auth()->user();

        $isMutu =
            (function_exists('isUserMutu') && isUserMutu()) ||
            (method_exists($user, 'hasRole')      && $user->hasRole('mutu')) ||
            (method_exists($user, 'hasAccess')    && (
                $user->hasAccess('acc_mutu_bap') ||
                $user->hasAccess('approve_mutu') ||
                $user->hasAccess('mutu_read')     ||
                $user->hasAccess('mutu')
            )) ||
            (method_exists($user, 'hasPrivilege') && (
                $user->hasPrivilege('acc_mutu_bap') ||
                $user->hasPrivilege('approve_mutu') ||
                $user->hasPrivilege('mutu')
            ));

        if (! $isMutu) {
            abort(403, 'Hanya Mutu yang bisa melakukan aksi ini.');
        }

        $kron = KronologisForm::with('bapForm')->findOrFail($id);

        if (is_null($kron->mutu_checked_at)) {
            $kron->mutu_checked_at = now();
            $kron->mutu_checked_by = $user->id;
            $kron->save();

            if (function_exists('activity_log')) {
                activity_log(
                    'kronologis.mutu_checked',
                    $kron,
                    "Mutu menandai kronologis #{$kron->id} sudah dibaca",
                    ['bap_form_id' => $kron->bap_form_id]
                );
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok'              => true,
                'mutu_checked_at' => optional($kron->mutu_checked_at)->toDateTimeString(),
                'message'         => 'Kronologis ditandai sebagai sudah dibaca Mutu.'
            ]);
        }

        return back()->with('success', 'Kronologis ditandai sebagai sudah dibaca Mutu.');
    }
}
