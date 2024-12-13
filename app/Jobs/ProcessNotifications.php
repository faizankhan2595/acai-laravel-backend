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

    protected function hasRecentNotification($userId)
    {
        return DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->where('data', 'LIKE', '%' . $this->notification['title'] . '%')
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->exists();
    }

    public function handle()
    {
        // Process customer notifications
        if ($this->requestData['purple_customers'] || $this->requestData['gold_customers']) {
            $query = User::role('user')->whereExists(function($query) {
                $query->from('device_tokens')
                      ->whereColumn('user_id', 'users.id');
            });

            $query->chunk(300, function($customers) {
                foreach ($customers as $customer) {
                    // Skip if user received same notification in past 24 hours
                    if ($this->hasRecentNotification($customer->id)) {
                        continue;
                    }

                    if ($this->requestData['purple_customers'] && !$this->requestData['gold_customers']) {
                        if ($customer->membership(true) == 1) {
                            $customer->notify(new GeneralNotification($this->notification));
                        }
                    }
                    else if (!$this->requestData['purple_customers'] && $this->requestData['gold_customers']) {
                        if ($customer->membership(true) == 2) {
                            $customer->notify(new GeneralNotification($this->notification));
                        }
                    }
                    else {
                        $customer->notify(new GeneralNotification($this->notification));
                    }
                }
            });
        }

        // Process merchant notifications
        if ($this->requestData['all_merchnats']) {
            User::role('merchant')
                ->chunk(300, function($merchants) {
                    foreach ($merchants as $merchant) {
                        // Skip if merchant received same notification in past 24 hours
                        if ($this->hasRecentNotification($merchant->id)) {
                            continue;
                        }
                        $merchant->notify(new GeneralNotification($this->notification));
                    }
                });
        }

        // Process sales person notifications
        if ($this->requestData['all_sales_persons']) {
            User::role('sales_person')
                ->chunk(300, function($sales_persons) {
                    foreach ($sales_persons as $sales_person) {
                        // Skip if sales person received same notification in past 24 hours
                        if ($this->hasRecentNotification($sales_person->id)) {
                            continue;
                        }
                        $sales_person->notify(new GeneralNotification($this->notification));
                    }
                });
        }
    }
}