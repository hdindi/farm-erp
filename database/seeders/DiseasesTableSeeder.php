<?php

// database/seeders/DiseasesTableSeeder.php
namespace Database\Seeders;

use App\Models\Disease;
use Illuminate\Database\Seeder;

class DiseasesTableSeeder extends Seeder
{
    public function run()
    {
        $diseases = [
            [
                'name' => 'Newcastle Disease',
                'description' => 'Highly contagious viral disease affecting respiratory, nervous and digestive systems',
            ],
            [
                'name' => 'Avian Influenza',
                'description' => 'Highly pathogenic viral disease with high mortality',
            ],
            [
                'name' => 'Infectious Bronchitis',
                'description' => 'Viral respiratory disease affecting egg production',
            ],
            [
                'name' => 'Coccidiosis',
                'description' => 'Parasitic disease of intestinal tract',
            ],
            [
                'name' => 'Fowl Typhoid',
                'description' => 'Bacterial disease causing high mortality',
            ],
            [
                'name' => 'Gumboro Disease',
                'description' => 'Viral disease affecting immune system',
            ],
            [
                'name' => 'Marek\'s Disease',
                'description' => 'Viral disease causing tumors and paralysis',
            ],
        ];

        foreach ($diseases as $disease) {
            Disease::create($disease);
        }
    }
}
