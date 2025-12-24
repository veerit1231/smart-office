<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DocumentDistributedNotification extends Notification
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
            'title'       => 'มีเอกสารใหม่ถึงคุณ',
            'message'     => "คุณได้รับเอกสารเลขที่ {$this->document->doc_no}",
            'url'         => route('documents.show', $this->document),
        ];
    }
}
