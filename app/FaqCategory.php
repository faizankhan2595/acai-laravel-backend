<?php

namespace App;

use App\Faq;
use Illuminate\Database\Eloquent\Model;

class FaqCategory extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at'];
    public function faqs()
    {
        return $this->hasMany('App\Faq')->active();
    }
}
