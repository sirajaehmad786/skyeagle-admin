<?php

namespace App\Repositories;

use App\Models\Airport;
use App\Models\Booking;
use App\Models\Quotation;
use App\Models\QuotationFlight;
use App\Models\QuotationFlightItem;
use App\Models\QuotationHotel;
use App\Models\QuotationSight;
use App\Models\QuotationSightItem;
use App\Models\QuotationVisa;
use App\Models\SightSeeingMaster;
use App\Models\Lead;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;

class QuotationRepository extends BaseRepository
{

    public function __construct(Quotation $quotation)
    {
        parent::__construct($quotation);
    }

    public function saveQuotation($request)
    {
        $data = $request->except(['_token', '_method']);

        $data['user_id'] = auth()->user()->id;
        return $this->model->create($data);
    }

    /**
     * Create a quotation directly from a lead (no form). Used when creating from lead action.
     */
    public function createFromLead(Lead $lead)
    {
        $companies = config('constant.companies', []);
        $companyId = !empty($companies) ? array_key_first($companies) : null;

        $data = [
            'user_id' => auth()->id(),
            'lead_id' => $lead->id,
            'contact_id' => $lead->contact_id,
            'start_date' => $lead->start_date,
            'end_date' => $lead->end_date,
            'company_id' => $companyId,
        ];

        return $this->model->create($data);
    }

    public function findQuotation($id)
    {
        return  $this->model->with(['lead', 'flight', 'flight.items', 'visa', 'sightseeing', 'hotel', 'hotel.hotel', 'booking'])->findOrFail($id);
    }

    public function update($request, $id)
    {
        $data = $request->except(['_token', '_method']);
        $data['amount_description_services'] = $request->input('amount_description_services', []);
        $quotation = $this->model->find($id);
        $quotation->update($data);
    }

    public function initData($request)
    {
        
        $query = $this->model
        ->with(['contact', 'lead', 'user', 'leadBooking'])
        ->select('quotations.*');

        // Keep only one quotation per lead (latest quotation id).
        $query->whereIn('quotations.id', function ($subQuery) {
            $subQuery->from('quotations')
                ->selectRaw('MAX(id)')
                ->groupBy('lead_id');
        });

        $authUser = auth()->user();
        
        if ($request->filled('search_text')) {
            $search = $request->search_text;

            $query->where(function ($q) use ($search) {

                $q->orWhereHas('contact', function ($c) use ($search) {
                    $c->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('mobile_no', 'LIKE', "%{$search}%");
                });

                $q->orWhereHas('lead', function ($l) use ($search) {
                    $l->where('lead_code', 'LIKE', "%{$search}%");
                });
            });
        }

        if ($request->filled('filter_name')) {
            $name = $request->filter_name;

            $query->whereHas('contact', function ($c) use ($name) {
                $c->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$name}%"]);
            });
        }

        if ($request->filled('filter_mobile_no')) {
            $mobile = $request->filter_mobile_no;

            $query->whereHas('contact', function ($c) use ($mobile) {
                $c->where('mobile_no', 'LIKE', "%{$mobile}%");
            });
        }

        if ($request->filled('filter_email')) {
            $email = $request->filter_email;

            $query->whereHas('contact', function ($c) use ($email) {
                $c->where('email', 'LIKE', "%{$email}%");
            });
        }

        if ($request->filled('filter_created_by')) {
            $query->where('user_id', $request->filter_created_by);
        }
        
