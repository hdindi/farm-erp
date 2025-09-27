<?php

namespace App\Application\Providers;

use App\Application\Events\BatchCreated;
use App\Application\Listeners\CreateVaccinationSchedule;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BatchCreated::class => [
            CreateVaccinationSchedule::class,
        ],

        // Add other events and listeners as needed
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
