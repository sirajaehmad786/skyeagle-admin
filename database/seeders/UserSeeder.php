<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Manan',
            'last_name' => 'Patel',
            'email' => 'manan@gmail.com',
            'email_verified_at' => now(),
            'status' => config('constant.user_status.Active'),
            'password' => 'Password@1',
            'remember_token' => Str::random(10),
        ]);
    }
}
