<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class ModulesSeeder extends Seeder
{
    public function run()
    {
        DB::table('modules')->insert([
            ['name' => 'User Management', 'description' => 'Manage application users'],
            ['name' => 'Inventory', 'description' => 'Manage stock and supplies'],
            ['name' => 'Reports', 'description' => 'Access reports and analytics'],
        ]);
    }
}
