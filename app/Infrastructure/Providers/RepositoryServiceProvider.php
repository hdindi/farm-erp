<?php

namespace App\Infrastructure\Providers;

use App\Application\Services\BatchService;
use App\Application\Services\Contracts\BatchServiceInterface;
use App\Infrastructure\Repositories\Contracts\BatchRepositoryInterface;
use App\Infrastructure\Repositories\EloquentBatchRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(
            BatchRepositoryInterface::class,
            EloquentBatchRepository::class
        );

        // Service bindings
        $this->app->bind(
            BatchServiceInterface::class,
            BatchService::class
        );
    }

    public function boot(): void
    {
        //
    }
}
