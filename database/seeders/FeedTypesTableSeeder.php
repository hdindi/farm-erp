<?php
// database/seeders/FeedTypesTableSeeder.php
namespace Database\Seeders;

use App\Models\FeedType;
use Illuminate\Database\Seeder;

class FeedTypesTableSeeder extends Seeder
{
    public function run()
    {
        $feedTypes = [
            [
                'name' => 'Starter Mash',
                'description' => 'High protein feed for day-old to 10-day-old chicks',
            ],
            [
                'name' => 'Grower Mash',
                'description' => 'Balanced feed for growing birds (11-24 days)',
            ],
            [
                'name' => 'Finisher Mash',
                'description' => 'Feed for birds approaching market weight',
            ],
            [
                'name' => 'Layer Mash',
                'description' => 'Calcium-rich feed for egg-laying hens',
            ],
            [
                'name' => 'Broiler Pre-starter',
                'description' => 'Specialized feed for first 5 days of broilers',
            ],
            [
                'name' => 'Medicated Feed',
                'description' => 'Feed containing coccidiostats and other medications',
            ],
        ];

        foreach ($feedTypes as $feedType) {
            FeedType::create($feedType);
        }
    }
}
