<?php

// database/seeders/UsersTableSeeder.php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@farm.com',
                'phone_number' => '+254700000001',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
            [
                'name' => 'Farm Manager',
                'email' => 'manager@farm.com',
                'phone_number' => '+254700000002',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
            [
                'name' => 'Veterinary Officer',
                'email' => 'vet@farm.com',
                'phone_number' => '+254700000003',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
            [
                'name' => 'Sales Representative',
                'email' => 'sales@farm.com',
                'phone_number' => '+254700000004',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
