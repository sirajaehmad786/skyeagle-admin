<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role_id' => null,
            'parent_id' => null,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= 'password',
            'profile_image' => null,
            'phone' => null,
            'al_phone' => null,
            'status' => 'Active',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Super admin for this admin panel (config constant.admin_access).
     */
    public function superAdmin(): static
    {
        return $this->state(function () {
            $roleId = (int) config('constant.admin_access.role_ids.super_admin', 1);
            $now = now();

            DB::table('roles')->updateOrInsert(
                ['id' => $roleId],
                [
                    'name' => 'Super Admin',
                    'guard_name' => 'web',
                    'level' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            return [
                'role_id' => $roleId,
                'status' => (string) config('constant.user_status.Active', 'Active'),
            ];
        });
    }

    /**
     * Non–super-admin CRM user (wrong role for this panel).
     */
    public function staffRole(): static
    {
        return $this->state(function () {
            $superId = (int) config('constant.admin_access.role_ids.super_admin', 1);
            $staffRoleId = $superId === 1 ? 2 : 1;

            DB::table('roles')->updateOrInsert(
                ['id' => $staffRoleId],
                [
                    'name' => 'Staff Test Role',
                    'guard_name' => 'web',
                    'level' => 5,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            return [
                'role_id' => $staffRoleId,
                'status' => (string) config('constant.user_status.Active', 'Active'),
            ];
        });
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'status' => 'Inactive',
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
