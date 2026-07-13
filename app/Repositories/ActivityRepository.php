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

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        if ($request->filled('activity_action')) {
            $query->where('activity_action', $request->activity_action);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('created_from')) {
            $query->where('created_at', '>=', istDateRangeToUtc($request->created_from));
        }

        if ($request->filled('created_to')) {
            $query->where('created_at', '<=', istDateRangeToUtc($request->created_to, true));
        }

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

    public function filterOptions(string $column)
    {
        return Activity::query()
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->distinct()
            ->orderBy($column)
            ->pluck($column);
    }

}
