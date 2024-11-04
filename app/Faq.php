<?php

namespace App;

use App\FaqCategory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at'];

    public function faqcategory()
    {
        return $this->belongsTo('App\FaqCategory','faq_category_id');
    }

    function scopeActive($query) {
        return $query->where('status',1);
    }
}
