<?php

// database/seeders/AuditLogsTableSeeder.php
namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AuditLogsTableSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $actions = ['INSERT', 'UPDATE', 'DELETE', 'LOGIN', 'LOGOUT', 'SYSTEM'];
        $tables = [
            'batches', 'daily_records', 'feed_records', 'egg_production',
            'disease_management', 'vaccination_logs', 'purchase_orders',
            'sales_records', 'users'
        ];

        for ($i = 1; $i <= 200; $i++) {
            $user = $users->random();
            $action = $actions[array_rand($actions)];
            $table = $tables[array_rand($tables)];

            AuditLog::create([
                'event_time' => Carbon::now()->subMinutes(rand(1, 10080)), // Up to 1 week ago
                'user_id' => $user->id,
                'table_name' => $table,
                'record_id' => rand(1, 100),
                'action' => $action,
                'old_values' => $action === 'UPDATE' ? json_encode(['field' => 'old_value']) : null,
                'new_values' => json_encode(['field' => 'new_value']),
                'ip_address' => '192.168.' . rand(1, 255) . '.' . rand(1, 255),
                'user_agent' => $this->getRandomUserAgent(),
            ]);
        }
    }

    protected function getRandomUserAgent()
    {
        $agents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
        ];

        return $agents[array_rand($agents)];
    }
}
