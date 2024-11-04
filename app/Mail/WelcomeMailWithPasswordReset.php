<?php

namespace App\Mail;

use App\Http\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMailWithPasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    protected $name;
    protected $email;
    protected $password;
    protected $reset_token;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name,$email,$password,$reset_token)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->reset_token = $reset_token;
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
            '$reset_url' => route('password.reset',$this->reset_token),
        ];

        $mail = new MailService();
        $mail = $mail->getTemplate('welcome_email_with_reset_password',$vars);
        return $this->html($mail['body'])->subject($mail['subject']);
    }
}
