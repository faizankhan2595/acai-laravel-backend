<?php
namespace App\Http\Tasks;

use App\Notifications\GeneralNotification;
use App\Setting;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MembershipReminder
{
    public function __invoke()
    {
        $goldcharge = Setting::where('key', 'gold_membership_renew_charge')->first();
        $gold_membership_charge = (!is_null($goldcharge)) ? $goldcharge->value : 0;
        // $gold_membership_charge = 250;
        $today = Carbon::today()->format('Y-m-d');
        $thirty_days = Carbon::today()->addDays(30)->format('Y-m-d');

        //send expiry reminder notification before 30 days
        $users = User::WhereDate('gold_expiring_date', '=', $thirty_days)->get();
        if ($users->count() > 0) {
            foreach ($users as $key => $user) {
                $expdate = $user->gold_expiring_date->format('d M Y');
                $user->notify(new GeneralNotification([
                    'title' => 'Your gold membership will expire this month',
                    'message' => 'Maintain upto '.$gold_membership_charge.' points to enjoy gold benefits for next year.',
                    'image' => null
                ]));

                $data = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'expdate' => $expdate,
                    'gold_membership_charge' => $gold_membership_charge
                ];

                Mail::send('mails.gold_membership_expiry_reminder', $data, function ($message) use ($data) {
                    $message->to($data['email'], $data['name'])
                        ->subject('Gold Membership Expiry Reminder');
                });
            }
        }

        //renew if has enough points or send expiry notification
        $renewforusers = User::WhereDate('gold_expiring_date', '=', $today)->get();
        if ($renewforusers->count() > 0) {
            $nextdate = Carbon::today()->addDays(365)->endOfDay();
            foreach ($renewforusers as $renewuser) {
                if ($renewuser->balance() >= $gold_membership_charge) {
                    $renewuser->gold_renew_date = Carbon::today();
                    $renewuser->gold_expiring_date = $nextdate;
                    $renewuser->membership_type = 2;
                    $renewuser->save();

                    DB::table('point_transactions')->insert([
                        'user_id' => $renewuser->id,
                        'transaction_type' => 2,
                        'transaction_value' => 0,
                        'points_redeemed' => 0,
                        'points_available' => 0,
                        'expiring_on' => $nextdate,
                        'data' => '{"message":"Gold Membership Renewed","sub_heading":"By System"}',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);

                } else{
                    $renewuser->membership_type = 1;
                    $renewuser->gold_expiring_date = NULL;
                    $renewuser->save();
                    $renewuser->notify(new GeneralNotification([
                        'title' => 'Membership Expired',
                        'message' => 'Gold Membership has expired.',
                        'image' => null
                    ]));
                }
            }
        }
    }
}
