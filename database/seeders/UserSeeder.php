<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        // Create Super Admin Role
        $role = Role::firstOrCreate(['name' => 'Super Admin', 'level'=>1]);

        $data = [
            'role_id' => $role->id,
            'first_name' => 'Manan',
            'last_name' => 'Patel',
            'email' => 'manan@gmail.com',
            'email_verified_at' => now(),
            'status' => config('constant.user_status.Active'),
            'password' => 'Password@1',//password
            'remember_token' => Str::random(10),
        ];

        $permissions = Permission::all('id')->toArray();
        $permissions = array_column($permissions, 'id');
        
        // Assign all permissions to Super Admin
        $role->syncPermissions($permissions);

        $user = User::create($data);
        $user->assignRole($role->name);
    }
}
