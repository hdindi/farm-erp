# Farm ERP Implementation Guide

## Overview

This guide provides step-by-step instructions for implementing the Clean Architecture improvements in the Farm ERP system. All the foundational code has been created and documented.

## 🚀 Quick Start

### 1. Install Dependencies and Run Existing Commands
```bash
# Install PHP dependencies
composer install

# Install Node dependencies  
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate:fresh --seed

# Build assets
npm run dev
```

### 2. Register New Service Providers

Add to `config/app.php` in the providers array:
```php
// Add these to the providers array
App\Infrastructure\Providers\RepositoryServiceProvider::class,
App\Application\Providers\EventServiceProvider::class,
```

### 3. Create Missing Service Provider Files

Run these commands to create the remaining infrastructure:

```bash
# Create the Repository Service Provider
php artisan make:provider Infrastructure/RepositoryServiceProvider

# Create the enhanced Event Service Provider  
php artisan make:provider Application/EventServiceProvider
```

### 4. Update the Repository Service Provider

Edit `app/Infrastructure/Providers/RepositoryServiceProvider.php`:

```php
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
```

### 5. Update Event Service Provider

Edit `app/Application/Providers/EventServiceProvider.php`:

```php
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
```

### 6. Update Your Existing Controllers

Replace your existing `BatchController` with the new architecture:

```php
<?php

namespace App\Http\Controllers;

use App\Application\DTOs\BatchDTO;
use App\Application\Services\Contracts\BatchServiceInterface;
use App\Domain\Exceptions\BatchNotFoundException;
use App\Domain\Exceptions\ValidationException;
use App\Presentation\Http\Requests\StoreBatchRequest;
use App\Presentation\Http\Requests\UpdateBatchRequest;
use App\Presentation\Http\Resources\BatchResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function __construct(
        private readonly BatchServiceInterface $batchService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'bird_type_id', 'breed_id', 'search']);
        $batches = $this->batchService->getPaginatedBatches($filters);
        
        if ($request->expectsJson()) {
            return BatchResource::collection($batches);
        }
        
        return view('batches.index', compact('batches'));
    }

    public function store(StoreBatchRequest $request)
    {
        try {
            $batchDTO = BatchDTO::fromArray($request->getValidatedData());
            $batch = $this->batchService->createBatch($batchDTO);
            
            if ($request->expectsJson()) {
                return new BatchResource($batch);
            }
            
            return redirect()->route('batches.show', $batch)
                ->with('success', 'Batch created successfully.');
                
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json($e->toArray(), $e->getHttpStatusCode());
            }
            
            return redirect()->back()
                ->withErrors($e->getErrors())
                ->withInput();
        }
    }

    public function show(int $id, Request $request)
    {
        try {
            $batch = $this->batchService->getBatch($id);
            
            if ($request->expectsJson()) {
                return new BatchResource($batch);
            }
            
            return view('batches.show', compact('batch'));
            
        } catch (BatchNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json($e->toArray(), $e->getHttpStatusCode());
            }
            
            abort(404, $e->getUserMessage());
        }
    }

    public function update(UpdateBatchRequest $request, int $id)
    {
        try {
            $batchDTO = BatchDTO::fromArray($request->getValidatedData());
            $batch = $this->batchService->updateBatch($id, $batchDTO);
            
            if ($request->expectsJson()) {
                return new BatchResource($batch);
            }
            
            return redirect()->route('batches.show', $batch)
                ->with('success', 'Batch updated successfully.');
                
        } catch (BatchNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json($e->toArray(), $e->getHttpStatusCode());
            }
            
            abort(404, $e->getUserMessage());
        }
    }
}
```

### 7. Update Your Routes

Update `routes/api.php`:
```php
<?php

use App\Http\Controllers\BatchController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('batches', BatchController::class);
    Route::get('batches/{batch}/performance', [BatchController::class, 'performance']);
});
```

### 8. Run Queue Workers

Since we're using queued event listeners:

```bash
# Start queue worker
php artisan queue:work --queue=batch-processing,default

# Or use Supervisor in production
```

### 9. Configure Caching

Add to your `.env`:
```env
CACHE_DRIVER=redis  # or file for development
QUEUE_CONNECTION=redis  # or database
```

