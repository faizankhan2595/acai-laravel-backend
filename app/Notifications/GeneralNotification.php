<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidFcmOptions;
use NotificationChannels\Fcm\Resources\AndroidNotification;
use NotificationChannels\Fcm\Resources\ApnsConfig;
use NotificationChannels\Fcm\Resources\ApnsFcmOptions;
use Illuminate\Support\Facades\Log;

class GeneralNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected $data;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->data['image'] = (array_key_exists('image',$data)) ? $data['image'] : null;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // Log the notification attempt
        Log::info('Sending notification via channels', [
            'user_id' => $notifiable->id,
            'fcm_token' => $notifiable->fcm_token ?? 'no_token',
            'title' => $this->data['title']
        ]);

        return [FcmChannel::class,'database'];
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
            'title' => $this->data['title'],
            'message' => $this->data['message'],
        ];
    }

    public function toFcm($notifiable)
    {
        try {
            Log::info('Creating FCM message', [
                'user_id' => $notifiable->id,
                'title' => $this->data['title']
            ]);

            return FcmMessage::create()
                ->setData(['type' => 'general_notification'])
                ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
                    ->setTitle($this->data['title'])
                    ->setBody($this->data['message'])
                    ->setImage($this->data['image']))
                ->setAndroid(
                    AndroidConfig::create()
                        ->setFcmOptions(AndroidFcmOptions::create()->setAnalyticsLabel('analytics'))
                        ->setNotification(AndroidNotification::create()->setColor('#0A0A0A'))
                )->setApns(
                    ApnsConfig::create()
                        ->setFcmOptions(ApnsFcmOptions::create()->setAnalyticsLabel('analytics_ios')->setImage($this->data['image']))
                        ->setPayload(['aps' => ['sound' => 'default']])
                    ); // Log the FCM message for debugging
            
            Log::info('FCM message created', [
                'user_id' => $notifiable->id,
                'message' => json_encode($fcmMessage)
            ]);

            return $fcmMessage;

        } catch (\Exception $e) {
            Log::error('Error creating FCM message', [
                'user_id' => $notifiable->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}
