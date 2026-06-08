<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Repositories\ActivityRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ActivityController extends Controller
{
    protected $activityRepository;

    public function __construct(ActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->initDataTable($request);
        }
        return view('crm.activity.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    protected function initDataTable($request)
    {
        $data = $this->activityRepository->initData($request);

        return DataTables::of($data)

            ->addColumn('user_name', function ($row) {
                return '<div class="w-150px">'
                    . optional($row->user)->first_name . ' ' . optional($row->user)->last_name .
                    '</div>';
            })

            ->addColumn('module', function ($row) {
                return '<div class="w-120px">'.$row->module.'</div>';
            })

            ->addColumn('activity_type', function ($row) {
                return '<div class="w-120px">'.$row->activity_type.'</div>';
            })

            ->addColumn('activity_action', function ($row) {
                return '<div class="w-120px">'.$row->activity_action.'</div>';
            })

            ->addColumn('reference_type', function ($row) {
                return '<div class="w-120px">'.$row->reference_type.'</div>';
            })

            ->addColumn('description', function ($row) {
                return '<div class="w-250px">'.$row->description.'</div>';
            })

            ->addColumn('ip_address', function ($row) {
                return '<div class="w-120px">'.$row->ip_address.'</div>';
            })

            ->addColumn('method', function ($row) {
                return '<span class="badge bg-info">'.$row->method.'</span>';
            })

            ->addColumn('created_at', function ($row) {
                return '<div class="w-150px">'.date('d M Y H:i', strtotime($row->created_at)).'</div>';
            })

            ->rawColumns([
                'user_name',
                'module',
                'activity_type',
                'activity_action',
                'reference_type',
                'description',
                'ip_address',
                'method',
                'created_at'
            ])

            ->make(true);
    }
}
