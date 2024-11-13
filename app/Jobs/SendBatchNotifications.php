<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBatchNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userIds;
    protected $notification;

    public function __construct(array $userIds, array $notification)
    {
        $this->userIds = $userIds;
        $this->notification = $notification;
    }

    public function handle()
    {
        User::whereIn('id', $this->userIds)
            ->chunk(20, function($users) {
                foreach ($users as $user) {
                    try {
                        $user->notify(new GeneralNotification($this->notification));
                    } catch (\Exception $e) {
                        \Log::error('Failed to send notification to user ' . $user->id . ': ' . $e->getMessage());
                        continue;
                    }
                }
            });
    }
}