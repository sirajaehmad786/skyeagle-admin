<?php

namespace App\Repositories;

use App\Models\BlogComment;

class BlogCommentRepository extends BaseRepository
{
    public function __construct(BlogComment $blogComment)
    {
        parent::__construct($blogComment);
    }

    public function initData($request = null)
    {
        $query = BlogComment::query()
            ->with(['post', 'parent'])
            ->latest();

        if ($request) {
            $this->applyFilters($query, $request);
        }

        return $query;
    }

    public function updateApprovalStatus(BlogComment $comment, string $status): BlogComment
    {
        $comment->status = $status;
        $comment->approved_at = $status === BlogComment::STATUS_APPROVED ? now() : null;
        $comment->save();

        return $comment->refresh();
    }

    public function delete($id)
    {
        $comment = BlogComment::withTrashed()->findOrFail($id);
        return $comment->forceDelete();
    }

    protected function applyFilters($query, $request): void
    {
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('created_from')) {
            $query->where('created_at', '>=', istDateRangeToUtc($request->created_from));
        }

        if ($request->filled('created_to')) {
            $query->where('created_at', '<=', istDateRangeToUtc($request->created_to, true));
        }
    }
}
