<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    protected $fillable = ['fcm_token','user_id'];
    public $timestamps = false;
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
