<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $dates = [
        'redeemed_on'
    ];
    protected $casts = [
        'amount' => 'integer'
    ];
    public function voucher()
    {
        return $this->belongsTo('App\RewardVoucher','reward_voucher_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\User')->withTrashed();
    }

    public function scannedBy()
    {
        return $this->belongsTo('App\User', 'scanned_by', 'id')->withTrashed();
    }
}
