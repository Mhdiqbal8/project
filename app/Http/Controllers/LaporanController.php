<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User;
use App\Models\BapForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema; // <— penting
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Exports\LaporanServiceExcel;

class LaporanController extends Controller
{
    /* ===================== Laporan Service (range tanggal – versi lama) ===================== */

    public function laporan_service()
    {
        return view('laporan.laporan_service');
    }

    protected function isIT(?User $user): bool
    {
        if (!$user) return false;
        $name = strtolower(optional($user->department)->nama ?? '');
        return in_array($name, ['it','ti','information technology','teknologi informasi'], true);
    }

    /***************** BASE QUERY: TABLE service *****************/
    private function baseQueryService(Carbon $start, Carbon $end, User $user)
    {
        $q = DB::table('service as s')
            ->join('users as a',  's.user_id',       '=', 'a.id')   // pemohon
            ->leftJoin('users as aa', 's.teknisi_id', '=', 'aa.id') // teknisi
            ->leftJoin('departments as b', 'a.department_id', '=', 'b.id')
            ->leftJoin('inventaris as c', 's.inventaris_id', '=', 'c.id')
            ->leftJoin('jenis_inventaris as d', 'c.jenis_inventaris_id', '=', 'd.id')
            ->leftJoin('units as u', 'a.unit_id', '=', 'u.id')
            ->whereBetween('s.created_at', [$start, $end]);

        if (!($user->isSuperAdmin() || $user->hasRole('Super-Admin') || $this->isIT($user))) {
            $q->where('a.department_id', $user->department_id);
        }

        return $q->selectRaw(
            's.id               as id_row,
             s.no_tiket         as no_tiket,
             a.nama             as pemohon,
             b.nama             as department,
             COALESCE(u.nama_unit, "") as nama_unit,
             d.jenis_inventaris as jenis_inventaris,
             c.nama             as inventaris,
             s.service          as service,
             s.biaya_service    as biaya_service,
             s.created_at       as created_at,
             s.keterangan       as keterangan,
             aa.nama            as nama_teknisi,
             s.status_id        as status_code,
             "service"          as sumber'
        );
    }

    /***************** BASE QUERY: TABLE request_service (opsional) *****************/
    private function baseQueryRequestService(Carbon $start, Carbon $end, User $user)
    {
        $q = DB::table('request_service as rs')
            ->join('users as a',  'rs.user_id',       '=', 'a.id')
            ->leftJoin('users as aa', 'rs.teknisi_id', '=', 'aa.id')
            ->leftJoin('departments as b', 'a.department_id', '=', 'b.id')
            ->leftJoin('units as u', 'a.unit_id', '=', 'u.id')
            ->leftJoin('inventaris as c', 'rs.inventaris_id', '=', 'c.id')
            ->leftJoin('jenis_inventaris as d', 'c.jenis_inventaris_id', '=', 'd.id')
            ->whereBetween('rs.created_at', [$start, $end]);

        if (!($user->isSuperAdmin() || $user->hasRole('Super-Admin') || $this->isIT($user))) {
            $q->where('a.department_id', $user->department_id);
        }

        return $q->selectRaw(
            'rs.id              as id_row,
             rs.no_tiket        as no_tiket,
             a.nama             as pemohon,
             b.nama             as department,
             COALESCE(u.nama_unit, "") as nama_unit,
             d.jenis_inventaris as jenis_inventaris,
             COALESCE(c.nama, rs.inventaris) as inventaris,
             COALESCE(rs.service, rs.jenis_service) as service,
             COALESCE(rs.biaya_service, rs.perkiraan_biaya, 0) as biaya_service,
             rs.created_at      as created_at,
             rs.keterangan      as keterangan,
             aa.nama            as nama_teknisi,
             rs.status_id       as status_code,
             "request_service"  as sumber'
        );
    }

