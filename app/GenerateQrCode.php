<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GenerateQrCode extends Model
{
    protected $dates = [
        'scanned_on'
    ];
    protected $casts = [
        'amount' => 'float'
    ];

    public function scannedBy()
    {
        return $this->belongsTo('App\User','scanned_by','id')->withTrashed();
    }

    public function generatedBy()
    {
        return $this->belongsTo('App\User','generated_by','id')->withTrashed();
    }
}
