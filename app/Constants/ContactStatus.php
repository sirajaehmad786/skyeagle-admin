<?php

namespace App\Constants;

/**
 * Contact status constants. Use these instead of magic strings.
 * Values match config('constant.contact_status').
 *
 * Usage:
 *   Model:  'status' => ContactStatus::ACTIVE
 *   Controller: if ($contact->status === ContactStatus::ACTIVE)
 *   Validation: 'status' => 'in:' . implode(',', ContactStatus::all())
 */
final class ContactStatus
{
    public const ACTIVE = 'active';
    public const INACTIVE = 'inactive';
    public const BLOCK = 'block';

    /**
     * All allowed status values (for validation rules).
     */
    public static function all(): array
    {
        return array_values(config('constant.contact_status', [
            self::ACTIVE,
            self::INACTIVE,
            self::BLOCK,
        ]));
    }

    /**
     * Default status for new contacts.
     */
    public static function default(): string
    {
        return self::ACTIVE;
    }
}
