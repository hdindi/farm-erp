<?php

// database/seeders/SalesTeamsTableSeeder.php
namespace Database\Seeders;

use App\Models\SalesTeam;
use Illuminate\Database\Seeder;

class SalesTeamsTableSeeder extends Seeder
{
    public function run()
    {
        $teamMembers = [
            [
                'name' => 'Peter Mwangi',
                'phone_no' => '+254712345671',
                'email' => 'peter@farm.com',
                'is_active' => true,
            ],
            [
                'name' => 'Jane Wambui',
                'phone_no' => '+254712345672',
                'email' => 'jane@farm.com',
                'is_active' => true,
            ],
            [
                'name' => 'David Omondi',
                'phone_no' => '+254712345673',
                'email' => 'david@farm.com',
                'is_active' => true,
            ],
            [
                'name' => 'Susan Akinyi',
                'phone_no' => '+254712345674',
                'email' => 'susan@farm.com',
                'is_active' => false,
            ],
        ];

        foreach ($teamMembers as $member) {
            SalesTeam::create($member);
        }
    }
}
