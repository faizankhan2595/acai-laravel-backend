<?php

namespace App;

use App\Notifications\CustomResetPasswordNotification;
use App\RewardVoucher;
use App\Traits\HasPoints;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Overtrue\LaravelLike\Traits\Liker;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles, SoftDeletes, HasApiTokens, Liker, HasPoints;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'mobile_number', 'mobile_verified', 'gender', 'account_status', 'dob', 'avatar', 'otp', 'is_featured', 'email_verified_at','is_project_acai','membership_type','sort_order','gold_activation_date','gold_expiring_date'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'otp',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'gold_activation_date' => 'datetime',
        'gold_expiring_date' => 'datetime',
        'dob'               => 'date',
        'data' => 'array',
    ];

    public function vouchers()
    {
        return $this->hasMany(RewardVoucher::class);
    }

    public function isVerified()
    {
        return !is_null($this->email_verified_at);
    }

    //featured merchant
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1)->where('account_status', 1)->whereNotNull('email_verified_at');
    }

    //active merchant
    public function scopeActive($query)
    {
        return $query->where('account_status', 1)->whereNotNull('email_verified_at');
    }

    //gold users
    public function scopeGoldUser($query)
    {
        return $query->where('membership_type', 2);
    }

    //comments
    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    //centers for merchant
    public function center()
    {
        return $this->hasOne('App\MerchantCenter');
    }

    //centers for merchant
    public function canBuy(RewardVoucher $voucher)
    {
        return $this->balance() >= $voucher->price;
    }

    public function orders()
    {
        return $this->hasMany('App\Order');
    }

    public function deviceTokens()
    {
        return $this->hasMany('App\DeviceToken');
    }

    public function routeNotificationForFcm()
    {
        return $this->deviceTokens()->pluck('fcm_token')->toArray();
    }

    public function scannedQrcodes()
    {
        return $this->hasMany('App\GenerateQrCode','scanned_by');
    }
    public function myGeneratedQrcodes()
    {
        return $this->hasMany('App\GenerateQrCode','generated_by');
    }

    public function scannedVouchers()
    {
        return $this->hasMany('App\Order','scanned_by');
    }

    public function specialVouchers()
    {
        return $this->belongsToMany('App\RewardVoucher')->withTimestamps()->withPivot('id','valid_from','valid_till','is_redeemed','redemption_count');
    }


    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($this->name,$token));
    }
}
