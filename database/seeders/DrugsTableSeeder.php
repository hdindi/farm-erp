<?php

// database/seeders/DrugsTableSeeder.php
namespace Database\Seeders;

use App\Models\Drug;
use Illuminate\Database\Seeder;

class DrugsTableSeeder extends Seeder
{
    public function run()
    {
        $drugs = [
            [
                'name' => 'Amoxicillin',
                'description' => 'Broad-spectrum antibiotic for bacterial infections',
            ],
            [
                'name' => 'Doxycycline',
                'description' => 'Antibiotic for respiratory infections',
            ],
            [
                'name' => 'Tylosin',
                'description' => 'For chronic respiratory disease',
            ],
            [
                'name' => 'Sulfadimidine',
                'description' => 'For coccidiosis treatment',
            ],
            [
                'name' => 'Ivermectin',
                'description' => 'For external and internal parasites',
            ],
            [
                'name' => 'Vitamin ADE',
                'description' => 'Vitamin supplement for general health',
            ],
        ];

        foreach ($drugs as $drug) {
            Drug::create($drug);
        }
    }
}
