<?php
namespace App\Notifications;

use App\Mail\WelcomeMailWithPasswordReset;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeWithPasswordReset extends Notification
{
    use Queueable;

    protected $name;
    protected $email;
    protected $password;
    protected $reset_token;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($name,$email, $password,$reset_token)
    {
        $this->name    = $name;
        $this->email    = $email;
        $this->password = $password;
        $this->reset_token = $reset_token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new WelcomeMailWithPasswordReset($this->name,$this->email,
            $this->password,$this->reset_token))->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
