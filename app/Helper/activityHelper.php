<?php

use App\Services\ActivityService;

if (!function_exists('public_storage_url')) {
    function public_storage_url(?string $path): ?string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        $path = preg_replace('#^/?storage/#', '', str_replace('\\', '/', $path));

        return url('storage/' . ltrim($path, '/'));
    }
}

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
