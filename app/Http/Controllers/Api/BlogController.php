<?php

namespace App\Http\Controllers\Api;

use App\Blog;
use App\BlogCategory;
use App\Comment;
use App\Http\Controllers\Controller;
use App\Http\Resources\BlogCategoryCollection;
use App\Http\Resources\BlogCollection;
use App\Http\Resources\BlogResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\MerchantCollection;
use App\Setting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function featured()
    {
        $featured = Blog::with('category')->featured();
        if ($featured->exists()) {
            return new BlogResource($featured);
        }else{
            $featured = Blog::with('category')->latest()->first();
            return new BlogResource($featured);
        }
    }

    public function latest()
    {
        $latest = Blog::active()->take(5)->latest()->get();
        return new BlogCollection($latest);
    }

    function list(Request $request) {
        $query = Blog::query();
        if ($request->has('category') && $request->category != 0) {
            $query = $query->where('category_id', $request->category);
        }
        if ($request->has('search') && $request->search != "") {
            $search = $request->search;
            $query  = $query->where(function ($q) use ($search) {
                $q->where('tags', 'like', '%' . $search . '%')
                    ->orWhere('title', 'like', '%' . $search . '%');
            });
        }
        if ($request->has('date_filter') && $request->date_filter != 0 && $request->fromDate == '' && $request->toDate == '') {
            switch ($request->date_filter) {
                case 1:
                    $query = $query->whereDate('created_at', Carbon::today());
                    break;
                case 2:
                    $query = $query->where('created_at', '>', Carbon::now()->startOfWeek())
                        ->where('created_at', '<', Carbon::now()->endOfWeek());
                    break;
                case 3:
                    $query = $query->whereMonth('created_at', Carbon::now()->month);
                    break;
                case 4:
                    $query = $query->whereYear('created_at', Carbon::now()->year);
                    break;
            }
        }

        if ($request->fromDate != '' && $request->toDate != '') {
            $from = Carbon::createFromFormat('d/m/Y', $request->fromDate)->format('Y-m-d 00:00:00');
            $to   = Carbon::createFromFormat('d/m/Y', $request->toDate)->format('Y-m-d 23:59:59');
            if ($from > $to) {
                $oldfrom = $from;
                $from    = $to;
                $to      = $oldfrom;
            }
            $query = $query->whereBetween('created_at', [$from, $to]);
        }

        $list = $query->active()->latest()->paginate(10);
        //if filter button clicked always return status 200 so if no content will be returned we can show no records found message
        if ($request->is_filter) {
            return new BlogCollection($list);
        }

        //if load more then we have to retun status 204 in case of no content
        if (!$list->isEmpty()) {
            return new BlogCollection($list);
        } else {
            return response()->noContent();
        }
    }

    public function categories()
    {
        $categories = BlogCategory::active()->orderBy('category_name', 'ASC')->get();
        return new BlogCategoryCollection($categories);
    }
    public function featuredMerchants()
    {
        $featured = User::role('merchant')->featured()->take(5)->get();
        return new MerchantCollection($featured);
    }

    public function blogDetail(Request $request)
    {
        $blog = Blog::findOrFail($request->id);

        $viewed = DB::table('blog_views')->select(DB::raw('count(*) as view_count, user_id,ip,blog_id'))
            ->where('blog_id', $blog->id)
            ->where('user_id', $request->user()->id)
            ->whereDate('created_at', '=', date('Y-m-d'))
            ->first();
        if ($viewed->view_count === 0) {
            DB::table('blog_views')->insertGetId([
                'blog_id' => $blog->id,
                'user_id' => $request->user()->id,
                'ip' => $request->ip(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        return new BlogResource($blog);
    }
    public function homepage()
    {
        return response()->json([
            'new_posts'         => $this->latest(),
            'featured_post'     => $this->featured(),
            'featured_rewards'  => $this->featuredMerchants(),
            'acai_menu'         => $this->getmenu(),
            'acai_reward_guide' => $this->getrewardGuide(),
            'locations'         => $this->getLocations(),
        ], 200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getmenu()
    {
        $menu = Setting::where('key', 'acai_menu')->first();
        if (!$menu) {
            return null;
        } else {
            return url('/') . Storage::url($menu->value);
        }
    }

    public function getrewardGuide()
    {
        $guide = Setting::where('key', 'acai_reward_guide_pdf')->first();
        if (!$guide) {
            return null;
        } else {
            return url('/') . Storage::url($guide->value);
        }
    }

    public function likeUnlike(Request $request)
    {
        try {
            $blog = Blog::findOrFail($request->id);
            $user = $request->user();

            if ($user->hasLiked($blog)) {
                $blog->likers()->wherePivot('user_id', '=', $user->id)->detach();
            } else {
                $user->like($blog);
            }
            return new BlogResource($blog);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'count'  => $e->message,
            ], 400);
        }
    }

    public function getLocations()
    {
        return DB::table('locations')->select('id', 'location_title', 'location_link', 'location_address')->get();
    }
    public function saveComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_body' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }
        $blog                  = Blog::findOrFail($request->id);
        $user                  = $request->user();
        $comment               = new Comment();
        $comment->comment_body = $request->comment_body;
        $comment->status       = 1;
        $comment->user_id      = $user->id;
        $comment->blog_id      = $blog->id;
        $comment->save();
        return new BlogResource($blog);
    }

    public function comments(Request $request)
    {
        $blog     = Blog::findOrFail($request->id);
        $comments = $blog->comments()->approved()->paginate(15);
        return CommentResource::collection($comments);
    }
}
