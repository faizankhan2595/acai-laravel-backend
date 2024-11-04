<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidFcmOptions;
use NotificationChannels\Fcm\Resources\AndroidNotification;
use NotificationChannels\Fcm\Resources\ApnsConfig;
use NotificationChannels\Fcm\Resources\ApnsFcmOptions;

class ScanSuccessForSales extends Notification
{
    use Queueable;
    protected $title;
    protected $message;
    public $points;
    public $date;
    public $time;
    public $balance;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($qrcode)
    {
        $this->title   = 'QR code scanned by ' . $qrcode->scannedBy->name;
        $this->message = $qrcode->scannedBy->name . ' scanned the QR code, ' . $qrcode->points . ' Points added to user\'s account.';

        //data
        $this->points  = strval($qrcode->points);
        $this->date    = $qrcode->scanned_on->format('d M Y');
        $this->time    = $qrcode->scanned_on->format('g:i A');
        $this->balance = strval($qrcode->scannedBy->balance());
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [FcmChannel::class, 'database'];
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
            'title'   => $this->title,
            'message' => $this->message,
        ];
    }

    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->setData([
                'type'    => 'qr_scan_success',
                'points'  => $this->points,
                'date'    => $this->date,
                'time'    => $this->time,
                'balance' => $this->balance,
                'sound'   => "default",
            ])
            ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
                    ->setTitle($this->title)
                    ->setBody($this->message))
            ->setAndroid(
                AndroidConfig::create()
                    ->setFcmOptions(AndroidFcmOptions::create()->setAnalyticsLabel('analytics'))
                    ->setNotification(AndroidNotification::create()->setColor('#0A0A0A'))
            )->setApns(
            ApnsConfig::create()
                ->setFcmOptions(ApnsFcmOptions::create()->setAnalyticsLabel('analytics_ios'))
                ->setPayload(['aps' => ['sound' => 'default']])
            );
    }
}
