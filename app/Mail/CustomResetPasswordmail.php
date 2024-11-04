<?php

namespace App\Mail;

use App\Http\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomResetPasswordmail extends Mailable
{
    use Queueable, SerializesModels;

    private $name;
    private $token;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name,$token)
    {
        $this->name = $name;
        $this->token = $token;
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
            '$reset_url' => route('password.reset', $this->token),
        ];

        $mail = new MailService();
        $mail = $mail->getTemplate('reset_password_email',$vars);
        return $this->html($mail['body'])->subject($mail['subject']);
    }
}
