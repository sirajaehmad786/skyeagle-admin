<?php

namespace App\Repositories;

use App\Models\City;
use Illuminate\Support\Facades\DB;

class LocationRepository extends BaseRepository
{
    public function __construct(City $city)
    {
        parent::__construct($city);
    }

    public function getCities()
    {
        return DB::table('cities')
            ->where('flag', 1)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);
    }

    public function searchCities($search = null)
    {
        return $this->model
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })
            ->orderBy('name')
            ->limit(20) // performance
            ->get(['id', 'name']);
    }
}