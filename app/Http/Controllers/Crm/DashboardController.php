<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;
    protected $userRepository;

    public function __construct(DashboardService $dashboardService, UserRepository $userRepository)
    {
        $this->dashboardService = $dashboardService;
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        $selectedUserId = $request->filled('user_id') ? (int) $request->get('user_id') : null;
        $this->dashboardService->setFilterUserId($selectedUserId);

        $activities = $this->dashboardService->getRecentActivities(10);
        $dashboardFilterUsers = $this->userRepository->userList();

        return view('crm.dashboard.index', compact(
            'activities',
            'dashboardFilterUsers',
            'selectedUserId'
        ));
    }

    public function data(Request $request)
    {
        $selectedUserId = $request->filled('user_id') ? (int) $request->get('user_id') : null;
        $this->dashboardService->setFilterUserId($selectedUserId);
        $period = $request->get('period', 'monthly');

        return response()->json([
            'status' => true,
            'data' => $this->dashboardService->getDashboardData($period),
        ]);
    }
}
