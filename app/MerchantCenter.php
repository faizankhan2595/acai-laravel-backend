<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MerchantCenter extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at'];

    public function merchant()
    {
        return $this->belongsTo('App\User');
    }
}
