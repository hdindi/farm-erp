<?php

namespace Database\Seeders;

use App\Models\SupplierFeedPrice;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SupplierFeedPricesTableSeeder extends Seeder
{
    public function run()
    {
        $prices = [
            [
                'supplier_id' => 1, // AgroFeed Ltd
                'feed_type_id' => 1, // Starter Mash
                'purchase_unit_id' => 2, // Bag
                'supplier_price' => 1250,
                'effective_date' => Carbon::now()->subDays(30),
                'description' => 'Standard 25kg bag price',
            ],
            [
                'supplier_id' => 1, // AgroFeed Ltd
                'feed_type_id' => 2, // Grower Mash
                'purchase_unit_id' => 2, // Bag
                'supplier_price' => 1200,
                'effective_date' => Carbon::now()->subDays(30),
                'description' => 'Standard 25kg bag price',
            ],
            [
                'supplier_id' => 1, // AgroFeed Ltd
                'feed_type_id' => 4, // Layer Mash
                'purchase_unit_id' => 2, // Bag
                'supplier_price' => 1300,
                'effective_date' => Carbon::now()->subDays(30),
                'description' => 'Standard 25kg bag price',
            ],
            [
                'supplier_id' => 2, // VetMed Supplies
                'feed_type_id' => 1, // Starter Mash
                'purchase_unit_id' => 2, // Bag
                'supplier_price' => 1275,
                'effective_date' => Carbon::now()->subDays(30),
                'description' => 'Premium starter feed',
            ],
        ];

        foreach ($prices as $price) {
            SupplierFeedPrice::create($price);
        }
    }
}
