<?php

namespace App\Support;

use App\Models\User;

final class AdminAccess
{
    public static function activeStatus(): string
    {
        return (string) config('constant.user_status.Active', 'Active');
    }

    public static function userMayAccessPanel(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->status === self::activeStatus();
    }
}
