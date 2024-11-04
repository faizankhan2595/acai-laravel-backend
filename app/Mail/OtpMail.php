<?php

namespace App\Mail;

use App\Http\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;
    private $name;
    private $otp;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name,$otp)
    {
        $this->name = $name;
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $vars = [
            '$name' => ucfirst($this->name),
            '$otp' => $this->otp,
        ];

        $mail = new MailService();
        $mail = $mail->getTemplate('otp_email',$vars);
        return $this->html($mail['body'])->subject($mail['subject']);
    }
}
