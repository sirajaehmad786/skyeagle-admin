<?php

namespace App\Repositories;

use App\Models\SightSeeingMaster;
use Illuminate\Support\Facades\Storage;

class SightseeingRepository
{
    /**
     * Get all Sightseeings with optional filters
     */
    public function getSightSeeings($request)
    {
        $query = SightSeeingMaster::with(['user']);                

        if ($request->filled('sightseeing_search')) {

            $search = trim($request->sightseeing_search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Find Sightseeing by ID
     */
    public function find($id)
    {
        return SightSeeingMaster::findOrFail($id);
    }

    /**
     * Create new Sightseeing
     */
    public function create(array $data)
    {
        $data['user_id'] = auth()->id();
        if (!empty($data['sight_image'])) {
            $path = $data['sight_image']->store('sightseeing/master', 'public');
            $data['images'] = $path;
        }
        unset($data['sight_image'], $data['delete_sight_image']);
        return SightSeeingMaster::create($data);
    }

    /**
     * Update existing Sightseeing
     */
    public function update($id, array $data)
    {
        $sightseeing = $this->find($id);
        /**
         * DELETE OLD IMAGE (if remove clicked OR new uploaded)
         */
        if (
            (!empty($data['delete_sight_image']) && $data['delete_sight_image'] == 1)
            || !empty($data['sight_image'])
        ) {

            if (!empty($sightseeing->images)) {
                Storage::disk('public')->delete($sightseeing->images);
            }
        }
        /**
         * Upload new image
         */
        if (!empty($data['sight_image'])) {
            $path = $data['sight_image']->store('sightseeing/master', 'public');
            $data['images'] = $path;
        } elseif (!empty($data['delete_sight_image']) && $data['delete_sight_image'] == 1) {

            // user removed image but did not upload new one
            $data['images'] = null;
        }
        unset($data['sight_image'], $data['delete_sight_image']);
        $sightseeing->update($data);
        return $sightseeing;
    }


    /**
     * Delete Sightseeing
     */
    public function delete($id)
    {
        $sightSeeing = $this->find($id);
        if ($sightSeeing->images && Storage::disk('public')->exists($sightSeeing->images)) {
            Storage::disk('public')->delete($sightSeeing->images);
        }
        return $sightSeeing->delete();
    }
}
