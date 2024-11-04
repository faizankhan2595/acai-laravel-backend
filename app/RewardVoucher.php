<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class RewardVoucher extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id', 'title', 'price', 'image', 'expiring_on', 'notes', 'terms', 'is_featured', 'status', 'voucher_type','valid_for','is_special_voucher','discount_title','discount_subtitle'
    ];

    protected $validFor = [
        'All Users',
        'Purple Users',
        'Gold Users',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'expiring_on' => 'datetime',
        'price'       => 'integer',
        'terms'       => 'object',
    ];
    public function merchant()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    //active merchant
    public function scopeActive($query)
    {
        return $query->where('status', 1)->whereDate('expiring_on', '>=', Carbon::now());
    }

    public function isActive()
    {
        return ($this->status == 1 && $this->expiring_on->format('Y-m-d') >= Carbon::now()->format('Y-m-d'));
    }

    public function orders()
    {
        return $this->hasMany('App\Order');
    }

    public function getValidForAttribute($value)
    {
        return Arr::get($this->validFor, $value);
    }
    public function specialVoucherUsers()
    {
        return $this->belongsToMany('App\User')->withTimestamps()->withPivot('id','valid_from','valid_till','is_redeemed','redemption_count');
    }
}
