<?php

// app/Listeners/DeleteExpiredNotificationTokens.php
namespace App\Listeners;

use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class DeleteExpiredNotificationTokens
{
    /**
     * Handle the event.
     */
    public function handle(NotificationFailed $event): void
    {
        try {
            $report = Arr::get($event->data, 'report');
            
            // Log the failure details
            Log::error('FCM Notification Failed', [
                'notifiable_id' => $event->notifiable->id ?? 'unknown',
                'notification_class' => get_class($event->notification),
                'error_data' => $event->data,
                'report' => $report
            ]);

        } catch (\Exception $e) {
            Log::error('Error in DeleteExpiredNotificationTokens listener', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
