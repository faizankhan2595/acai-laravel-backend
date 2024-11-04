<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Overtrue\LaravelLike\Traits\Likeable;

class Blog extends Model
{
    use SoftDeletes,Likeable;

    protected $guarded = [];

    protected $casts = [
        'published_on' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo('App\BlogCategory');
    }

    /**
     * Scope a query to only include active users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1)->where('status', 1)->first();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function approvedComments()
    {
        return $this->hasMany(Comment::class)->approved();
    }
    /**
     * Get the images for the Blog.
     */
    public function images()
    {
        return $this->hasMany('App\Image');
    }

    public function imageUrls()
    {
        $urls = [];
        foreach ($this->images as $image) {
            $url = url('/') . Storage::url($image->path);
            array_push($urls,$url);
        }
        return $urls;
    }
}
