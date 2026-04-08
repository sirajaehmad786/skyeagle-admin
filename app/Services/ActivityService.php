<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class ActivityService
{

    public function log(
        $module,
        $type,
        $action,
        $referenceType = null,
        $referenceId = null,
        $description = null,
        $oldValues = [],
        $newValues = [],
        $metadata = []
    ) {

        Activity::create([

            'user_id' => Auth::id(),

            'module' => $module,

            'activity_type' => $type,

            'activity_action' => $action,

            'reference_type' => $referenceType,

            'reference_id' => $referenceId,

            'description' => $description,

            'old_values' => $oldValues,

            'new_values' => $newValues,

            'metadata' => $metadata,

            'ip_address' => request()->ip(),

            'user_agent' => request()->userAgent(),

            'url' => request()->fullUrl(),

            'method' => request()->method()

        ]);
    }
}