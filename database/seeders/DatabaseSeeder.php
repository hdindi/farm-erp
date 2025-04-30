<?php

// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {

        // Temporarily disable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Your seeder code here

        $this->call([
            // --- Core Data Seeders ---
            RolesSeeder::class,          // Create Roles first
            PermissionsSeeder::class,    // Create Permissions
            ModulesSeeder::class,        // Create Modules
            ModulePermissionsSeeder::class, // Link Modules & Permissions
            RolePermissionsSeeder::class, // Assign base permissions to Roles

            // --- Application Data Seeders ---
            RoleUserSeeder::class,      // Assign Roles to Users (NEW)

            BirdTypesTableSeeder::class,
            BreedsTableSeeder::class,
            StagesTableSeeder::class,
            FeedTypesTableSeeder::class,
            PurchaseOrderStatusesTableSeeder::class,
            PurchaseUnitsTableSeeder::class,
            SalesUnitsTableSeeder::class,
            SuppliersTableSeeder::class,
            DrugsTableSeeder::class,
            DiseasesTableSeeder::class,
            VaccinesTableSeeder::class,
            SalesTeamsTableSeeder::class,
            UsersTableSeeder::class,
            BatchesTableSeeder::class,
            DailyRecordsTableSeeder::class,
            FeedRecordsTableSeeder::class,
            EggProductionTableSeeder::class,
            DiseaseManagementTableSeeder::class,
            VaccinationLogsTableSeeder::class,
            VaccineScheduleTableSeeder::class,
            SupplierFeedPricesTableSeeder::class,
            PurchaseOrdersTableSeeder::class,
            SalesPricesTableSeeder::class,
            SalesRecordsTableSeeder::class,
            AuditLogsTableSeeder::class,
            RolesSeeder::class,
            ModulesSeeder::class,
            PermissionsSeeder::class,
            ModulePermissionsSeeder::class,
            RolePermissionsSeeder::class,

        ]);


        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');


    }
}
