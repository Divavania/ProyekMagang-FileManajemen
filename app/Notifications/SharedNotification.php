<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SharedNotification extends Notification
{
    use Queueable;

    protected $item;     // file atau folder
    protected $sender;
    protected $message;
    protected $type;     // 'file' atau 'folder'

    public function __construct($item, $sender, $type = 'file', $message = null)
    {
        $this->item = $item;
        $this->sender = $sender;
        $this->type = $type;
        $this->message = $message;
    }

    // Kirim notifikasi via database
    public function via($notifiable)
    {
        return ['database'];
    }

    // Simpan ke database
    public function toDatabase($notifiable)
    {
        return [
            'title' => ucfirst($this->type) . ' dibagikan: ' . ($this->type === 'file' ? $this->item->file_name : $this->item->name),
            'message' => $this->sender->name . ' telah membagikan ' . $this->type . ' "' . 
             ($this->type === 'file' ? $this->item->file_name : $this->item->name) . 
             '" kepada Anda.' . ($this->message ? ' Pesan: ' . $this->message : ''),
            'item_type' => $this->type,
            'item_id' => $this->item->id,
            'link' => route('shared.index', ['type' => $this->type]), // arahkan ke halaman share index sesuai tipe
        ];
    }

}