<?php

namespace App\Support;

use App\Models\User;

final class AdminAccess
{
    public static function superAdminRoleId(): int
    {
        return (int) config('constant.admin_access.role_ids.super_admin', 1);
    }

    /** @return list<int> */
    public static function allowedLoginRoleIds(): array
    {
        $ids = config('constant.admin_access.allowed_login_role_ids', [1]);

        return array_values(array_map('intval', (array) $ids));
    }

    public static function activeStatus(): string
    {
        return (string) config('constant.user_status.Active', 'Active');
    }

    public static function userMayAccessPanel(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return in_array((int) $user->role_id, self::allowedLoginRoleIds(), true)
            && $user->status === self::activeStatus();
    }
}