## 🧪 Testing the Implementation

### 1. Test Batch Creation
```bash
curl -X POST http://your-app.test/api/batches \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "batch_code": "BATCH001",
    "bird_type_id": 1,
    "breed_id": 1,
    "initial_population": 1000,
    "date_received": "2024-01-15",
    "bird_age_days": 1
  }'
```

### 2. Verify Event Processing
Check logs to ensure the `CreateVaccinationSchedule` listener executed:
```bash
tail -f storage/logs/laravel.log | grep "vaccination schedule"
```

### 3. Test Validation
```bash
curl -X POST http://your-app.test/api/batches \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "batch_code": "",
    "initial_population": -1
  }'
```

Should return validation errors with detailed messages.

## 🔧 Configuration Options

### Farm-Specific Settings

Add to `config/farm.php`:
```php
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
```

### Queue Configuration

For production, use Supervisor (`/etc/supervisor/conf.d/laravel-worker.conf`):
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/app/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopaslogsignal=TERM
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/your/app/storage/logs/worker.log
stopwaitsecs=3600
```

## 📝 Next Steps

### 1. Add More Domain Services
- `DailyRecordService`
- `VaccinationService` 
- `HealthManagementService`
- `ReportingService`

### 2. Implement Additional Events
- `DailyRecordCreated`
- `HighMortalityDetected`
- `VaccinationCompleted`
- `BatchRequiresAttention`

### 3. Add Comprehensive Testing
```bash
# Create test files
php artisan make:test BatchServiceTest --unit
php artisan make:test BatchCreationTest --feature
php artisan make:test BatchApiTest --feature

# Run tests
php artisan test
```

### 4. Add API Documentation
Consider using Laravel API Documentation tools:
- Laravel Passport/Sanctum documentation
- OpenAPI/Swagger documentation
- Postman collections

### 5. Performance Monitoring
- Install Laravel Telescope for development
- Add APM monitoring (New Relic, DataDog)
- Implement custom metrics collection

## 🔍 Troubleshooting

### Common Issues

1. **Service Provider Not Registered**
   - Ensure providers are added to `config/app.php`
   - Run `php artisan config:clear`

2. **Events Not Firing**
   - Check queue workers are running
   - Verify event registration in EventServiceProvider
   - Check logs for errors

3. **Validation Errors**
   - Ensure form request classes are being used
   - Check validation rules match your data
   - Verify custom validation logic

4. **Database Errors**
   - Run migrations: `php artisan migrate`
   - Check foreign key constraints
   - Verify seeded data exists

### Debug Commands
```bash
# Check registered services
php artisan tinker
app()->make(App\Application\Services\Contracts\BatchServiceInterface::class)

# Test events manually
php artisan tinker
event(new App\Application\Events\BatchCreated($batch))

# Check queue jobs
php artisan queue:failed
php artisan queue:retry all
```

## 📊 Monitoring & Metrics

### Key Metrics to Track
- Batch creation success rate
- Vaccination schedule completion
- Average response times
- Error rates by endpoint
- Queue processing times

### Logging Best Practices
- Use structured logging with context
- Set appropriate log levels
- Monitor error patterns
- Implement alerting for critical errors

## 🎯 Success Criteria

Your implementation is successful when:

✅ Batch creation triggers vaccination schedule creation automatically  
✅ API responses are consistent and well-structured  
✅ Validation provides clear, actionable error messages  
✅ Events process asynchronously without blocking requests  
✅ Error handling provides appropriate HTTP status codes  
✅ Performance metrics show improved response times  
✅ Code is well-documented and maintainable  

## 🤝 Getting Help

If you encounter issues:
1. Check the logs: `storage/logs/laravel.log`
2. Review the technical documentation: `docs/TECHNICAL-DOCUMENTATION.md`  
3. Test individual components in isolation
4. Use Laravel Tinker to debug services directly

The architecture is now significantly improved with:
- **Clean separation of concerns**
- **Comprehensive validation**
- **Event-driven automation**  
- **Consistent API responses**
- **Proper error handling**
- **Scalable repository pattern**

Continue building on this foundation to create additional features and domains!