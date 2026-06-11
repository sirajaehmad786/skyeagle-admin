<?php

namespace App\Repositories;

use App\Models\BlogComment;

class BlogCommentRepository extends BaseRepository
{
    public function __construct(BlogComment $blogComment)
    {
        parent::__construct($blogComment);
    }

    public function initData()
    {
        return BlogComment::query()
            ->with(['post', 'parent'])
            ->latest();
    }

    public function delete($id)
    {
        $comment = BlogComment::withTrashed()->findOrFail($id);
        return $comment->forceDelete();
    }
}
