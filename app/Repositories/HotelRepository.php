<?php

namespace App\Repositories;

use App\Models\Hotel;
use Illuminate\Support\Facades\Storage;

class HotelRepository extends BaseRepository
{

    public function __construct(Hotel $hotel)
    {
        parent::__construct($hotel);
    }

    public function createHotel($request)
    {
        $data = [
            'name'    => $request->name,
            'address' => $request->address,
            'state_id'  => $request->state_id,
            'city_id'   => $request->city_id,
        ];
        //Image upload
        if ($request->hasFile('images')) {
            $file = $request->file('images');
            $imagePath = $file->store('hotels', 'public');
            $data['images']  = $imagePath;
        }
        return Hotel::create($data);
    }

    public function findHotel($id){
        return $this->model->findOrFail($id);
    }

    public function update($id, $data)
    {
        $hotel = Hotel::findOrFail($id);

        if (isset($data['images']) && $data['images']->isValid()) {
            if ($hotel->images && Storage::disk('public')->exists($hotel->images)) {
                Storage::disk('public')->delete($hotel->images);
            }
            $path = $data['images']->store('hotels', 'public');
            $data['images'] = $path;
        }

        $hotel->update($data);
        return $hotel;
    }

    
    public function getHotels()
    {
        return $this->model->select('id', 'name')->orderBy('name')->get();
    }

    public function initData($request){
        $query =  $this->model->query();
       
        if ($request->filled('hotel_search')) {
            $search = str_replace(' ', '', $request->hotel_search);
            $query->whereRaw("
                REPLACE(LOWER(name), ' ', '') LIKE ?
            ", ['%' . strtolower($search) . '%']);
        }
        return $query;
    }

}
