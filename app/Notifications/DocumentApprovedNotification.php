<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DocumentApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(private Document $document)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'document_id' => $this->document->id,
            'title'       => 'เอกสารได้รับการอนุมัติ',
            'message'     => "เอกสารเลขที่ {$this->document->doc_no} ได้รับการอนุมัติแล้ว",
            'url'         => route('documents.show', $this->document),
        ];
    }
    public function toArray($notifiable)
{
    return [
        'message' => 'มีเอกสารใหม่รอพิจารณา',
        'document_id' => $this->document->id,
        'url' => route('documents.show', $this->document->id),
    ];
}

}
