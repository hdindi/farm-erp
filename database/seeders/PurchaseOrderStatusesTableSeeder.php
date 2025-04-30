<?php
// database/seeders/PurchaseOrderStatusesTableSeeder.php
namespace Database\Seeders;

use App\Models\PurchaseOrderStatus;
use Illuminate\Database\Seeder;

class PurchaseOrderStatusesTableSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            ['name' => 'Draft', 'description' => 'Order is being prepared'],
            ['name' => 'Submitted', 'description' => 'Order has been submitted for approval'],
            ['name' => 'Approved', 'description' => 'Order has been approved'],
            ['name' => 'Rejected', 'description' => 'Order has been rejected'],
            ['name' => 'Ordered', 'description' => 'Order has been placed with supplier'],
            ['name' => 'Partially Received', 'description' => 'Partial delivery received'],
            ['name' => 'Completed', 'description' => 'Order fully received'],
            ['name' => 'Cancelled', 'description' => 'Order has been cancelled'],
        ];

        foreach ($statuses as $status) {
            PurchaseOrderStatus::create($status);
        }
    }
}
