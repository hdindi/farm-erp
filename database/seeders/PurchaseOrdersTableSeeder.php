<?php

namespace Database\Seeders;

use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PurchaseOrdersTableSeeder extends Seeder
{
    public function run()
    {
        PurchaseOrder::create([
            'purchase_order_no' => 'PO-2023-001',
            'supplier_id' => 1, // AgroFeed Ltd
            'feed_type_id' => 1, // Starter Mash
            'purchase_unit_id' => 2, // Bag
            'quantity' => 20,
            'unit_price' => 1250,
            'total_price' => 25000,
            'order_date' => Carbon::now()->subDays(15),
            'expected_delivery_date' => Carbon::now()->subDays(10),
            'actual_delivery_date' => Carbon::now()->subDays(10),
            'purchase_order_status_id' => 5, // Delivered
            'notes' => 'Regular monthly order',
        ]);

        PurchaseOrder::create([
            'purchase_order_no' => 'PO-2023-002',
            'supplier_id' => 1, // AgroFeed Ltd
            'feed_type_id' => 4, // Layer Mash
            'purchase_unit_id' => 2, // Bag
            'quantity' => 30,
            'unit_price' => 1300,
            'total_price' => 39000,
            'order_date' => Carbon::now()->subDays(5),
            'expected_delivery_date' => Carbon::now()->addDays(2),
            'actual_delivery_date' => null,
            'purchase_order_status_id' => 3, // Approved
            'notes' => 'Additional order for increased flock',
        ]);

        PurchaseOrder::create([
            'purchase_order_no' => 'PO-2023-003',
            'supplier_id' => 2, // VetMed Supplies
            'feed_type_id' => 1, // Starter Mash
            'purchase_unit_id' => 2, // Bag
            'quantity' => 10,
            'unit_price' => 1275,
            'total_price' => 12750,
            'order_date' => Carbon::now()->subDays(8),
            'expected_delivery_date' => Carbon::now()->subDays(3),
            'actual_delivery_date' => Carbon::now()->subDays(3),
            'purchase_order_status_id' => 5, // Delivered
            'notes' => 'Trial order of premium feed',
        ]);
    }
}
