<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use App\Models\ModulePermission;

class ModulePermissionsSeeder extends Seeder
{
    public function run()
    {
        $map = [
            ['module_id' => 1, 'permission_id' => 1], // User Management - Create
            ['module_id' => 1, 'permission_id' => 2], // User Management - Read
            ['module_id' => 1, 'permission_id' => 3], // User Management - Update
            ['module_id' => 1, 'permission_id' => 4], // User Management - Delete
            ['module_id' => 2, 'permission_id' => 2], // Inventory - Read
            ['module_id' => 2, 'permission_id' => 3], // Inventory - Update
            ['module_id' => 3, 'permission_id' => 2], // Reports - Read
        ];

        DB::table('module_permissions')->insert($map);
    }
}
