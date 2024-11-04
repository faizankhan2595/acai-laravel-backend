<?php
namespace App\Http\Tasks;

use App\Notifications\GeneralNotification;
use App\Setting;
use App\User;
use Illuminate\Support\Carbon;

class BirthDayReward
{
    public function __invoke()
    {
        $date = Carbon::today();
        $users = User::whereMonth('dob', '=', $date->month)->whereDay('dob', '=', $date->day)->get();
        $valinpoints = Setting::where('key', 'birthday_reward')->first();
        $valinpoints = (!is_null($valinpoints)) ? (int) ($valinpoints->value) : 0;
        if ($valinpoints > 0) {
            foreach ($users as $key => $user) {
                $user->creditPoints([
                    'transaction_value' => (int) $valinpoints,
                    'data'              => json_encode(['message' => "Birthday Reward Added", 'sub_heading' => 'By Project Acai Admin']),
                ]);
                $user->notify(new GeneralNotification([
                    'title' => 'Happy Birthday!',
                    'message' => 'Here are '.$valinpoints.' points as a gift from us.',
                    'image' => null
                ]));
            }
        }

    }
}
