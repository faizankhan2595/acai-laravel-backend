<?php

namespace App\Exports;

use App\Comment;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CommentExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return ["Comment By", "Post Title", "Comment", "Status", "Commented On"];
    }

    public function array(): array
    {
        $comments = Comment::all();
        $data = [];
        foreach ($comments as $key => $comment) {
            $data[] = [
            'commented_by' => $comment->user->name,
            'post' => $comment->blog->title,
            'comment' => $comment->comment_body,
            'status' => $comment->status === 1 ? 'Active' : 'Inactive',
            'created_at' => (!is_null($comment->created_at)) ? $comment->created_at->format('d M Y') : 'NA',
        ];
        }
        return $data;
    }
}
