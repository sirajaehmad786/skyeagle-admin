<?php 

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\City;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    public function searchCity(Request $request)
    {
        $term = $request->get('term');

        $cities = City::with(['state', 'country'])
            ->where('name', 'like', "%{$term}%")
            ->limit(10)
            ->get();

        // Format for jQuery UI autocomplete
        return response()->json(
            $cities->map(function ($city) {
                return [
                    'id' => $city->id,
                    'label' => $city->name . ', ' . $city->state->name . ', ' . $city->country->name,
                    'value' => $city->name
                ];
            })
        );
    }

    public function getCityDetails($id)
    {
        try{
            $city = City::with(['state', 'country'])->findOrFail($id);
            return response()->json([
                'status' => true,
                'city' => $city->name,
                'state'   => $city->state->name,
                'country' => $city->country->name
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => "Something went wrong"
            ]);
        }
    }
}
