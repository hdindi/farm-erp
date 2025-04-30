<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use App\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        DB::table('permissions')->insert([
            ['name' => 'create', 'description' => 'Ability to create records'],
            ['name' => 'read', 'description' => 'Ability to view records'],
            ['name' => 'update', 'description' => 'Ability to update records'],
            ['name' => 'delete', 'description' => 'Ability to delete records'],
        ]);
    }
}
