<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\BapForm;

class UnitTagged extends Notification
{
    use Queueable;

    public $form;

    public function __construct(BapForm $form)
    {
        $this->form = $form;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => '📌 Penugasan Unit',
            'message' => 'Unit kamu ditugaskan untuk isi kronologis Form BAP #' . $this->form->id,
            'form_id' => $this->form->id,
            'unit' => $this->form->unit->nama_unit ?? 'Umum',
            'link' => route('bap.detail', $this->form->id),
        ];
    }
}
