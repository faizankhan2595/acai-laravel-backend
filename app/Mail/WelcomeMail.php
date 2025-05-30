<?php

namespace App\Mail;

use App\Http\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $name;
    protected $email;
    protected $password;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name,$email,$password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
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
            '$email' => $this->email,
            '$password' => $this->password,
        ];

        $mail = new MailService();
        $mail = $mail->getTemplate('welcome_email',$vars);
        return $this->html($mail['body'])->subject($mail['subject']);
    }
}
