<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlogPostRequest;
use App\Models\Category;
use App\Repositories\BlogPostRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class BlogPostController extends Controller
{
    public function __construct(protected BlogPostRepository $blogPostRepository)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->initDataTable($request);
        }

        $tags = $this->blogPostRepository->activeBlogTags();
        $statuses = config('constant.status', []);

        return view('crm.blogPost.index', compact('tags', 'statuses'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('crm.blogPost.create', compact('categories'));
    }

    public function store(BlogPostRequest $request)
    {
        try {
            $this->blogPostRepository->createBlogPost($request);
            session()->flash('success', 'Blog post created successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Blog post created successfully.',
                'redirect_url' => route('blog-posts.index'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        $post = $this->blogPostRepository->getById($id);
        return view('crm.blogPost.view', compact('post'));
    }

    public function edit(string $id)
    {
        $post = $this->blogPostRepository->getById($id);
        $categories = Category::orderBy('name')->get();

        return view('crm.blogPost.edit', compact('post', 'categories'));
    }

    public function update(BlogPostRequest $request, string $id)
    {
        try {
            $this->blogPostRepository->updateBlogPost($request, $id);
            session()->flash('success', 'Blog post updated successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Blog post updated successfully.',
                'redirect_url' => route('blog-posts.index'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->blogPostRepository->delete($id);
            return response()->json([
                'status' => true,
                'message' => 'Blog post deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete blog post: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function inlineUpdate(Request $request, string $id)
    {
        $this->blogPostRepository->inlineUpdate($id, $request->all());

        return response()->json(['status' => true, 'message' => 'Blog post updated successfully.']);
    }

    protected function initDataTable(Request $request)
    {
        $data = $this->blogPostRepository->initData($request);
        $activeStatus = config('constant.status.0', 'Active');
        $inactiveStatus = config('constant.status.1', 'Inactive');

        return DataTables::of($data)
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && $request->search['value']) {
                    $search = strtolower($request->search['value']);
                    $query->where(function ($q) use ($search) {
                        $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(status) LIKE ?', ["%{$search}%"])
                            ->orWhereHas('category', function ($categoryQuery) use ($search) {
                                $categoryQuery->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                            });
                    });
                }
            })
            ->addColumn('title', function ($row) {
                $title = $row->title ?? '-';
                return '<div class="w-200px inline-edit-cell message-cell" data-full="' . e($title) . '" data-id="' . $row->id . '" data-field="title" data-type="text" data-value="' . e($title) . '" title="Double click to edit">' . e(Str::limit($title, 55)) . '</div>';
            })
            ->addColumn('category', function ($row) {
                return '<div class="w-150px inline-edit-cell" data-id="' . $row->id . '" data-field="category_id" data-type="category" data-value="' . e($row->category_id ?? '') . '" title="Double click to edit">' . e($row->category->name ?? '-') . '</div>';
            })
            ->addColumn('status', function ($row) use ($activeStatus, $inactiveStatus) {
                $isActive = $row->status === $activeStatus;
                $badge = $isActive ? 'success' : 'secondary';
                $status = $isActive ? $activeStatus : $inactiveStatus;

                return '<div class="w-100px inline-edit-cell" data-id="' . $row->id . '" data-field="status" data-type="status" data-value="' . e($status) . '" title="Double click to edit"><span class="badge bg-' . $badge . '">' . e($status) . '</span></div>';
            })
            ->addColumn('is_featured', function ($row) {
                return $row->is_featured
                    ? '<span class="badge bg-primary">Yes</span>'
                    : '<span class="badge bg-secondary">No</span>';
            })
            ->addColumn('published_at', function ($row) {
                return $row->published_at ? formatDateTimeIST($row->published_at) : '-';
            })
            ->addColumn('created_at', function ($row) {
                return formatDateTimeIST($row->created_at);
            })
            ->addColumn('action', function ($row) {
                return view('crm.blogPost.action', compact('row'))->render();
            })
            ->rawColumns(['title', 'category', 'status', 'is_featured', 'action'])
            ->make(true);
    }
}
