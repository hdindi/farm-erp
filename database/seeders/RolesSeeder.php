<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use App\Models\Role;



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
