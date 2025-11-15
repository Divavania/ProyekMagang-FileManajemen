<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SharedNotification extends Notification
{
    use Queueable;

    protected $file;
    protected $sender;
    protected $message;

    public function __construct($file, $sender, $message = null)
    {
        $this->file = $file;
        $this->sender = $sender;
        $this->message = $message;
    }

    // 🔔 Kirim notifikasi via database & email
    public function via($notifiable)
    {
        return ['database'];
    }

    // 💾 Simpan notifikasi ke database
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'File dibagikan: ' . $this->file->file_name,
            'message' => $this->sender->name . ' telah membagikan file kepada Anda.' .
                         ($this->message ? ' Pesan: ' . $this->message : ''),
            'file_id' => $this->file->id,
            'link' => route('shared.index'), 
        ];
    }

}