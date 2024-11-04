<?php
namespace App\Traits;

use App\PointTransaction;
use App\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

trait HasPoints
{
    public function creditPoints($params = [])
    {
        $defdays                    = Setting::where('key', 'default_exipry')->first();
        $defdays                    = (!is_null($defdays)) ? $defdays->value : config('admin.default_expiry');
        $params['transaction_type'] = 1;
        $params['points_available'] = $params['transaction_value'];
        $params['expiring_on']      = (isset($params['expiring_on'])) ? $params['expiring_on'] : Carbon::now()->addDays($defdays);
        return $this->points()->create($params);
    }

    public function debitPoints($params = [])
    {
        $rows = PointTransaction::selectRaw('id, points_available')->where([
            ['transaction_type', '=', 1],
            ['user_id', '=', $this->id],
            ['points_available', '>', 0],
        ])->whereDate('expiring_on', '>=', Carbon::now()->endOfDay())->orderBy('expiring_on', 'ASC')->get();

        $amount_to_redeem = (int) $params['transaction_value'];

        // Map storing amount_redeemed against id
        $amount_redeemed_map = array();

        foreach ($rows as $row) {
            // Calculate the amount that can be used against a specific credit
            // It will be the minimum of available credit and amount left to redeem
            $amount_redeemed = min($row['points_available'], $amount_to_redeem);

            // Populate the map
            $amount_redeemed_map[$row['id']] = $amount_redeemed;

            // Adjust the amount_to_redeem
            $amount_to_redeem -= $amount_redeemed;

            // If no more amount_to_redeem, we can finish the loop
            if ($amount_to_redeem == 0) {
                break;
            } elseif ($amount_to_redeem < 0) {

                // This should never happen, still if it happens, throw error
                throw new Exception("Something wrong!");
                exit();
            }

            // if we are here, that means some more amount left to redeem
        }

        foreach ($amount_redeemed_map as $tid => $tr_amt) {
            $transaction                   = PointTransaction::find($tid);
            $transaction->points_redeemed  = $transaction->points_redeemed + $tr_amt;
            $transaction->points_available = $transaction->transaction_value - $transaction->points_redeemed;
            $transaction->save();
        }
        $params['transaction_type'] = 2;
        return $this->points()->create($params);
    }

    public function balanceOld()
    {
        // ->whereNotNull('source_id')
        $credit  = $this->points()->where('transaction_type', 1)->whereDate('expiring_on', '>=', Carbon::now()->format('Y-m-d'))->get()->sum('transaction_value');
        $debit   = $this->points()->where('transaction_type', 2)->get()->sum('transaction_value');
        $balance = $credit - $debit;
        return $balance < 0 ? 0 : $balance;
    }

    public function balance()
    {
        $balance = $this->points()->where('transaction_type', 1)->whereDate('expiring_on', '>=', Carbon::now()->endOfDay())->get()->sum('points_available');
        return $balance;
    }

    /**
     * [pointsEarned total points earned till now including expired points]
     * @return [type] [description]
     */
    public function pointsEarned()
    {
        $balance = $this->points()->where('transaction_type', 1)->get()->sum('points_available');
        return $balance;
    }

    public function transactions($limit = 10)
    {
        return $this->points()->latest()->paginate($limit);
    }

    public function lastCredittransaction()
    {
        return $this->points()->latest()->first();
    }

    public function nextExpiring()
    {
        $data = PointTransaction::selectRaw('SUM(points_available) as points,date(expiring_on) as expiring_on')->where('transaction_type', 1)->where('points_available','!=', 0)->whereDate('expiring_on', '>=', Carbon::now()->format('Y-m-d'))->where('user_id', $this->id)->groupBy(DB::raw('date(expiring_on)'))->orderBy('expiring_on', 'ASC')->first();

        if (!is_null($data)) {
            $data->points = ($data->points > $this->balance()) ? $this->balance() : $data->points;
        }
        return $data;
    }

    public function lastExpired()
    {
        $data = PointTransaction::selectRaw('DATE(expiring_on) AS last_expired')->where('transaction_type', 1)->where('user_id', $this->id)->whereNotNull('expiring_on')->orderBy('last_expired', 'ASC')->first();

        if (!is_null($data)) {
            return date('d M Y', strtotime($data->last_expired));
        }
        return null;
    }

    /**
     * [Deprecated]
     * @param  boolean $wantNum [description]
     * @return [type]           [description]
     */
    public function membershipOld($wantNum = false)
    {
        $maxpoints = Setting::where('key', 'gold_membership_points')->first();
        $maxpoints = (!is_null($maxpoints)) ? $maxpoints->value : config('admin.gold_membership_points');
        $points    = $this->points()->where('transaction_type', 1)->get()->sum('transaction_value');
        if ($points >= $maxpoints) {
            return (!$wantNum) ? 'Gold Member' : 2;
        }
        return (!$wantNum) ? 'Purple Member' : 1;
    }
    public function membership($wantNum=false)
    {
        if ($wantNum) {
            return $this->membership_type;
        }
        return ($this->membership_type == 1) ? 'Purple Member' : 'Gold Member';
    }
    public function isEligibleForGold()
    {
        $maxpoints = Setting::where('key', 'gold_membership_points')->first();
        $maxpointsval = (!is_null($maxpoints)) ? $maxpoints->value : config('admin.gold_membership_points');
        if (is_null($maxpoints)) {
            return false;
        } else {
            return ($this->balance() >= $maxpointsval);
        }
    }
    public function totalPointsEarnedTillNow()
    {
        return $this->points()->where('transaction_type', 1)->get()->sum('transaction_value');
    }
    /**
     * [points relationship]
     * @return [type] [hasMany relationship]
     */
    public function points()
    {
        return $this->hasMany('App\PointTransaction');
    }
}
