<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use App\Repositories\BlogCommentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class BlogCommentController extends Controller
{
    public function __construct(protected BlogCommentRepository $blogCommentRepository)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->initDataTable($request);
        }

        $statuses = BlogComment::statusOptions();

        return view('crm.blogComment.index', compact('statuses'));
    }

    public function approval(Request $request, BlogComment $blog_comment)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(BlogComment::statusOptions()))],
        ]);

        try {
            $comment = $this->blogCommentRepository->updateApprovalStatus($blog_comment, $data['status']);
            $label = BlogComment::statusOptions()[$comment->status] ?? Str::headline($comment->status);

            return response()->json([
                'status' => true,
                'message' => "Blog comment marked as {$label}.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to update blog comment approval: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->blogCommentRepository->delete($id);
            return response()->json(['status' => true, 'message' => 'Blog comment deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to delete blog comment: ' . $e->getMessage()], 500);
        }
    }

    protected function initDataTable(Request $request)
    {
        $data = $this->blogCommentRepository->initData($request);
        $statuses = BlogComment::statusOptions();

        return DataTables::of($data)
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && $request->search['value']) {
                    $search = strtolower($request->search['value']);
                    $query->where(function ($q) use ($search) {
                        $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(status) LIKE ?', ["%{$search}%"])
                            ->orWhereHas('post', function ($postQuery) use ($search) {
                                $postQuery->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"]);
                            });
                    });
                }
            })
            ->addColumn('post', fn ($row) => '<div class="w-200px">' . e(Str::limit($row->post->title ?? '-', 45)) . '</div>')
            ->addColumn('name', fn ($row) => '<div class="w-150px">' . e($row->name) . '</div>')
            ->addColumn('message', fn ($row) => '<div class="w-200px message-cell" data-full="' . e($row->message) . '" style="cursor:pointer;">' . e(Str::limit($row->message, 60)) . '</div>')
            ->addColumn('status', fn ($row) => $this->renderStatusBadge($row, $statuses))
            ->addColumn('approval', fn ($row) => $this->renderApprovalControls($row, $statuses))
            ->addColumn('approved_at', fn ($row) => $row->approved_at ? formatDateTimeIST($row->approved_at) : '-')
            ->addColumn('created_at', fn ($row) => formatDateTimeIST($row->created_at))
            ->addColumn('action', fn ($row) => view('crm.blogComment.action', compact('row'))->render())
            ->rawColumns(['post', 'name', 'message', 'status', 'approval', 'action'])
            ->make(true);
    }

    protected function renderStatusBadge(BlogComment $comment, array $statuses): string
    {
        $badgeClass = match ($comment->status) {
            BlogComment::STATUS_APPROVED => 'success',
            BlogComment::STATUS_REJECTED => 'danger',
            default => 'warning',
        };
        $label = $statuses[$comment->status] ?? Str::headline($comment->status);

        return '<span class="badge bg-' . $badgeClass . '">' . e($label) . '</span>';
    }

    protected function renderApprovalControls(BlogComment $comment, array $statuses): string
    {
        $buttons = [
            BlogComment::STATUS_APPROVED => ['class' => 'success', 'icon' => 'ri-check-line'],
            BlogComment::STATUS_PENDING => ['class' => 'warning', 'icon' => 'ri-time-line'],
            BlogComment::STATUS_REJECTED => ['class' => 'danger', 'icon' => 'ri-close-line'],
        ];

        $html = '<div class="btn-group btn-group-sm comment-approval-group" role="group">';

        foreach ($buttons as $status => $meta) {
            $isActive = $comment->status === $status;
            $label = $statuses[$status] ?? Str::headline($status);
            $class = $isActive ? 'btn-' . $meta['class'] : 'btn-outline-' . $meta['class'];
            $disabled = $isActive ? ' disabled' : '';

            $html .= '<button type="button" class="btn ' . $class . ' comment-approval-btn" data-id="' . $comment->id . '" data-status="' . e($status) . '" title="' . e($label) . '"' . $disabled . '>';
            $html .= '<i class="' . $meta['icon'] . '"></i>';
            $html .= '</button>';
        }

        return $html . '</div>';
    }
}
