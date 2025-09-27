<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        DB::table('roles')->insert([
            ['name' => 'Admin', 'description' => 'Full access to all modules'],
            ['name' => 'Manager', 'description' => 'Manage users and view reports'],
            ['name' => 'User', 'description' => 'Basic access to own data'],
        ]);
    }
}
