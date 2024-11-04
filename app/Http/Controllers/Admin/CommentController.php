<?php

namespace App\Http\Controllers\Admin;

use App\Blog;
use App\Comment;
use App\Exports\CommentExport;
use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CommentController extends Controller
{
    // public function index()
    // {
    //     $comments = Comment::with('blog','user')->get();
    //     return view('admin.comment.list', compact('comments'));
    // }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if (is_null($request->blog)) {
                $data = Comment::with('blog', 'user')->latest()->get();
            } else {
                $data = Comment::where('blog_id', $request->blog)->with('blog', 'user')->get();
            }
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    if ($data->status) {
                        $button = '<form action=' . route("comment.deny", $data->id) . '  method="POST" class="inline-el" data-swal="1">
                            ' . method_field('PATCH') . '
                            ' . csrf_field() . '
                            <button class="on-default text-warning btn btn-xs btn-default action_form" type="button">Denied</button>
                        </form>';
                    } else {
                        $button = '<form action=' . route("comment.approve", $data->id) . '  method="POST" class="inline-el">
                            ' . method_field('PATCH') . '
                            ' . csrf_field() . '
                            <button class="on-default text-success btn btn-xs btn-default action_form" type="button">Approve</button>
                        </form>';
                    }
                    $button .= '&nbsp;&nbsp;<form onSubmit="return confirm(\'Are you sure you want to delete this?\')" action=' . route("comment.destroy", $data->id) . '  method="POST" class="inline-el" data-swal="1">
                        ' . method_field('DELETE') . '
                        ' . csrf_field() . '
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
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.comment.list');
    }

    public function approve(Comment $comment)
    {
        $comment->status = 1;
        $comment->save();
        return redirect(route('comment.index'))
            ->with('success', 'Comment approved successfully.');
    }
    public function deny(Comment $comment)
    {
        $comment->status = 0;
        $comment->save();
        return redirect(route('comment.index'))
            ->with('success', 'Comment denied successfully.');
    }

    public function denyAjax(Request $request)
    {
        $comment = Comment::findOrFail($request->comment_id);
        $comment->status = 0;
        $comment->save();
        return response()->json(['status'=>true,'message' => 'Status updated successfully!'],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return redirect()->back()
            ->with('success', 'Comment deleted successfully.');
    }

    public function export()
    {
        return Excel::download(new CommentExport, 'comments.xlsx');
    }
}
