<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    protected $guarded = [];
    protected $dates = ['expiring_on'];
    /**
     * [user|transaction done by]
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }


}
