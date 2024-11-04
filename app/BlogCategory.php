<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $guarded = [];

    public function posts()
    {
        return $this->hasMany('App\Blog');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
