<?php

namespace App\Http\Resources;

use App\Http\Resources\CommentResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'short_description'    => Str::limit(strip_tags($this->post_body),38, '...'),
            'description'    => $this->post_body,
            'featured_image' => (count($this->imageUrls())) ? $this->imageUrls()[0] : (!is_null($this->featured_image) ? url('/').Storage::url($this->featured_image) : NULL),
            'images'         => (count($this->imageUrls())) ? $this->imageUrls() : null,
            'featured_video' => (!is_null($this->featured_video)) ? ytId($this->featured_video) : null,
            'date'           => $this->created_at->format('d M Y'),
            'allow_comments' => $this->allow_comments,
            'category_name'  => $this->category->category_name,
            'isLiked'        => ($this->isLikedBy($request->user())) ? true : false,
            'like_count'     => $this->likers()->count(),
            'comments'       => ($this->comments()->approved()->count() > 0) ? CommentResource::collection($this->comments()->approved()->latest()->take(5)->get()) : null,
            'comment_count'  => $this->comments()->approved()->count(),
        ];
    }
}
