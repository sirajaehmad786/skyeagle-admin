<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Create Permission
        Permission::firstOrCreate(["name"=>"edit-post"]);

        //Create role
        $role = Role::firstOrCreate(["name"=>"admin"]);

        //Assign permission to role
        $role->givePermissionTo("edit-post");


        // Assign Role to a User
        $user = User::find(1);
        $user->assignRole('writer');

        // Check permission
        $user->hasPermissionTo('edit-post'); // true
    }
}
