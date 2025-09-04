<?php

namespace App\Notifications;

use App\Models\Service;
use App\Models\RequestService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewServiceRequest extends Notification
{
    use Queueable;

    protected Service $service;
    protected string  $target;   // 'service' | 'request_service'
    protected ?string $title;    // judul custom (opsional)
    protected ?string $message;  // pesan custom (opsional)

    /**
     * @param Service     $service
     * @param string      $target   'service' untuk notifikasi yang mengarah ke halaman SERVICE,
     *                              'request_service' untuk yang mengarah ke halaman REQUEST SERVICE
     * @param string|null $message  pesan custom (contoh: "Sudah di-approve Supervisor")
     * @param string|null $title    judul custom (contoh: "Tiket Disetujui")
     */
    public function __construct(Service $service, string $target = 'service', ?string $message = null, ?string $title = null)
    {
        // butuh unit asal pemohon -> user.unit (kalau ga ada, ga masalah)
        $this->service = $service->loadMissing('user.unit');
        $this->target  = $target;
        $this->message = $message;
        $this->title   = $title;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $unitName = optional($this->service->user?->unit)->nama_unit
            ?? optional($this->service->unit)->nama_unit
            ?? 'Unit Tidak Dikenal';

        // tentukan URL tujuan sesuai target
        if ($this->target === 'request_service') {
            $rs  = RequestService::where('service_id', $this->service->id)->first();
            $url = $rs ? route('request_service.show', $rs->id)
                       : route('request_service.index');
        } else {
            $url = route('service.show', $this->service->id);
        }

        // default title & message kalau tidak diberikan
        $defaultTitle = $this->target === 'request_service'
            ? 'Tiket Masuk ke Unit Tujuan'
            : 'Permohonan Service';
        $defaultMessage = $this->target === 'request_service'
            ? "Tiket {$this->service->no_tiket} masuk ke unit tujuan."
            : "Permohonan {$this->service->no_tiket} dari unit {$unitName}.";

        return [
            'title'      => $this->title   ?? $defaultTitle,
            'message'    => $this->message ?? $defaultMessage,
            'service_id' => $this->service->id,
            'no_tiket'   => $this->service->no_tiket,
            'unit'       => $unitName,
            'target'     => $this->target,
            'url'        => $url,
        ];
    }
}
