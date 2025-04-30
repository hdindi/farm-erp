<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Import User model
use App\Models\Role; // Import Role model
use Illuminate\Support\Facades\DB; // Import DB facade for direct manipulation if needed
use Illuminate\Support\Facades\Log; // Optional: For logging errors

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- Clear existing assignments before seeding ---
        // It's often good practice to clear the pivot table first
        // to avoid duplicate entries if the seeder is run multiple times.
        DB::table('role_user')->truncate(); // Assumes pivot table name is 'role_user'

        // --- Find Roles ---
        // Find roles by name. Handle cases where roles might not exist.
        $adminRole = Role::where('name', 'Admin')->first();
        $managerRole = Role::where('name', 'Manager')->first();
        $userRole = Role::where('name', 'User')->first(); // Assuming a general 'User' role exists

        // --- Find Users ---
        // Find users by email or another unique identifier. Handle missing users.
        $adminUser = User::where('email', 'admin@farm.com')->first();
        $managerUser = User::where('email', 'manager@farm.com')->first();
        $vetUser = User::where('email', 'vet@farm.com')->first(); // Example Vet
        $salesUser = User::where('email', 'sales@farm.com')->first(); // Example Sales
        $harrisUser = User::where('email', 'harrisdindisamuel@gmail.com')->first(); // Your specific user

        // --- Assign Roles ---
        // Use the attach() method on the roles() relationship.
        // Check if both the user and role were found before attaching.

        if ($adminUser && $adminRole) {
            $adminUser->roles()->attach($adminRole->id);
            $this->command->info("Attached 'Admin' role to {$adminUser->name}");
        } else {
            Log::warning("RoleUserSeeder: Could not find Admin user or Admin role.");
            $this->command->warn("RoleUserSeeder: Could not find Admin user or Admin role.");
        }

        if ($managerUser && $managerRole) {
            $managerUser->roles()->attach($managerRole->id);
            $this->command->info("Attached 'Manager' role to {$managerUser->name}");
        } else {
            Log::warning("RoleUserSeeder: Could not find Manager user or Manager role.");
            $this->command->warn("RoleUserSeeder: Could not find Manager user or Manager role.");
        }

        // Assigning 'User' role as an example to others
        if ($vetUser && $userRole) {
            $vetUser->roles()->attach($userRole->id);
            $this->command->info("Attached 'User' role to {$vetUser->name}");
        } else {
            Log::warning("RoleUserSeeder: Could not find Vet user or User role.");
            $this->command->warn("RoleUserSeeder: Could not find Vet user or User role.");
        }

        if ($salesUser && $userRole) {
            $salesUser->roles()->attach($userRole->id);
            $this->command->info("Attached 'User' role to {$salesUser->name}");
        } else {
            Log::warning("RoleUserSeeder: Could not find Sales user or User role.");
            $this->command->warn("RoleUserSeeder: Could not find Sales user or User role.");
        }

        if ($harrisUser && $adminRole) { // Assigning Admin role to Harris Dindi as an example
            $harrisUser->roles()->attach($adminRole->id);
            $this->command->info("Attached 'Admin' role to {$harrisUser->name}");
        } else {
            Log::warning("RoleUserSeeder: Could not find Harris Dindi user or required role.");
            $this->command->warn("RoleUserSeeder: Could not find Harris Dindi user or required role.");
        }

        // You can add more assignments here for other users/roles
    }
}