    /** Laporan lama: range tanggal, pakai tabel service saja */
    protected function cek_data($start_date, $end_date)
    {
        $start = Carbon::parse($start_date)->startOfDay();
        $end   = Carbon::parse($end_date)->endOfDay();
        $user  = Auth::user()->load('akses');

        $STATUS_OK = [8, 9]; // sesuaikan

        return $this->baseQueryService($start, $end, $user)
            ->whereIn('s.status_id', $STATUS_OK) // prefix biar nggak ambigu
            ->orderBy('s.created_at')
            ->get();
    }

    /** API JSON (range tanggal) */
    public function search(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        try {
            $search = $this->cek_data($start_date, $end_date);
            return response()->json(['response' => 'success', 'search' => $search]);
        } catch (\Throwable $e) {
            Log::error('LaporanService search error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['response' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /** Export Excel (range tanggal) */
    public function search_excel(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        return Excel::download(
            new LaporanServiceExcel($start_date, $end_date),
            'Laporan_Service_'.$this->fmtRange($start_date, $end_date).'.xlsx'
        );
    }

    /** Export PDF batch (range tanggal) */
    public function search_pdf(Request $request)
    {
        @set_time_limit(180);
        @ini_set('max_execution_time', '180');
        @ini_set('memory_limit', '512M');

        $start_date   = $request->start_date;
        $end_date     = $request->end_date;
        $data_service = $this->cek_data($start_date, $end_date);

        $html = view('laporan.laporan_service_pdf', compact('data_service', 'start_date', 'end_date'))->render();

        $pdf = Pdf::loadHTML($html)
            ->setPaper('A4', 'landscape')
            ->setWarnings(false)
            ->setOptions([
                'isRemoteEnabled'         => false,
                'isHtml5ParserEnabled'    => true,
                'isFontSubsettingEnabled' => true,
                'dpi'                     => 96,
                'defaultFont'             => 'DejaVu Sans',
            ]);

        return $pdf->stream(
            'Laporan_Service_'.$this->fmtRange($start_date, $end_date).'.pdf',
            ['Attachment' => false]
        );
    }

    /** Export PDF single form service */
    public function search_pdf_single($id)
    {
        @set_time_limit(60);
        @ini_set('max_execution_time', '60');

        $data_service = Service::with(['user.department','user.unit','teknisi','inventaris.jenis_inventaris'])
            ->findOrFail($id);

        $manager = User::where('department_id', $data_service->user->department_id ?? null)
                       ->where('jabatan_id', 3)
                       ->first();

        $html = view('laporan.form_service_pdf', compact('data_service', 'manager'))->render();

        $pdf = Pdf::loadHTML($html)
            ->setPaper('A4', 'portrait')
            ->setWarnings(false)
            ->setOptions([
                'isHtml5ParserEnabled'    => true,
                'isFontSubsettingEnabled' => true,
                'dpi'                     => 96,
                'defaultFont'             => 'DejaVu Sans',
            ]);

        return $pdf->stream(
            'Form_Service_'.$data_service->created_at->format('Ymd').'.pdf',
            ['Attachment' => false]
        );
    }

    private function fmtRange($start, $end): string
    {
        return date('d_M_Y', strtotime($start)).'-'.date('d_M_Y', strtotime($end));
    }

    /* ===================== BAP (tanpa perubahan besar) ===================== */
    private function buildBapReport(Request $r): array
    {
        $m = (int)($r->input('month') ?? now()->month);
        $y = (int)($r->input('year')  ?? now()->year);

        $start = Carbon::create($y, $m, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $ranking = BapForm::query()
            ->whereBetween('bap_forms.created_at', [$start, $end])
            ->join('users as u', 'bap_forms.user_id', '=', 'u.id')
            ->leftJoin('units as un', 'u.unit_id', '=', 'un.id')
            ->groupBy('u.unit_id', 'un.nama_unit')
            ->orderByRaw('COUNT(*) DESC')
            ->get([
                DB::raw('COALESCE(un.nama_unit, "Tanpa Unit") as unit'),
                DB::raw('COUNT(*) as total'),
            ]);

        $total   = BapForm::whereBetween('bap_forms.created_at', [$start, $end])->count();
        $selesai = BapForm::whereBetween('bap_forms.created_at', [$start, $end])
                    ->whereNotNull('final_approved_at')->count();
        $pct     = $total ? round($selesai / $total * 100, 1) : 0.0;

        $months = collect(range(0,5))
            ->map(fn($i) => now()->startOfMonth()->subMonths($i))
            ->reverse()
            ->values();

        $timeline = $months->map(function ($mth) {
            $s = $mth->copy()->startOfMonth();
            $e = $mth->copy()->endOfMonth();

            $t = BapForm::whereBetween('bap_forms.created_at', [$s, $e])->count();
            $f = BapForm::whereBetween('bap_forms.created_at', [$s, $e])
                ->whereNotNull('final_approved_at')->count();

            return [
                'label' => $mth->format('M Y'),
                'pct'   => $t ? round($f / $t * 100, 1) : 0.0,
            ];
        });

        return [
            'year'           => $y,
            'month'          => $m,
            'start'          => $start,
            'end'            => $end,
            'ranking'        => $ranking,
            'total'          => $total,
            'selesai'        => $selesai,
            'pct'            => $pct,
            'timelineLabels' => $timeline->pluck('label'),
            'timelinePct'    => $timeline->pluck('pct'),
        ];
    }

    public function laporanBap(Request $r)
    {
        $data = $this->buildBapReport($r);
        return view('laporan.bap_index', $data);
    }

    public function bapPdf(Request $r)
    {
        @set_time_limit(120);
        @ini_set('memory_limit', '512M');

        $data = $this->buildBapReport($r);
        $fileName = sprintf('Laporan_BAP_%02d-%d.pdf', $data['month'], $data['year']);

        $pdf = Pdf::loadView('laporan.bap_pdf', $data)
            ->setPaper('A4', 'portrait')
            ->setWarnings(false)
            ->setOptions([
                'isHtml5ParserEnabled'    => true,
                'isFontSubsettingEnabled' => true,
                'dpi'                     => 96,
                'defaultFont'             => 'DejaVu Sans',
            ]);

        return $pdf->stream($fileName, ['Attachment' => false]);
    }

    /* ===================== Laporan Service BULANAN (gabungan & opsional) ===================== */

    public function serviceBulanan(Request $req)
    {
        $month = (int)($req->input('month') ?: now()->month);
        $year  = (int)($req->input('year')  ?: now()->year);

        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end   = (clone $start)->endOfMonth()->endOfDay();
        $user  = Auth::user();

        // mapping status (sesuaikan)
        $S_DONE_SVC = [9, 10];
        $S_PROG_SVC = [7, 8];
        $S_WAIT_SVC = [5, 6];
        $S_CLS_SVC  = [3, 4, 10];

        $S_DONE_REQ = [9, 10];
        $S_PROG_REQ = [7, 8];
        $S_WAIT_REQ = [5, 6];
        $S_CLS_REQ  = [3, 4, 10];

        $svc   = $this->baseQueryService($start, $end, $user);

        // hanya union kalau tabel request_service memang ada
        $hasReq = Schema::hasTable('request_service');
        if ($hasReq) {
            $reqQ  = $this->baseQueryRequestService($start, $end, $user);
            $base  = DB::query()->fromSub($svc->unionAll($reqQ), 'x');
        } else {
            $base  = DB::query()->fromSub($svc, 'x');
        }

        $total = (clone $base)->count();

        // helper kondisi status (kalau $hasReq false, branch request_service dilewati)
        $whereStatus = function ($q, array $svcIds, array $reqIds) use ($hasReq) {
            $q->where(function ($qq) use ($svcIds, $reqIds, $hasReq) {
                $qq->where(function ($w) use ($svcIds) {
                        $w->where('sumber', 'service')->whereIn('status_code', $svcIds);
                    });
                if ($hasReq) {
                    $qq->orWhere(function ($w) use ($reqIds) {
                        $w->where('sumber', 'request_service')->whereIn('status_code', $reqIds);
                    });
                }
            });
        };

        $selesai         = (clone $base)->where(fn($q) => $whereStatus($q, $S_DONE_SVC, $S_DONE_REQ))->count();
        $onprogress      = (clone $base)->where(fn($q) => $whereStatus($q, $S_PROG_SVC, $S_PROG_REQ))->count();
        $waiting         = (clone $base)->where(fn($q) => $whereStatus($q, $S_WAIT_SVC, $S_WAIT_REQ))->count();
        $closed_rejected = (clone $base)->where(fn($q) => $whereStatus($q, $S_CLS_SVC,  $S_CLS_REQ))->count();

        $pct = $total ? ($selesai / $total * 100) : 0;

        // ranking per unit
        $ranking = (clone $base)
            ->selectRaw('COALESCE(nama_unit, "Tanpa Unit") as unit, COUNT(*) as total')
            ->groupBy('nama_unit')
            ->orderByDesc('total')
            ->get();

        $donePerUnit = (clone $base)
            ->where(fn($q) => $whereStatus($q, $S_DONE_SVC, $S_DONE_REQ))
            ->selectRaw('COALESCE(nama_unit, "Tanpa Unit") as unit, COUNT(*) as done')
            ->groupBy('nama_unit')
            ->pluck('done', 'unit');

        $ranking = $ranking->map(function ($r) use ($donePerUnit) {
            $r->done = (int)($donePerUnit[$r->unit] ?? 0);
            $r->pct  = $r->total ? ($r->done / $r->total * 100) : 0;
            return $r;
        });

        // timeline 6 bulan terakhir
        $timelineLabels = [];
        $timelinePct    = [];
        for ($i = 5; $i >= 0; $i--) {
            $s = (clone $start)->copy()->subMonths($i)->startOfMonth();
            $e = (clone $s)->endOfMonth();

            $svc_i = $this->baseQueryService($s, $e, $user);

            if ($hasReq) {
                $req_i = $this->baseQueryRequestService($s, $e, $user);
                $u_i   = DB::query()->fromSub($svc_i->unionAll($req_i), 't');
            } else {
                $u_i   = DB::query()->fromSub($svc_i, 't');
            }

            $ttl = (clone $u_i)->count();
            $dn  = (clone $u_i)->where(fn($q) => $whereStatus($q, $S_DONE_SVC, $S_DONE_REQ))->count();

            $timelineLabels[] = $s->isoFormat('MMM YY');
            $timelinePct[]    = $ttl ? round($dn / $ttl * 100, 1) : 0;
        }

        return view('laporan.service_bulanan', compact(
            'month','year','start','end',
            'total','selesai','onprogress','waiting','closed_rejected','pct',
            'ranking','timelineLabels','timelinePct'
        ));
    }

    /***************** PDF BULANAN *****************/
    public function serviceBulananPdf(Request $req)
    {
        $view = $this->serviceBulanan($req);
        $data = $view->getData();

        $pdf = Pdf::loadView('laporan.service_bulanan_pdf', $data)
            ->setPaper('A4', 'landscape')
            ->setWarnings(false)
            ->setOptions([
                'isHtml5ParserEnabled'    => true,
                'isFontSubsettingEnabled' => true,
                'dpi'                     => 96,
                'defaultFont'             => 'DejaVu Sans',
            ]);

        $fileName = sprintf('Laporan_Service_Bulanan_%02d-%d.pdf', $data['month'], $data['year']);
        return $pdf->stream($fileName, ['Attachment' => false]);
    }
}
