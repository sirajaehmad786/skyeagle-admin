<?php

namespace App\Repositories;

use App\Models\Activity;

class ActivityRepository extends BaseRepository
{

    public function __construct(Activity $activity)
    {
        parent::__construct($activity);
    }

    public function initData($request)
    {
        $query = Activity::with('user');
        if ($request->search_text) {
            $search = $request->search_text;
            $query->where(function ($q) use ($search) {
                $q->where('module', 'like', "%$search%")
                ->orWhere('activity_type', 'like', "%$search%")
                ->orWhere('activity_action', 'like', "%$search%")
                ->orWhereHas('user', function ($userQuery) use ($search) {
                    // ✅ FULL NAME SEARCH
                    $userQuery->whereRaw(
                        "CONCAT(first_name, ' ', last_name) LIKE ?",
                        ["%{$search}%"]
                    );
                });
            });
        }
        return $query;
    }

}
