<?php

use App\Services\ActivityService;

if (!function_exists('activityLog')) {

    function activityLog(
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

        $activityService = app(ActivityService::class);

        $activityService->log(
            $module,
            $type,
            $action,
            $referenceType,
            $referenceId,
            $description,
            $oldValues,
            $newValues,
            $metadata
        );
    }
}