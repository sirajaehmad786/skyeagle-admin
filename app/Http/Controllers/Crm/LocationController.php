<?php 

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\City;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

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


    public function searchGeoapifyCities(Request $request)
    {
        $request->validate([
            'term' => 'required|string|min:2|max:100',
        ]);

        $apiKey = config('services.geoapify.api_key');
        if (empty($apiKey)) {
            return response()->json([
                'status' => false,
                'message' => 'Geoapify API key is not configured. Add GEOAPIFY_API_KEY to your .env file.',
                'data' => [],
            ], 503);
        }

        $term = trim($request->get('term'));
        $cacheKey = 'geoapify_cities_' . md5(strtolower($term));

        try {
            if (Cache::has($cacheKey)) {
                return response()->json([
                    'status' => true,
                    'data' => Cache::get($cacheKey),
                ]);
            }

            $response = $this->geoapifyHttp()->get('https://api.geoapify.com/v1/geocode/autocomplete', [
                'text' => $term,
                'apiKey' => $apiKey,
                'limit' => 8,
                'type' => 'city',
                'lang' => 'en',
            ]);

            if (! $response->successful()) {
                Log::warning('Geoapify autocomplete request failed', [
                    'status' => $response->status(),
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Unable to fetch city suggestions.',
                    'data' => [],
                ], 502);
            }

            $data = collect($response->json('features', []))
                ->map(function (array $feature) {
                    $props = $feature['properties'] ?? [];
                    $label = trim($this->formatGeoapifyCityLabel($props));

                    if ($label === '' || $label === 'Unknown') {
                        return null;
                    }

                    return [
                        'label' => $label,
                        'value' => $label,
                    ];
                })
                ->filter()
                ->unique('value')
                ->values()
                ->all();

            Cache::put($cacheKey, $data, now()->addDay());

            return response()->json([
                'status' => true,
                'data' => $data,
            ]);
        } catch (Exception $e) {
            Log::error('Geoapify autocomplete error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while fetching cities.',
                'data' => [],
            ], 500);
        }
    }

    private function geoapifyHttp()
    {
        $client = Http::connectTimeout(5)->timeout(8);

        if (! config('services.geoapify.verify_ssl', true)) {
            $client = $client->withoutVerifying();
        }

        return $client;
    }

    private function formatGeoapifyCityLabel(array $props): string
    {
        $parts = array_filter([
            $props['city'] ?? $props['name'] ?? null,
            $props['state'] ?? null,
            $props['country'] ?? null,
        ]);

        if (! empty($parts)) {
            return implode(', ', array_unique($parts));
        }

        return trim($props['formatted'] ?? '') ?: 'Unknown';
    }

}
