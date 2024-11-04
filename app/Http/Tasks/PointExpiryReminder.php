<?php
namespace App\Http\Tasks;

use App\Notifications\GeneralNotification;
use App\PointTransaction;
use App\Setting;
use App\User;
use Illuminate\Support\Carbon;

class PointExpiryReminder
{
    public function __invoke()
    {
        $date = Carbon::today()->addDays(6)->format('Y-m-d');
        $tommorow = Carbon::today()->addDays(1)->format('Y-m-d');
        $transactions = PointTransaction::whereDate('expiring_on', '=', $date)->get();
        if ($transactions->count() > 0) {
            foreach ($transactions as $key => $transaction) {
                $expdate = $transaction->expiring_on->format('d M Y');
                if ($transaction->points_available > 0) {
                    $transaction->user->notify(new GeneralNotification([
                        'title' => 'Points Expiring',
                        'message' => 'Your loyalty points are expiring. Redeem a reward soon!',
                        'image' => null
                    ]));
                }
            }
        }

    }
}
