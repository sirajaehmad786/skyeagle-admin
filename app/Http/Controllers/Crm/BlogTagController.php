<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlogTagRequest;
use App\Repositories\BlogTagRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BlogTagController extends Controller
{
    public function __construct(protected BlogTagRepository $blogTagRepository)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->initDataTable($request);
        }

        $statuses = config('constant.status', []);
        return view('crm.blogTag.index', compact('statuses'));
    }

    public function create()
    {
        return view('crm.blogTag.create');
    }

    public function store(BlogTagRequest $request)
    {
        try {
            $this->blogTagRepository->createTag($request->all());
            session()->flash('success', 'Blog tag created successfully.');

            return response()->json([
                'status' => true,
                'redirect_url' => route('blog-tags.index'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        return redirect()->route('blog-tags.edit', $id);
    }

    public function edit(string $id)
    {
        $tag = $this->blogTagRepository->findOrFail($id);
        return view('crm.blogTag.edit', compact('tag'));
    }

    public function update(BlogTagRequest $request, string $id)
    {
        try {
            $this->blogTagRepository->updateTag($id, $request->all());
            session()->flash('success', 'Blog tag updated successfully.');

            return response()->json([
                'status' => true,
                'redirect_url' => route('blog-tags.index'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->blogTagRepository->delete($id);
            return response()->json(['status' => true, 'message' => 'Blog tag deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to delete blog tag: ' . $e->getMessage()], 500);
        }
    }

    public function inlineUpdate(Request $request, string $id)
    {
        $this->blogTagRepository->inlineUpdate($id, $request->all());

        return response()->json(['status' => true, 'message' => 'Blog tag updated successfully.']);
    }

    protected function initDataTable(Request $request)
    {
        $data = $this->blogTagRepository->initData($request);
        $activeStatus = config('constant.status.0', 'Active');
        $inactiveStatus = config('constant.status.1', 'Inactive');

        return DataTables::of($data)
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && $request->search['value']) {
                    $search = strtolower($request->search['value']);
                    $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$search}%"]);
                }
            })
            ->addColumn('name', fn ($row) => '<div class="w-150px inline-edit-cell" data-id="' . $row->id . '" data-field="name" data-type="text" data-value="' . e($row->name) . '" title="Double click to edit">' . e($row->name) . '</div>')
            ->addColumn('status', fn ($row) => $row->status ? '<div class="w-100px inline-edit-cell" data-id="' . $row->id . '" data-field="status" data-type="status" data-value="1" title="Double click to edit"><span class="badge bg-success">' . e($activeStatus) . '</span></div>' : '<div class="w-100px inline-edit-cell" data-id="' . $row->id . '" data-field="status" data-type="status" data-value="0" title="Double click to edit"><span class="badge bg-secondary">' . e($inactiveStatus) . '</span></div>')
            ->addColumn('created_at', fn ($row) => formatDateTimeIST($row->created_at))
            ->addColumn('action', fn ($row) => view('crm.blogTag.action', compact('row'))->render())
            ->rawColumns(['name', 'status', 'action'])
            ->make(true);
    }
}
