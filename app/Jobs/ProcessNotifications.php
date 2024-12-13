<?php

namespace App\Jobs;

use App\Notifications\GeneralNotification;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProcessNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notification;
    protected $requestData;

    public $timeout = 21600;

    public function __construct(array $notification, array $requestData)
    {
        $this->notification = $notification;
        $this->requestData = $requestData;
    }

    protected function getUsersWithRecentNotifications($userIds)
    {
        return DB::table('notifications')
            ->where('data', 'LIKE', '%' . $this->notification['title'] . '%')
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->whereIn('notifiable_id', $userIds)
            ->pluck('notifiable_id')
            ->toArray();
    }

    protected function processUserBatch($users, $checkMembership = false, $membershipType = null)
    {
        // Get IDs of users in current batch
        $userIds = $users->pluck('id')->toArray();
        
        // Get users who already received notification in this batch
        $recentNotificationUserIds = $this->getUsersWithRecentNotifications($userIds);
        
        foreach ($users as $user) {
            // Skip if user already received notification
            if (in_array($user->id, $recentNotificationUserIds)) {
                continue;
            }

            // Check membership if required
            if ($checkMembership) {
                $userMembership = $user->membership(true);
                if ($userMembership != $membershipType) {
                    continue;
                }
            }

            $user->notify(new GeneralNotification($this->notification));
        }
    }

    public function handle()
    {
        // Process customer notifications
        if ($this->requestData['purple_customers'] || $this->requestData['gold_customers']) {
            $query = User::role('user')
                ->whereExists(function($query) {
                    $query->from('device_tokens')
                          ->whereColumn('user_id', 'users.id');
                });

            $query->chunk(500, function($customers) {
                if ($this->requestData['purple_customers'] && !$this->requestData['gold_customers']) {
                    $this->processUserBatch($customers, true, 1);
                }
                else if (!$this->requestData['purple_customers'] && $this->requestData['gold_customers']) {
                    $this->processUserBatch($customers, true, 2);
                }
                else {
                    $this->processUserBatch($customers);
                }
            });
        }

        // Process merchant notifications
        if ($this->requestData['all_merchnats']) {
            User::role('merchant')
                ->chunk(500, function($merchants) {
                    $this->processUserBatch($merchants);
                });
        }

        // Process sales person notifications
        if ($this->requestData['all_sales_persons']) {
            User::role('sales_person')
                ->chunk(500, function($sales_persons) {
                    $this->processUserBatch($sales_persons);
                });
        }
    }
}