<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate the permissions table before inserting
        DB::table('permissions')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // List of permissions with optional grouping
        $permissions = [
            'dashboard-view',

            'profile-update',

            'user-list',
            'user-add',
            'user-edit',
            'user-delete',
            'user-view',

            'role-list',
            'role-add',
            'role-edit',
            'role-delete',
            'role-view',

            //Contact
            'contact-manage',
            'contact-add',
            'contact-edit',
            'contact-delete',
            'contact-import',
            'contact-assign',

            //Lead
            'lead-list',
            'lead-add',
            'lead-edit',
            'lead-delete',
            'lead-transfer',

            //Quotation
            'quotation-list',
            'quotation-add',
            'quotation-edit',
            'quotation-delete',

            //Hotel master
            'hotel-list',
            // 'hotel-add',
            'hotel-edit',
            'hotel-delete',

            //Sightseeing master
            'sightseeing-list',
            // 'sightseeing-add',
            'sightseeing-edit',
            'sightseeing-delete',
            

            //Booking 
            'booking-list',
            'booking-confirm',

            //Payment
            'payment-list',
            'payment-add',
            'payment-history',
            'payment-edit',
            'payment-delete',

            //document
            'document-list',
            'document-add',
            'document-edit',
            'document-delete',
            'document-download',


            //Setting
            'settings-manage',

            //Notification
            // 'notification-list'

            //Activity
            'activity-list',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
