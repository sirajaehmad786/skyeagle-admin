<?php

namespace App\Http\Controllers\Crm;

use App\Enums\ActivityAction;
use App\Enums\ActivityType;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Repositories\SettingRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    private $settingRepository;
    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = $this->settingRepository->getSettings();
        $setting = (object) array_merge(
            ['description' => '', 'visa_policy' => '', 'payment_policy' => ''],
            $settings->toArray()
        );
        return view('crm.setting.index', compact('setting'));
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
        try {
        $setting = $this->settingRepository->storeOrUpdate($request);
        // session()->flash('success', 'Terms Condition saved successfully!');
        activityLog(
            'Setting Module',
            ActivityType::SETTING,
            ActivityAction::UPDATE,
            Setting::class,
            $setting->id ?? null,
            'Terms Condition saved successfully',
            [],
            $request->all(),
            [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'added_by' => auth()->id()
            ]
        );
            return response()->json([
                'status' => true,
                'message' => 'Terms Condition saved successfully!'
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => "Something went wrong"
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Setting $setting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        //
    }
}