        return $query;
    }

    public function getByLead($leadId)
    {
        /* return $this->model
            ->with(['contact', 'user'])
            ->where('lead_id', $leadId)
            ->orderBy('id', 'desc')
            ->get(); */
            $query = $this->model
        ->with(['contact', 'lead', 'user', 'leadBooking'])
        ->select('quotations.*')
        ->where('lead_id',$leadId)
        ->orderBy('created_at', 'desc')->get();
        return $query;
    }

    public function saveFlightData($request)
    {
        try {
            $quotationData = $request->except(['_token', '_method', 'flight_multi_from', 'flight_multi_to', 'flight_multi_date', 'remove_item_id']);
            $flightItem = $request->only(['flight_multi_from', 'flight_multi_to', 'flight_multi_date']);

            $flight = QuotationFlight::updateOrCreate(
                [
                    "quotation_id" => $quotationData['quotation_id'],
                    "lead_id" => $quotationData['lead_id'],
                ],
                $quotationData
            );

            //if select travel trip multi city
            if ($flight && $quotationData['trip_type'] == 'multi_city') {
                if (count($flightItem) > 0) {

                    foreach ($flightItem['flight_multi_from'] as $key => $item) {

                        $itemData = [
                            'quotation_id' => $quotationData['quotation_id'],
                            'flight_id' => $flight->id,
                            'from_city' => $item,
                            'to_city' => $flightItem['flight_multi_to'][$key],
                            'date' =>  $flightItem['flight_multi_date'][$key],
                        ];

                        QuotationFlightItem::updateOrCreate(
                            [
                                'flight_id' => $flight->id,
                                'from_city' => $itemData['from_city'],
                                'to_city' => $itemData['to_city']
                            ],
                            $itemData
                        );
                    }
                }

                if ($request->remove_item_id) {
                    $deleteIdArr = explode(',', $request->remove_item_id);
                    QuotationFlightItem::whereIn('id', $deleteIdArr)->delete();
                }
            } else {
                $flight->items()->delete();
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Quotation update failed: " . $e->getMessage());
            // you can throw exception back or return false
            throw $e;
        }
    }

    public function saveVisaData($request)
    {
        DB::beginTransaction();
        try {
            $visaAdultServiceCharge = (float) ($request->visa_adult_service_charge ?? 0);
            $visaChildServiceCharge = (float) ($request->visa_child_service_charge ?? 0);

            Quotation::where('id', $request->quotation_id)
                ->update([
                    'visa_adult_service_charge' => $visaAdultServiceCharge,
                    'visa_child_service_charge' => $visaChildServiceCharge,
                ]);
            if (!empty($request->remove_visa_id)) {
                $removeIds = explode(',', $request->remove_visa_id);
                QuotationVisa::whereIn('id', $removeIds)->delete();
            }
            if (!empty($request->visa_country)) {
                foreach ($request->visa_country as $index => $visa_country) {
                    $itemId = $request->visa_item_id[$index] ?? null;

                    $data = [
                        'lead_id'         => $request->lead_id,
                        'quotation_id'    => $request->quotation_id,
                        'visa_country'    => $visa_country,
                        'visa_category'   => $request->visa_category[$index],
                        'visa_travel_date' => date('Y-m-d', strtotime($request->visa_travel_date[$index])),
                        'visa_adults'     => $request->visa_adults[$index],
                        'visa_child'      => $request->visa_child[$index] ?? 0,
                        'visa_infant'     => $request->visa_infant[$index] ?? 0,
                        'visa_type'       => $request->visa_type[$index] ?? null,
                        'visa_adult_price' => $request->visa_adult_price[$index] ?? 0,
                        'visa_child_price' => $request->visa_child_price[$index] ?? 0,
                        'visa_remarks'    => $request->visa_remarks[$index] ?? '',
                    ];

                    if ($itemId && QuotationVisa::where('id', $itemId)->exists()) {
                        QuotationVisa::where('id', $itemId)->update($data);
                    } else {
                        QuotationVisa::create($data);
                    }
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function sightseeingVisaData($request)
    {
        try {

            $visaData = $request->except(['_token', '_method']);
            return QuotationVisa::updateOrCreate(
                [
                    "quotation_id" => $visaData['quotation_id'],
                    "lead_id" => $visaData['lead_id'],
                ],
                $visaData
            );
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Quotation update failed: " . $e->getMessage());
            // you can throw exception back or return false
            throw $e;
        }
    }

    public function saveSightseeing($request)
    {
        try {
            DB::beginTransaction();
            //delte sightseeing row
            if (!empty($request->remove_sigh_id)) {
                $ids = explode(',', $request->remove_sigh_id);
                self::removeSightseeingRow($ids);
            }

            // Delete sub sightseeing rows
            if (!empty($request->remove_sub_sight_id)) {
                $ids = explode(',', $request->remove_sub_sight_id);
                self::removeSightseeingItems($ids);
            }

            if ($request->day_no) {
                foreach ($request->day_no as $key => $day) {
                    $sight_id = $request->sight_id[$key] ?? '';

                    //Add sightseeing data
                    $reqDat = [
                        'quotation_id' => $request->quotation_id,
                        'lead_id'      => $request->lead_id,
                        'day_no'       => $day,
                        'date'        => $request->date[$key],
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];

                    $sightseeing = QuotationSight::updateOrCreate(
                        [
                            'quotation_id' => $reqDat['quotation_id'],
                            'lead_id'      => $reqDat['lead_id'],
                            'day_no'       => $reqDat['day_no'],
                        ],
                        $reqDat
                    );

                    //Insert item
                    if ($sightseeing) {
                        if (isset($request->title[$key])) {
                            foreach ($request->title[$key] as $innerKey => $title) {
                                $sub_item_id = isset($request->sub_item_id[$key][$innerKey]) ? $request->sub_item_id[$key][$innerKey] : '';
                                $is_from_master = isset($request->is_from_master[$key][$innerKey]) ? $request->is_from_master[$key][$innerKey] : '';

                                if ($title) {
                                    $subDesc = $request->sub_description[$key][$innerKey] ?? null;
                                    $description[] = $subDesc;
                                    //When data select from master
                                    if (!empty($is_from_master)) {
                                        $subData = [
                                            'quotation_sight_id' => $sightseeing->id,
                                            'title' => $title,
                                            'description' => $subDesc,
                                        ];

                                        $sub_item = QuotationSightItem::find($sub_item_id);
                                        if (!empty($sub_item->image)) {
                                            Storage::disk('public')->delete($sub_item->image);
                                        }
                                        $sightMaster = SightSeeingMaster::where('title', $title)->first();
                                        $url = asset('storage/' . $sightMaster->images);
                                        $relativePath = str_replace("/storage/", "", parse_url($url, PHP_URL_PATH));

                                        // copy file
                                        if (Storage::disk('public')->exists($relativePath)) {

                                            // Get original extension
                                            $extension = pathinfo($relativePath, PATHINFO_EXTENSION);
                                            $newFileName = 'image_' . time() . '.' . $extension;
                                            $newPath = "sightseeing/sub/" . $newFileName;
                                            Storage::disk('public')->copy($relativePath, $newPath);
                                            $subData['image'] = $newPath;
                                        }
                                        if ($sub_item_id) {
                                            QuotationSightItem::where('id', $sub_item_id)->update($subData);
                                        } else {
                                            QuotationSightItem::create($subData);
                                        }
                                    } else {

                                        $exists = SightSeeingMaster::whereRaw('LOWER(title) = ?', [strtolower($title)])->exists();
                                        
                                        if (!$exists) {
                                            $masterData = [
                                                'user_id'     => Auth::id(),
                                                'title'       => $title,
                                                'description' => $subDesc,
                                            ];
                                            
                                            if ($request->hasFile("sight_image.$key.$innerKey")) {
                                                
                                                $masterData['images'] = $request->file("sight_image.$key.$innerKey")->store("sightseeing/master", "public");
                                            }
                                            SightSeeingMaster::create($masterData);
                                        }

                                        $imagePath = null;
                                        
                                        if ($request->hasFile("sight_image.$key.$innerKey")) {
                                            $imagePath = $request->file("sight_image.$key.$innerKey")->store("sightseeing/sub", "public");
                                            
                                            //Delete existing image
                                            if (!empty($request->old_sub_sight_image[$key][$innerKey])) {
                                                Storage::disk('public')->delete($request->old_sub_sight_image[$key][$innerKey]);
                                            }
                                        }
                                    }

                                    //Create or update sub data
                                    $subData = [
                                        'quotation_sight_id' => $sightseeing->id,
                                        'title' => $title,
                                        'description' => $subDesc,
                                    ];
                                    if (!empty($imagePath)) {
                                        $subData['image'] = $imagePath;
                                    }

                                    //Delete image only
                                    if (
                                        isset($request->delete_sub_sight_image[$key][$innerKey]) &&
                                        $request->delete_sub_sight_image[$key][$innerKey] == 1
                                    ) {
                                        if (!empty($request->old_sub_sight_image[$key][$innerKey])) {
                                            Storage::disk('public')->delete($request->old_sub_sight_image[$key][$innerKey]);
                                        }
                                        $subData['image'] = Null;
                                    }

                                    if ($sub_item_id != '') {
                                        QuotationSightItem::where('id', $sub_item_id)->update($subData);
                                    } else {
                                        QuotationSightItem::updateOrCreate([
                                            'quotation_sight_id' => $subData['quotation_sight_id'],
                                            'title' => $subData['title'],
                                        ], $subData);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $updateData = [];
            
            if ($request->has('inclusion')) {
                $updateData['inclusion'] = $request->inclusion;
            }
            
            if ($request->has('exclusion')) {
                $updateData['exclusion'] = $request->exclusion;
            }
            
            if ($request->has('sightseeing_adult_price')) {
                $updateData['sightseeing_adult_price'] = $request->sightseeing_adult_price;
            }
            
            if ($request->has('sightseeing_child_price')) {
                $updateData['sightseeing_child_price'] = $request->sightseeing_child_price;
            }
 
            if ($request->has('sightseeing_adult_service_charge')) {
                $updateData['sightseeing_adult_service_charge'] = $request->sightseeing_adult_service_charge;
            }

            if ($request->has('sightseeing_child_service_charge')) {
                $updateData['sightseeing_child_service_charge'] = $request->sightseeing_child_service_charge;
            }
            if (!empty($updateData)) {
                Quotation::where('id', $request->quotation_id)->update($updateData);
            }
            
            DB::commit();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    protected function removeSightseeingRow($ids)
    {

        if (count($ids) > 0) {
            QuotationSight::whereIn('id', $ids)->get()->each->delete();
        }
    }

    protected function removeSightseeingItems($ids)
    {

        if (count($ids) > 0) {
            QuotationSightItem::whereIn('id', $ids)->get()->each->delete();
        }
    }

    public function saveHotels($request)
    {
        DB::beginTransaction();
        try {
            $doubleRoomServicePerPerson = (float) ($request->double_room_service_price ?? 0);
            $tripleRoomServicePerPerson = (float) ($request->triple_room_service_price ?? 0);
            $cnbServicePerPerson = (float) ($request->total_cnb_service_price ?? 0);
            $singleRoomServicePerPerson = (float) ($request->single_room_service_price ?? 0);

            // Aggregate passenger counts from room counts across all hotel rows
            // (Single=1 person, Double=2, Triple=3, CNB+CWB=1 each)
            $doublePersonsTotal = 0;
            $triplePersonsTotal = 0;
            $cnbPersonsTotal = 0;
            $cwbPersonsTotal = 0;
            $singlePersonsTotal = 0;

            foreach ($request->hotel_id as $index => $hotel_id) {
                $itemId = $request->item_id[$index] ?? null;

                $doubleRooms = (int) ($request->total_room[$index] ?? 0);
                $tripleRooms = (int) ($request->triple_room[$index] ?? 0);
                $cnbCount = (int) ($request->total_cnb[$index] ?? 0);
                $cwbCount = (int) ($request->total_cwb[$index] ?? 0);
                $singleCount = (int) ($request->single_room[$index] ?? 0);

                $doublePersonsTotal += $doubleRooms * 2;
                $triplePersonsTotal += $tripleRooms * 3;
                $cnbPersonsTotal += $cnbCount;
                $cwbPersonsTotal += $cwbCount;
                $singlePersonsTotal += $singleCount;

                $data = [
                    'lead_id' => $request->lead_id,
                    'quotation_id'     => $request->quotation_id,
                    'hotel_id'         => $hotel_id,
                    'check_in'         => $request->check_in[$index],
                    'check_out'        => $request->check_out[$index],
                    'total_room'       => $doubleRooms,
                    'single_room'     => $singleCount,
                    'meals'            => $request->meals[$index],
                    'room_type'        => $request->room_type[$index],
                    'destination'      => $request->destination[$index],
                    'total_cnb'        => $cnbCount,
                    'total_cwb'        => $cwbCount,
                    'single_room_price'=> ($request->single_room_price[$index] ?? null) ?: 0,
                    'total_cwb_price'  => ($request->total_cwb_price[$index] ?? null) ?: 0,
                    'total_cnb_price'  => ($request->total_cnb_price[$index] ?? null) ?: 0,
                    'triple_room'      => $tripleRooms,
                    'triple_room_price'=> ($request->triple_room_price[$index] ?? null) ?: 0,
                    'total_room_price' => ($request->total_room_price[$index] ?? null) ?: 0,
                    'hotel_remarks'    => ($request->hotel_remarks[$index]) ? $request->hotel_remarks[$index] : '',
                ];

                if ($itemId) {
                    $hotel = QuotationHotel::find($itemId);
                    if ($hotel) {
                        $hotel->update($data);
                    }
                } else {
                    QuotationHotel::create($data);
                }
            }
            
            if ($request->filled('remove_hotel_id')) {
                $removeIds = array_filter(array_map('trim', explode(',', $request->remove_hotel_id)));
                $removeIds = array_filter($removeIds, 'is_numeric');
                if (count($removeIds) > 0) {
                    QuotationHotel::where('quotation_id', $request->quotation_id)
                        ->whereIn('id', $removeIds)
                        ->delete();
                }
            }

            $hotelsServicePriceTotal =
                ($doubleRoomServicePerPerson * $doublePersonsTotal) +
                ($tripleRoomServicePerPerson * $triplePersonsTotal) +
                ($cnbServicePerPerson * ($cnbPersonsTotal + $cwbPersonsTotal)) +
                ($singleRoomServicePerPerson * $singlePersonsTotal);

            Quotation::where('id', $request->quotation_id)->update([
                'double_room_service_price' => $doubleRoomServicePerPerson,
                'triple_room_service_price' => $tripleRoomServicePerPerson,
                'total_cnb_service_price'   => $cnbServicePerPerson,
                'single_room_service_price' => $singleRoomServicePerPerson,
                'hotels_service_price'       => $hotelsServicePriceTotal,
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function find($id)
    {
        return Quotation::find($id);
    }

    public function findWithHotels($id)
    {
        return Quotation::with(['lead', 'quotationHotels.hotel'])->find($id);
    }

    public function getTitleSuggestions(string $title)
    {
        return SightSeeingMaster::where('title', 'like', '%' . $title . '%')
            ->pluck('title', 'id')
            ->toArray();
    }

    public function bookingIdByQuotationId($quotation_id)
    {
        return Booking::where('quotation_id', $quotation_id)->value('id');
    }

    public function updateDiscount(int $quotationId, $discount): bool
    {
        $quotation = Quotation::find($quotationId);
        if (!$quotation) {
            return false;
        }
        $quotation->discount = $discount ?? 0;
        $quotation->save();
        $quotation->refresh();
        return true;
    }

    public function getAirports()
    {
        return Airport::select(
            'id',
            'airport_code',
            'airport_name',
            'city'
        )
        ->orderBy('city')
        ->get();
    }

    /**
     * Remove all rows for one quotation section (flight, visa, hotels, or sightseeing)
     * and clear related per-section fields on the quotation record.
     */
    public function resetQuotationTabSection(int $quotationId, string $section): void
    {
        DB::beginTransaction();
        try {
            switch ($section) {
                case 'flight':
                    $flight = QuotationFlight::where('quotation_id', $quotationId)->first();
                    if ($flight) {
                        $flight->delete();
                    }
                    break;
                case 'visa':
                    QuotationVisa::where('quotation_id', $quotationId)->delete();
                    Quotation::where('id', $quotationId)->update([
                        'visa_adult_service_charge' => 0,
                        'visa_child_service_charge' => 0,
                    ]);
                    break;
                case 'hotels':
                    QuotationHotel::where('quotation_id', $quotationId)->delete();
                    Quotation::where('id', $quotationId)->update([
                        'double_room_service_price' => 0,
                        'triple_room_service_price' => 0,
                        'total_cnb_service_price' => 0,
                        'single_room_service_price' => 0,
                        'hotels_service_price' => 0,
                    ]);
                    break;
                case 'sightseeing':
                    QuotationSight::where('quotation_id', $quotationId)->get()->each->delete();
                    Quotation::where('id', $quotationId)->update([
                        'sightseeing_adult_price' => 0,
                        'sightseeing_child_price' => 0,
                        'sightseeing_adult_service_charge' => 0,
                        'sightseeing_child_service_charge' => 0,
                    ]);
                    break;
                default:
                    throw new Exception('Invalid quotation tab section.');
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
