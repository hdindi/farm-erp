<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use App\Models\RolePermission;
use App\Models\ModulePermission;

class RolePermissionsSeeder extends Seeder
{
    public function run()
    {
        // Admin gets all permissions
        $module_permissions = DB::table('module_permissions')->pluck('id');

        foreach ($module_permissions as $module_permission_id) {
            DB::table('role_permissions')->insert([
                'role_id' => 1,
                'module_permission_id' => $module_permission_id,
            ]);
        }

        // Manager: Inventory and Reports only
        $manager_permissions = DB::table('module_permissions')
            ->whereIn('module_id', [2, 3])
            ->pluck('id');

        foreach ($manager_permissions as $module_permission_id) {
            DB::table('role_permissions')->insert([
                'role_id' => 2,
                'module_permission_id' => $module_permission_id,
            ]);
        }

        // User: Reports Read only
        $user_permissions = DB::table('module_permissions')
            ->where('module_id', 3)
            ->where('permission_id', 2)
            ->pluck('id');

        foreach ($user_permissions as $module_permission_id) {
            DB::table('role_permissions')->insert([
                'role_id' => 3,
                'module_permission_id' => $module_permission_id,
            ]);
        }
    }
}
