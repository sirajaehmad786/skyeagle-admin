<?php

namespace App\Http\Controllers\Crm\Master;

use App\Enums\ActivityAction;
use App\Enums\ActivityType;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSightSeeingRequest;
use App\Http\Requests\UpdateSightSeeingRequest;
use App\Models\SightSeeingMaster;
use App\Repositories\SightseeingRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class SightSeeingController extends Controller
{
    protected $sightseeingRepository;

    public function __construct(SightseeingRepository $sightseeingRepository)
    {
        $this->middleware('permission:sightseeing-list')->only('index','initDataTable');
        // $this->middleware('permission:sightseeing-add')->only('create', 'store');
        $this->middleware('permission:sightseeing-edit')->only('edit', 'update');
        $this->middleware('permission:sightseeing-delete')->only('destroy');

        $this->sightseeingRepository = $sightseeingRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->initDataTable($request);
        }
        return view('crm.master.sightseeing.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('crm.master.sightseeing.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSightSeeingRequest $request)
    {
        $data = $request->validated();
        $sightseeing = $this->sightseeingRepository->create($data);
        activityLog(
            'Sightseeing Module',
            ActivityType::SIGHTSEEING,
            ActivityAction::CREATE,
            SightSeeingMaster::class,
            $sightseeing->id ?? null,
            'Sightseeing created successfully',
            [],
            $data,
            [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'added_by' => auth()->id()
            ]
        );
        return response()->json([
            'status' => true,
            'message' => 'Sightseeing created successfully.',
            'redirect' => route('sightseeings.index')
        ]);
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
        $item = $this->sightseeingRepository->find($id);
        return view('crm.master.sightseeing.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSightSeeingRequest  $request, string $id)
    {
        
        $data = $request->validated();
        $item = $this->sightseeingRepository->update($id,$data);
        $oldData = $item->toArray();
        activityLog(
            'Sightseeing Module',
            ActivityType::SIGHTSEEING,
            ActivityAction::UPDATE,
            SightSeeingMaster::class,
            $item->id ?? null,
            'Sightseeing updated successfully',
            $oldData,
            $data,
            [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'added_by' => auth()->id()
            ]
        );
        return redirect()->route('sightseeings.index')
        ->with('success', 'Sightseeing updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = $this->sightseeingRepository->find($id);
        $oldData = $item ? $item->toArray() : [];

        $this->sightseeingRepository->delete($id);
        activityLog(
            'Sightseeing Module',
            ActivityType::SIGHTSEEING,
            ActivityAction::DELETE,
            SightSeeingMaster::class,
            $id,
            'Sightseeing deleted successfully',
            $oldData,
            [],
            [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'added_by' => auth()->id()
            ]
        );
        return response()->json([
            'status' => true,
            'message' => 'Sightseeing deleted successfully.'
        ]);
    }

    protected function initDataTable($request)
    {
        $data = $this->sightseeingRepository->getSightSeeings($request);
        return DataTables::of($data)
            ->orderColumn('title', 'title $1')
            ->addColumn('images', function ($row) {
                if ($row->images) {
                    return '<img src="' . asset('storage/' . $row->images) . '" 
                    class="img-thumbnail datatable-sight-img">';
                }
                return '<span class="badge bg-secondary">No Image</span>';
            })

            ->addColumn('title', function ($row) {
                return '<div>' . e($row->title) . '</div>';
            })
            ->addColumn('description', function ($row) {
                return '<div>' . Str::limit(strip_tags($row->description), 60) . '</div>';
            })
            ->addColumn('created_by', function ($row) {
                return $row->user ? $row->user->name : '-';
            })
            ->addColumn('action', function ($row) {
                return view('crm.master.sightseeing.action', compact('row'))->render();
            })
            ->rawColumns(['images', 'title', 'description', 'created_by', 'action'])
            ->make(true);
    }
}
