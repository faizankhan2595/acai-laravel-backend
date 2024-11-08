<?php

namespace App\Http\Controllers\Admin;

use App\Blog;
use App\BlogCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\BlogStoreRequest;
use App\Image;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $blogs = Blog::with('category')->get();
    //     return view('admin.blog.list', compact('blogs'));
    // }
    public function index(Request $request)
    {
        if($request->ajax())
        {
            $data = Blog::with('category')->latest()->get();
            return DataTables::of($data)
                    ->addColumn('action', function($data){
                        $button = '<a href='.route("blog.edit",$data->id).' class="on-default text-success btn btn-xs btn-default"><i class="fa fa-pencil"></i> Edit</a>';
                        $button .= '&nbsp;&nbsp;<a href='.route('blog.likes',$data->id).' class="on-default text-info btn btn-xs btn-default"><i class="fa fa-thumbs-up"></i> Likes</a>';
                        $button .= '&nbsp;&nbsp;<a href='.route('blog.comments',$data->id).' class="on-default text-warning btn btn-xs btn-default"><i class="fa fa-comments"></i> Comments</a>';
                        $button .= '&nbsp;&nbsp;<form onSubmit="return confirm(\'Are you sure you want to delete this?\')" action='.route("blog.destroy",$data->id).'  method="POST" class="inline-el" data-swal="1">
                        '.method_field('DELETE').'
                        '.csrf_field().'
                        <button class="on-default text-danger btn btn-xs btn-default delete_form" type="submit"><i class="fa fa-trash"></i> Delete</button>
                    </form>';

                        return $button;
                    })
                    ->editColumn('created_at', function ($data) {
                        return [
                            'display' => (!is_null($data->created_at)) ? $data->created_at->format('d M Y') : 'N/A',
                            'timestamp' => (!is_null($data->created_at)) ?  $data->created_at->timestamp : (10*9000*100000)
                        ];
                    })
                    ->addColumn('blog_views', function($data){
                        $viewed = DB::table('blog_views')->select(DB::raw('count(*) as view_count'))
                            ->where('blog_id', $data->id)
                            ->first();
                        return $viewed->view_count;
                    })
                    ->rawColumns(['action','blog_views'])
                    ->make(true);
        }
        return view('admin.blog.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = BlogCategory::all();
        return view('admin.blog.add', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BlogStoreRequest $request)
    {
        $validated                   = $request->validated();
        $validated['featured_image'] = null;
        // if ($request->hasfile('featured_image')) {
        //     $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        // }

        $post                 = new Blog();
        $post->title          = $validated['title'];
        $post->slug           = Str::slug($validated['title']);
        $post->category_id    = $validated['category_id'];
        $post->user_id        = Auth::guard('admin')->user()->id;
        $post->post_body      = $validated['post_body'];
        // $post->featured_image = $validated['featured_image'];
        $post->featured_video = $validated['featured_video'];
        $post->tags           = $validated['tags'];
        $post->published_on   = Carbon::now();
        $post->is_featured    = (isset($validated['is_featured'])) ? 1 : 0;
        $post->allow_comments = (isset($validated['allow_comments'])) ? 1 : 0;
        $post->status         = $validated['status'];

        $post->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $key => $file) {
                $image = new Image;
                $image->path = $file->store('blog', 'public');
                $image->blog_id = $post->id;
                $imgid = $image->save();
            }
        }

        if ($post->is_featured == 1) {
            $blogstoupdate = Blog::where('is_featured',1)->where('id','!=',$post->id)->get();
            foreach ($blogstoupdate as $key => $blogpost) {
                $blogpost->is_featured = 0;
                $blogpost->save();
            }
        }

        return redirect(route('blog.index'))
            ->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function likes(Blog $blog)
    {
        $likes = $blog->likers()->get();
        return view('admin.blog.likes', compact('likes', 'blog'));
    }
    public function deletelike(Request $request, Blog $blog)
    {
        $blog->likers()->wherePivot('user_id', '=', $user->id)->detach();
        return redirect()->back()
            ->with('success', 'Post updated successfully.');
    }

    /**
     * [post comments list]
     * @return [view] [comment list]
     */
    public function comments($blog)
    {
        return view('admin.comment.list', compact('blog'));
    }

    /**
     * [trashed posts]
     * @return [view] [trash list]
     */
    public function trash()
    {
        $trashed = Blog::onlyTrashed()->get();
        return view('admin.blog.trash', compact('trashed'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Blog $blog)
    {
        $categories = BlogCategory::all();
        return view('admin.blog.edit', compact('blog', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BlogStoreRequest $request, Blog $blog)
    {
        $validated = $request->validated();
        // if ($request->hasfile('featured_image')) {
        //     Storage::disk('public')->delete($blog->featured_image);
        //     $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        // } else {
        //     $validated['featured_image'] = $validated['old_image'];
        // }

        $blog->title          = $validated['title'];
        $blog->slug           = Str::slug($validated['title']);
        $blog->category_id    = $validated['category_id'];
        $blog->user_id        = Auth::guard('admin')->user()->id;
        $blog->post_body      = $validated['post_body'];
        // $blog->featured_image = $validated['featured_image'];
        $blog->featured_video = $validated['featured_video'];
        $blog->tags           = $validated['tags'];
        // $blog->published_on   = Carbon::now();
        $blog->is_featured    = (isset($validated['is_featured'])) ? 1 : 0;
        $blog->allow_comments = (isset($validated['allow_comments'])) ? 1 : 0;
        $blog->status         = $validated['status'];

        $blog->update();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $key => $file) {
                $image = new Image;
                $image->path = $file->store('blog', 'public');
                $image->blog_id = $blog->id;
                $imgid = $image->save();
            }
        }

        if ($blog->is_featured == 1) {
            $blogstoupdate = Blog::where('is_featured',1)->where('id','!=',$blog->id)->get();
            foreach ($blogstoupdate as $key => $blogpost) {
                $blogpost->is_featured = 0;
                $blogpost->save();
            }
        }

        return redirect(route('blog.index'))
            ->with('success', 'Post updated successfully.');
    }

    public function restore($id)
    {
        $blog = Blog::onlyTrashed()->findOrfail($id);
        $blog->restore();
        return redirect(route('blog.trash'))
            ->with('success', 'Post restored successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blog $blog)
    {
        $blog->delete();
        return redirect(route('blog.index'))
            ->with('success', 'Post deleted successfully.');
    }

    public function deleteimage(Image $image)
    {
        Storage::delete($image->path);
        $image->delete();
        return redirect()->back()
            ->with('success', 'Post deleted successfully.');
    }

    public function forcedelete($id)
    {
        $blog = Blog::withTrashed()->findOrFail($id);
        DB::table('images')->where('blog_id', $blog->id)->delete();
        DB::table('blog_views')->where('blog_id', $blog->id)->delete();
        DB::table('likes')->where('likeable_id', $blog->id)->delete();
        DB::table('comments')->where('blog_id', $blog->id)->delete();
        
        Storage::delete($blog->featured_image);
        $blog->forceDelete();
        return redirect(route('blog.trash'))
            ->with('success', 'Post deleted successfully.');
    }
}
