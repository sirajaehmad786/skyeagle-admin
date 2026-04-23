<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    protected $categoryRepository;
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->initDataTable($request);
        }
        return view('crm.category.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('crm.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        try {
            $this->categoryRepository->createCategory($request->all());
            session()->flash('success', 'Category created successfully.');
            return response()->json([
                'status' => true, 
                'redirect_url' => route('category.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = $this->categoryRepository->find($id);
        return view('crm.category.edit',compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id)
    {
        try{
            $this->categoryRepository->update($id, [
                'name' => $request->name
            ]);
            session()->flash('success', 'Category updated successfully.');
            return response()->json([
                'status' => true,
                'redirect_url' => route('category.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->categoryRepository->delete($id);
            return response()->json([
                'status' => true,
                'message' => 'Category deleted successfully'
            ]);
    }

    protected function initDataTable($request)
    {
        $data = $this->categoryRepository->initData($request);
        return DataTables::of($data)
        ->filter(function ($query) use ($request) {
            if ($request->has('search') && $request->search['value']) {
                $search = strtolower($request->search['value']);
                $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
            }
        })
        ->addColumn('name', function ($row) {
            return '<div class="w-100px">' . $row->name . '</div>';
        })
        ->addColumn('created_at', function ($row) {
            return formateDate($row->created_at);
        })
        ->addColumn('action', function ($row) {
            return view('crm.category.action', compact('row'))->render();
        })
        ->rawColumns([
            'name',
            'action'
        ])
        ->make(true);
    }

    public function search(Request $request)
    {
        $search = $request->get('search');
        $categories = $this->categoryRepository->search($search);
        return response()->json([
            'data' => $categories
        ]);
    }
}
