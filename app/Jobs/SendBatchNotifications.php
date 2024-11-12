<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBatchNotifications implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userIds;
    protected $notification;

    public function __construct(array $userIds, array $notification)
    {
        $this->userIds = $userIds;
        $this->notification = $notification;
    }

    public function handle()
    {
        User::whereIn('id', $this->userIds)->each(function ($user) {
            $user->notify(new GeneralNotification($this->notification));
        });
    }
}