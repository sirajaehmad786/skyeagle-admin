<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Repositories\BlogCommentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

        return view('crm.blogComment.index');
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
        $data = $this->blogCommentRepository->initData();
        $activeStatus = config('constant.status.0', 'Active');
        $inactiveStatus = config('constant.status.1', 'Inactive');

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
            ->addColumn('status', function ($row) use ($activeStatus, $inactiveStatus) {
                $isActive = $row->status === $activeStatus;
                $badge = $isActive ? 'success' : 'secondary';
                $status = $isActive ? $activeStatus : $inactiveStatus;

                return '<span class="badge bg-' . $badge . '">' . e($status) . '</span>';
            })
            ->addColumn('created_at', fn ($row) => formatDateTimeIST($row->created_at))
            ->addColumn('action', fn ($row) => view('crm.blogComment.action', compact('row'))->render())
            ->rawColumns(['post', 'name', 'message', 'status', 'action'])
            ->make(true);
    }
}
