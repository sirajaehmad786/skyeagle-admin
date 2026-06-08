<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    /**
     * Record an activity in a generic, reusable way.
     *
     * @param  string       $type    High-level entity type: user, setting, etc.
     * @param  string       $action  Action verb: created, updated, sent, confirmed, cancelled, received, etc.
     * @param  Model|null   $model   Related Eloquent model instance, if any
     * @param  string|null  $description  Human-readable description
     * @param  array        $metadata     Optional structured payload
     */
    public static function log(
        string $type,
        string $action,
        ?Model $model = null,
        ?string $description = null,
        array $metadata = []
    ): void {
        $referenceType = null;
        $referenceId = null;

        if ($model instanceof Model) {
            $referenceType = $model->getMorphClass();
            $referenceId = $model->getKey();
        }

        Activity::create([
            'user_id'        => Auth::id(),
            'activity_type'  => $type,
            'activity_action'=> $action,
            'reference_type' => $referenceType,
            'reference_id'   => $referenceId,
            'description'    => $description,
            'metadata'       => $metadata ?: null,
        ]);
    }
}

