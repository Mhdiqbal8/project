<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
   // App\Http\Controllers\ActivityLogController.php

public function index(Request $request)
{
    $this->authorize('view-activity-logs');

    $q      = trim($request->get('q', ''));
    $userId = $request->get('user_id');
    $action = $request->get('action');
    $from   = $request->get('from');
    $to     = $request->get('to');

    $logs = ActivityLog::with('user')
        ->when($q, function ($s) use ($q) {
            $s->where(function ($w) use ($q) {
                $w->where('action', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%")
                  ->orWhere('subject_type', 'like', "%{$q}%")
                  ->orWhere('url', 'like', "%{$q}%")
                  ->orWhere('ip_address', 'like', "%{$q}%");   // ✅ kolom benar
            })->orWhereHas('user', function ($u) use ($q) {
                $u->where('nama', 'like', "%{$q}%")
                  ->orWhere('username', 'like', "%{$q}%");
            });
        })
        ->when($userId, fn ($s) => $s->where('user_id', $userId))
        ->when($action, fn ($s) => $s->where('action', $action))
        ->when($from, fn ($s) => $s->whereDate('created_at', '>=', $from))
        ->when($to, fn ($s) => $s->whereDate('created_at', '<=', $to))
        ->orderByDesc('id')
        ->paginate(20)
        ->withQueryString();

    $actions = ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');

    return view('activity.index', compact('logs', 'actions'));
}

}
