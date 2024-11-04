<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\UserExportMail;
use Illuminate\Support\Facades\Mail;
use App\RewardVoucher;
use App\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SpecialVoucherAllUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $special_voucher;
    public $user_ids; 

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($special_voucher, $user_ids = null)
    {
        $this->special_voucher = $special_voucher;
        $this->user_ids = $user_ids;

        $test_user = User::where('email', 'faizankhan2595@gmail.com')->first();

        $test_user->notify(new GeneralNotification([
            "title"   => "Job Started",
            "message" => "Job Started",
        ]));
    }

    /** 
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Log::channel('single')->debug('SpecialVoucherAllUsers Starting');

        $special_voucher = $this->special_voucher;
        $batchSize = 50; 

        $test_user = User::where('email', 'faizankhan2595@gmail.com')->first();

        $test_user->notify(new GeneralNotification([
            "title"   => "Job Started 2",
            "message" => "Job Started 2",
        ]));

        $usersAlreadyHasVoucher = DB::table('reward_voucher_user')
            ->where('reward_voucher_id', $special_voucher->id)
            ->get();

        User::where('id', '>', 0)
            ->when($this->user_ids, function ($query) {
                return $query->whereIn('id', $this->user_ids);
            })
            ->whereNotIn('id', $usersAlreadyHasVoucher->pluck('user_id')->toArray())
        ->chunkById($batchSize, function ($users) use ($special_voucher) {
            $datatoinsert = [];

            foreach ($users as $user) {
                Log::debug('SpecialVoucherAllUsers User ID: ' . $user->id);

                $datatoinsert[$user->id] = [
                    'valid_from'  => $special_voucher->created_at->startOfDay(),
                    'valid_till'  => $special_voucher->expiring_on->endOfDay(),
                    'redeemed_on' => null,
                ];
                
                // if($user->email == "faizankhan2595@gmail.com") {
                $user->notify(new GeneralNotification([
                    "title"   => "Special Reward",
                    "message" => "A reward just for you has been added to your account. View it under “Special Rewards”",
                ]));
                // }
            }

            $special_voucher->specialVoucherUsers()->sync($datatoinsert, false);
        });

        $test_user->notify(new GeneralNotification([
            "title"   => "Job Ended 2",
            "message" => "Job Ended 2", 
        ]));
    }
}
