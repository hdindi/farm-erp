<?php
// database/seeders/BreedsTableSeeder.php
namespace Database\Seeders;

use App\Models\Breed;
use Illuminate\Database\Seeder;

class BreedsTableSeeder extends Seeder
{
    public function run()
    {
        $breeds = [
            [
                'name' => 'Cobb 500',
                'description' => 'Fast-growing broiler breed with excellent feed conversion',
            ],
            [
                'name' => 'Ross 308',
                'description' => 'Popular broiler breed known for high meat yield',
            ],
            [
                'name' => 'Lohmann Brown',
                'description' => 'Highly productive layer breed with brown eggs',
            ],
            [
                'name' => 'Hy-Line',
                'description' => 'Commercial layer breed with excellent egg production',
            ],
            [
                'name' => 'Sasso',
                'description' => 'Dual-purpose breed with good meat and egg production',
            ],
            [
                'name' => 'Kuroiler',
                'description' => 'Dual-purpose breed suitable for small-scale farming',
            ],
        ];

        foreach ($breeds as $breed) {
            Breed::create($breed);
        }
    }
}
