<?php

// database/seeders/SalesRecordsTableSeeder.php
namespace Database\Seeders;

use App\Models\SalesPrice;
use App\Models\SalesRecord;
use App\Models\SalesTeam;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SalesRecordsTableSeeder extends Seeder
{
    public function run()
    {
        $salesPrices = SalesPrice::where('status', 'active')->get();
        $salesTeams = SalesTeam::where('is_active', true)->get();

        for ($i = 1; $i <= 50; $i++) {
            $price = $salesPrices->random();
            $quantity = $price->salesUnit->name === 'Kilogram'
                ? rand(1, 50) + (rand(0, 99) / 100)
                : rand(1, 20);

            $totalAmount = round($quantity * $price->price, 2);

            SalesRecord::create([
                'sales_person_id' => $salesTeams->random()->id,
                'customer_name' => $this->generateCustomerName(),
                'customer_phone' => $this->generatePhoneNumber(),
                'sales_price_id' => $price->id,
                'quantity' => $quantity,
                'total_amount' => $totalAmount,
                'amount_paid' => $totalAmount * (rand(80, 100) / 100),
                'sale_date' => Carbon::now()->subDays(rand(0, 30)),
                'notes' => $this->getSalesNotes($price),
            ]);
        }
    }

    protected function generateCustomerName()
    {
        $firstNames = ['John', 'Mary', 'James', 'Elizabeth', 'Robert', 'Patricia', 'Michael', 'Jennifer'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Miller', 'Davis', 'Garcia'];

        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }

    protected function generatePhoneNumber()
    {
        return '+2547' . rand(10, 99) . rand(100000, 999999);
    }

    protected function getSalesNotes($price)
    {
        $items = [
            'egg' => ['Fresh eggs', 'Grade A eggs', 'Farm fresh eggs'],
            'bird' => ['Healthy birds', 'Quality poultry', 'Farm raised birds'],
            'manure' => ['Organic fertilizer', 'Poultry manure', 'Compost material'],
        ];

        $itemType = $items[$price->item_type] ?? $items['egg'];
        return $itemType[array_rand($itemType)] . ' sold to customer';
    }
}
