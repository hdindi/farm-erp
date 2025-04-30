<?php
// database/seeders/SuppliersTableSeeder.php
namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SuppliersTableSeeder extends Seeder
{
    public function run()
    {
        $suppliers = [
            [
                'name' => 'AgroFeeds Ltd',
                'contact_person' => 'John Kamau',
                'phone_no' => '+254712345678',
                'email' => 'sales@agrofeeds.co.ke',
                'address' => 'Industrial Area, Nairobi',
                'description' => 'Leading animal feed manufacturer',
            ],
            [
                'name' => 'Unga Farm Care',
                'contact_person' => 'Sarah Wanjiku',
                'phone_no' => '+254723456789',
                'email' => 'info@ungafarmcare.com',
                'address' => 'Eldoret, Kenya',
                'description' => 'Quality poultry feeds and supplements',
            ],
            [
                'name' => 'KenChick Breeders',
                'contact_person' => 'David Ochieng',
                'phone_no' => '+254734567890',
                'email' => 'breeders@kenchick.com',
                'address' => 'Thika, Kenya',
                'description' => 'Day-old chicks supplier',
            ],
            [
                'name' => 'VetCare Pharmaceuticals',
                'contact_person' => 'Dr. James Mwangi',
                'phone_no' => '+254745678901',
                'email' => 'pharma@vetcare.co.ke',
                'address' => 'Westlands, Nairobi',
                'description' => 'Veterinary drugs and vaccines',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
