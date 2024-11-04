<?php

namespace App\Mail;

use App\Http\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserExportMail extends Mailable
{
    use Queueable, SerializesModels;
    public $filename;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = new MailService();
        $mail = $mail->getTemplate('export_user_mail',[]);
        return $this->html($mail['body'])->subject($mail['subject'])->attachFromStorage($this->filename);
    }
}
