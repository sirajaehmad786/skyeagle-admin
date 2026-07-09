<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        $activities = $this->dashboardService->getRecentActivities(10);

        return view('crm.dashboard.index', compact('activities'));
    }

    public function data(Request $request)
    {
        $period = $request->get('period', 'monthly');

        return response()->json([
            'status' => true,
            'data' => $this->dashboardService->getDashboardData($period),
        ]);
    }
}
