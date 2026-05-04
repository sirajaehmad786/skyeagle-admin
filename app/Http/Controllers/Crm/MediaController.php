<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Repositories\MediaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MediaController extends Controller
{
    protected $mediaRepository;
    public function __construct(MediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->initDataTable($request);
        }
        return view('crm.media.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('crm.media.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $media = $this->mediaRepository->createMedia($request);
            $this->mediaRepository->uploadMediaImages($request, $media->id);
            DB::commit();
            return response()->json([
                'status' => true,
                'redirect_url' => route('media.index'),
                'message' => 'Media created successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $media = $this->mediaRepository->findWithImages($id);
        return view('crm.media.view', compact('media'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $media = $this->mediaRepository->findWithImages($id);
        return view('crm.media.edit', compact('media'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $this->mediaRepository->updateMedia($request, $id);
            $this->mediaRepository->uploadMediaImages($request, $id);
            $this->mediaRepository->deleteRemovedImages($request);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Media updated successfully.',
                'redirect_url' => route('media.index')
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->mediaRepository->deleteMediaRecord($id);
        return response()->json([
            'status' => true,
            'message' => 'Media deleted successfully'
        ]);
    }

    protected function initDataTable($request)
    {
        $data = $this->mediaRepository->initData($request);
        return DataTables::of($data)
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && $request->search['value']) {
                    $search = strtolower($request->search['value']);
                    $query->where(function ($q) use ($search) {
                        $q->whereRaw('LOWER(module) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(section) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(title) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(sub_title) LIKE ?', ["%{$search}%"]);
                    });
                }
            })
            ->addColumn('module', function ($row) {
                return '<div class="w-150px">' . ($row->module ?? '-') . '</div>';
            })
            ->addColumn('section', function ($row) {
                return '<div class="w-150px">' . ($row->section ?? '-') . '</div>';
            })
            ->addColumn('title', function ($row) {
                return '<div class="w-150px">' . ($row->title ?? '-') . '</div>';
            })
            ->addColumn('sub_title', function ($row) {
                return '<div class="w-150px">' . ($row->sub_title ?? '-') . '</div>';
            })
            ->addColumn('is_active', function ($row) {
                return '<div class="w-150px">' . ($row->is_active ?? '-') . '</div>';
            })
            ->addColumn('created_at', function ($row) {
                return formateDate($row->created_at);
            })
            ->addColumn('action', function ($row) {
                return view('crm.media.action', compact('row'))->render();
            })
            ->rawColumns([
                'module',
                'section',
                'title',
                'sub_title',
                'is_active',
                'created_at',
                'action'
            ])
            ->make(true);
    }
}
