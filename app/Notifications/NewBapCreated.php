<?php

namespace App\Notifications;

use App\Models\BapForm;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBapCreated extends Notification
{
    use Queueable;

    protected BapForm $bap;
    protected ?string $message;
    protected ?string $title;

    public function __construct(BapForm $bap, ?string $message = null, ?string $title = null)
    {
        // preload relasi yang sering dipakai
        $this->bap     = $bap->loadMissing('creator.unit', 'creator.department');
        $this->message = $message;
        $this->title   = $title;
    }

    public function via($notifiable)
    {
        // sinkron ke database (tanpa queue biar pasti masuk)
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $noBap    = $this->bap->no_bap ?? ('BAP#' . $this->bap->id);
        $unitName = optional($this->bap->creator?->unit)->nama_unit ?? '-';

        // amanin route
        try {
            $url = route('bap.detail', $this->bap->id);
        } catch (\Throwable $e) {
            $url = route('bap.index');
        }

        return [
    'title'   => $this->title ?? 'BAP Baru Masuk',
    'message' => $this->message ?? "Form {$noBap} dari unit {$unitName} menunggu persetujuan.",
    'context' => 'bap',
    'target'  => 'bap', // ⬅️ TAMBAH INI
    'bap_id'  => $this->bap->id,
    'no_bap'  => $noBap,
    'unit'    => $unitName,
    'url'     => $url,
    'status'  => $this->bap->status,
    'creator' => optional($this->bap->creator)->nama,
];

    }
}
