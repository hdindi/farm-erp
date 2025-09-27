<?php

return [
    'thresholds' => [
        'mortality_rate' => env('FARM_MORTALITY_THRESHOLD', 10.0),
        'overdue_grace_days' => env('FARM_OVERDUE_GRACE_DAYS', 7),
    ],

    'max_birds_per_house' => env('FARM_MAX_BIRDS_PER_HOUSE', 5000),

    'vaccination' => [
        'auto_create_schedules' => env('FARM_AUTO_VACCINATION_SCHEDULES', true),
        'default_tolerance_days' => env('FARM_VACCINATION_TOLERANCE_DAYS', 14),
    ],
];
