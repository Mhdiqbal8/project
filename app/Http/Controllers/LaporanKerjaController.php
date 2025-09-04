<?php

namespace App\Http\Controllers;

use App\Models\LaporanKerja;
use App\Models\LaporanKerjaKomentar;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanKerjaExport;

class LaporanKerjaController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->hasAccess('laporan_it')) {
            abort(403, 'Akses ke laporan harian IT ditolak.');
        }

        $query = LaporanKerja::with(['user', 'approver', 'komentar']);

        if ($request->filled('tanggal')) {
            try {
                $tanggal = str_replace('/', '-', $request->tanggal);
                $date = Carbon::parse($tanggal)->format('Y-m-d');
                $query->whereDate('tanggal', $date);
            } catch (\Exception $e) {
                // format salah, diabaikan
            }
        }

        if ($request->filled('user_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->user_name . '%');
            });
        }

        if ($request->filled('komentar_status')) {
            if ($request->komentar_status === 'ada') {
                $query->whereHas('komentar', function ($q) {
                    $q->where('is_beres', false);
                });
            } elseif ($request->komentar_status === 'beres') {
                $query->whereDoesntHave('komentar', function ($q) {
                    $q->where('is_beres', false);
                })->has('komentar');
            }
        }

        $laporans = $query
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(10);

        $users = User::orderBy('nama')->get();

        return view('laporan_kerja.index', compact('laporans', 'users'));
    }

    public function create()
    {
        return view('laporan_kerja.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'shift' => 'required|string',
            'jam_in_mulai' => 'nullable',
            'jam_in_selesai' => 'nullable',
            'jam_out_mulai' => 'nullable',
            'jam_out_selesai' => 'nullable',
            'kegiatan_in' => 'nullable|string',
            'kegiatan_out' => 'nullable|string',
            'komentar_staff' => 'nullable|string',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'shift.required' => 'Shift wajib diisi.',
        ]);

        $laporan = LaporanKerja::create([
            'user_id' => auth()->id(),
            'tanggal' => $validated['tanggal'],
            'shift' => $validated['shift'],
            'jam_in_mulai' => $validated['jam_in_mulai'] ?? null,
            'jam_in_selesai' => $validated['jam_in_selesai'] ?? null,
            'jam_out_mulai' => $validated['jam_out_mulai'] ?? null,
            'jam_out_selesai' => $validated['jam_out_selesai'] ?? null,
            'kegiatan_in' => $validated['kegiatan_in'] ?? null,
            'kegiatan_out' => $validated['kegiatan_out'] ?? null,
            'komentar_staff' => $validated['komentar_staff'] ?? null,
            'status_id' => 1,
        ]);

        return redirect()->route('laporan-kerja.show', $laporan->id)
                         ->with('success', 'Laporan berhasil disimpan!');
    }

    public function show($id)
    {
        $laporan = LaporanKerja::with([
            'approver.jabatan',
            'komentar.user.jabatan'
        ])->findOrFail($id);

        return view('laporan_kerja.show', compact('laporan'));
    }

    public function edit($id)
    {
        $laporan = LaporanKerja::findOrFail($id);
        return view('laporan_kerja.edit', compact('laporan'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'shift' => 'required|string',
            'jam_in_mulai' => 'nullable',
            'jam_in_selesai' => 'nullable',
            'jam_out_mulai' => 'nullable',
            'jam_out_selesai' => 'nullable',
            'kegiatan_in' => 'nullable|string',
            'kegiatan_out' => 'nullable|string',
            'komentar_staff' => 'nullable|string',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'shift.required' => 'Shift wajib diisi.',
        ]);

        $laporan = LaporanKerja::findOrFail($id);
        $laporan->update([
            'tanggal' => $validated['tanggal'],
            'shift' => $validated['shift'],
            'jam_in_mulai' => $validated['jam_in_mulai'] ?? null,
            'jam_in_selesai' => $validated['jam_in_selesai'] ?? null,
            'jam_out_mulai' => $validated['jam_out_mulai'] ?? null,
            'jam_out_selesai' => $validated['jam_out_selesai'] ?? null,
            'kegiatan_in' => $validated['kegiatan_in'] ?? null,
            'kegiatan_out' => $validated['kegiatan_out'] ?? null,
            'komentar_staff' => $validated['komentar_staff'] ?? null,
        ]);

        return redirect()->route('laporan-kerja.show', $laporan->id)
                         ->with('success', 'Laporan berhasil diupdate!');
    }

    public function simpanKomentar(Request $request, $id)
    {
        $validated = $request->validate([
            'komentar' => 'required|string',
        ]);

        LaporanKerjaKomentar::create([
            'laporan_kerja_id' => $id,
            'user_id' => auth()->id(),
            'komentar' => $validated['komentar'],
        ]);

        return redirect()->route('laporan-kerja.show', $id)
                         ->with('success', 'Komentar berhasil dikirim!');
    }

    public function beresKomentar($id)
    {
        $komentar = LaporanKerjaKomentar::findOrFail($id);
        $komentar->update([
            'is_beres' => true,
        ]);

        return redirect()->route('laporan-kerja.show', $komentar->laporan_kerja_id)
                         ->with('success', 'Komentar berhasil ditandai sudah dibahas.');
    }

    public function cetak($id)
    {
        if (!auth()->user()->hasAccess('laporan_it')) {
            abort(403);
        }

        $laporan = LaporanKerja::with(['user.jabatan'])->findOrFail($id);

        $ttdPath = null;
        if ($laporan->user && $laporan->user->ttd_path) {
            $ttdPath = asset('storage/' . $laporan->user->ttd_path);
        }

        $pdf = Pdf::loadView('pdf.laporan_kerja', [
            'laporan' => $laporan,
            'ttdPath' => $ttdPath,
        ]);

        return $pdf->stream("Laporan_Harian_Kerja_{$laporan->id}.pdf");
    }

    public function exportExcel()
    {
        if (!auth()->user()->hasAccess('laporan_it')) {
            abort(403);
        }

        $filename = 'laporan_harian_kerja_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new LaporanKerjaExport, $filename);
    }
}
