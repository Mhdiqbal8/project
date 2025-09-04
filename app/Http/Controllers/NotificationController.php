<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
  public function read($id)
{
    if (!auth()->check()) {
        return redirect()->route('login')->with('error', 'Sesi kamu habis, silakan login ulang.');
    }

    $notif = auth()->user()
        ->notifications()              // bisa read maupun unread
        ->where('id', $id)
        ->firstOrFail();

    if (is_null($notif->read_at)) {
        $notif->markAsRead();
    }

    $link = $notif->data['link'] ?? route('home');
    return redirect($link);
}


    public function unread() // buat polling via AJAX
    {
        $user = auth()->user();

        return response()->json([
            'count' => $user->unreadNotifications()->count(),
            'items' => $user->unreadNotifications()->take(10)->get()->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->data['title'] ?? '📌 Notifikasi',
                    'message' => $n->data['message'] ?? '',
                    'time' => $n->created_at->diffForHumans(),
                    'link' => $n->data['link'] ?? null,
                ];
            }),
        ]);
    }
    
    public function readAll()
{
    $user = auth()->user();
    $user->unreadNotifications->markAsRead();

    return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
}


public function go($id)
{
    $notif = auth()->user()->notifications()->findOrFail($id);

    $url = data_get($notif->data, 'url') ?? data_get($notif->data, 'link') ?? route('home');

    if ($notif->unread()) {
        $notif->markAsRead();
    }
    return redirect()->to($url);
}


}
