<?php

namespace App\Http\Controllers\Crm\Master;

use App\Enums\ActivityAction;
use App\Enums\ActivityType;
use App\Http\Controllers\Controller;
use App\Http\Requests\HotelCreateRequest;
use App\Http\Requests\HotelUpdateRequest;
use App\Models\City;
use App\Models\Hotel;
use App\Models\State;
use App\Repositories\HotelRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class HotelController extends Controller
{
    protected $hotelRepository;

    public function __construct(HotelRepository $hotelRepository)
    {

        $this->middleware('permission:hotel-list')->only('index', 'initDataTable');
        // $this->middleware('permission:hotel-add')->only('create', 'store');
        $this->middleware('permission:hotel-edit')->only('edit', 'update');
        $this->middleware('permission:hotel-delete')->only('destroy');

        $this->hotelRepository = $hotelRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->initDataTable($request);
        }
        return view('crm.master.hotel.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $states = State::where('country_id', '101')->get();
        return view('crm.master.hotel.create', compact('states'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HotelCreateRequest $request)
    {
        try {
            $hotel = $this->hotelRepository->createHotel($request);
            activityLog(
                'Hotel Module',
                ActivityType::HOTEL,
                ActivityAction::CREATE,
                Hotel::class,
                $hotel->id ?? null,
                'Hotel created successfully',
                [],
                $request->all(),
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'added_by' => auth()->id()
                ]
            );
            $hotels = [];
            if ($request->has('is_from') && $request->is_from == 'modal') {
                $hotels = $this->hotelRepository->getHotels();
            }
            session()->flash('success', 'Hotel created successfully.');
            return response()->json([
                'status'   => true,
                'message' => 'Hotel created successfully',
                'hotels' => $hotels,
                'redirect_url' => route('hotels.index'),
            ]);
        } catch (\Exception $e) {
            Log::error('Hotel Create Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    public function getCitiesByState($state_id)
    {
        $cities = City::where('state_id', $state_id)->get();
        $html = '<option value="">Select City</option>';
        foreach ($cities as $city) {
            $html .= '<option value="' . $city->id . '">' . $city->name . '</option>';
        }

        return response()->json($html);
    }


    public function edit($id)
    {
        try {
            $hotel = $this->hotelRepository->findHotel($id);
            $states = State::where('country_id', 101)->pluck('name', 'id');
            $cities = City::where('state_id', $hotel->state_id)->pluck('name', 'id');
            return view('crm.master.hotel.edit', compact('hotel','states', 'cities'));
        } catch (\Exception $e) {
            Log::error('Hotel Edit Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    public function update(HotelUpdateRequest  $request, $id)
    {
        try {
            $hotel = $this->hotelRepository->findHotel($id);
            $oldData = $hotel->toArray();
            $this->hotelRepository->update($id, $request->all());
            activityLog(
                'Hotel Module',
                ActivityType::HOTEL,
                ActivityAction::UPDATE,
                Hotel::class,
                $id,
                'Hotel updated successfully',
                $oldData,
                $request->all(),
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'added_by' => auth()->id()
                ]
            );

            return response()->json([
            'status' => true,
            'message' => 'Hotel updated successfully.',   // Toastify message
            'redirect_url' => route('hotels.index'),      // Redirect URL for JS
        ]);
        } catch (\Exception $e) {
            Log::error('Hotel Update Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $hotel = Hotel::findOrFail($id);
        $oldData = $hotel->toArray();
        if ($hotel->images && Storage::disk('public')->exists($hotel->images)) {
            Storage::disk('public')->delete($hotel->images);
        }
        $hotel->delete();
        activityLog(
            'Hotel Module',
            ActivityType::HOTEL,
            ActivityAction::DELETE,
            Hotel::class,
            $id,
            'Hotel deleted successfully',
            $oldData,
            [],
            [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'added_by' => auth()->id()
            ]
        );
        return response()->json(['status' => true, 'message' => 'Hotel deleted successfully.']);
    }



    /**
     * initDataTable function use for load data
     */
    protected function initDataTable($request)
    {
        $data = $this->hotelRepository->initData($request);
        return DataTables::of($data)
            ->orderColumn('name', 'name $1')
            ->orderColumn('created_at', 'created_at $1')
            ->editColumn('images', function ($row) {
                if (!empty($row->images)) {
                    return '<div class="w-50"><img src="' . asset(Storage::url($row->images)) . '" alt="Hotel Image" class="rounded me-1" height="50" width="50"></div>';
                }
                return;
            })
            ->addColumn('created_at', function ($row) {
                return formateDate($row->created_at);
            })
            ->addColumn('action', function ($row) {
                return view('crm.master.hotel.action', compact('row'))->render();
            })
            ->rawColumns(['images', 'created_at', 'action'])
            ->make(true);
    }
}
