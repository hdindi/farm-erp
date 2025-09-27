# API Enhancement Starter Guide

## Quick Start Commands

### 1. Create API Controllers
```bash
# Core resource controllers
php artisan make:controller Api/DailyRecordController --api
php artisan make:controller Api/FeedRecordController --api
php artisan make:controller Api/EggProductionController --api
php artisan make:controller Api/DiseaseController --api
php artisan make:controller Api/UserController --api

# Analytics controllers
php artisan make:controller Api/DashboardController
php artisan make:controller Api/ReportController
php artisan make:controller Api/AnalyticsController
```

### 2. Create API Resources
```bash
# Data transformation resources
php artisan make:resource DailyRecordResource
php artisan make:resource DailyRecordCollection
php artisan make:resource FeedRecordResource
php artisan make:resource EggProductionResource
php artisan make:resource UserResource
```

### 3. Create Request Validators
```bash
# API request validation
php artisan make:request Api/StoreDailyRecordRequest
php artisan make:request Api/UpdateDailyRecordRequest
php artisan make:request Api/StoreFeedRecordRequest
php artisan make:request Api/LoginRequest
```

### 4. Example API Controller Implementation

```php
<?php
// app/Http/Controllers/Api/DailyRecordController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreDailyRecordRequest;
use App\Http\Resources\DailyRecordResource;
use App\Http\Resources\DailyRecordCollection;
use App\Models\DailyRecord;
use Illuminate\Http\Request;

class DailyRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = DailyRecord::with(['batch', 'stage']);
        
        // Add filtering
        if ($request->has('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }
        
        if ($request->has('date_from')) {
            $query->where('record_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->where('record_date', '<=', $request->date_to);
        }
        
        // Add pagination
        $records = $query->paginate($request->get('per_page', 15));
        
        return new DailyRecordCollection($records);
    }
    
    public function store(StoreDailyRecordRequest $request)
    {
        $record = DailyRecord::create($request->validated());
        $record->load(['batch', 'stage']);
        
        return new DailyRecordResource($record);
    }
    
    public function show(DailyRecord $dailyRecord)
    {
        $dailyRecord->load(['batch', 'stage', 'feedRecords', 'eggProduction']);
        return new DailyRecordResource($dailyRecord);
    }
    
    public function update(StoreDailyRecordRequest $request, DailyRecord $dailyRecord)
    {
        $dailyRecord->update($request->validated());
        $dailyRecord->load(['batch', 'stage']);
        
        return new DailyRecordResource($dailyRecord);
    }
    
    public function destroy(DailyRecord $dailyRecord)
    {
        $dailyRecord->delete();
        return response()->json(['message' => 'Record deleted successfully']);
    }
}
```

### 5. Update API Routes
```php
// routes/api.php - Add these routes

Route::middleware('auth:sanctum')->group(function () {
    // Existing batches routes...
    
    // Daily Records
    Route::apiResource('daily-records', Api\DailyRecordController::class);
    
    // Feed Records
    Route::apiResource('feed-records', Api\FeedRecordController::class);
    
    // Egg Production
    Route::apiResource('egg-production', Api\EggProductionController::class);
    
    // Health Management
    Route::apiResource('diseases', Api\DiseaseController::class);
    Route::apiResource('vaccination-logs', Api\VaccinationLogController::class);
    
    // Lookup Data (read-only)
    Route::get('bird-types', [Api\BirdTypeController::class, 'index']);
    Route::get('breeds', [Api\BreedController::class, 'index']);
    Route::get('stages', [Api\StageController::class, 'index']);
    Route::get('feed-types', [Api\FeedTypeController::class, 'index']);
    
    // Dashboard & Analytics
    Route::get('dashboard/kpis', [Api\DashboardController::class, 'kpis']);
    Route::get('dashboard/recent-activities', [Api\DashboardController::class, 'recentActivities']);
    Route::get('analytics/mortality-trends', [Api\AnalyticsController::class, 'mortalityTrends']);
    Route::get('analytics/production-forecast', [Api\AnalyticsController::class, 'productionForecast']);
    
    // Bulk Operations
    Route::post('daily-records/bulk', [Api\DailyRecordController::class, 'bulkStore']);
    Route::post('sync/upload', [Api\SyncController::class, 'bulkUpload']);
    Route::get('sync/changes/{timestamp}', [Api\SyncController::class, 'getChangesSince']);
});
```

## Sample API Responses

### Daily Records List
```json
{
  "data": [
    {
      "id": 1,
      "record_date": "2025-09-18",
      "alive_count": 950,
      "dead_count": 5,
      "culls_count": 2,
      "mortality_rate": 0.74,
      "batch": {
        "id": 1,
        "batch_code": "BATCH001",
        "status": "active"
      },
      "stage": {
        "id": 1,
        "name": "Starter",
        "min_age_days": 1,
        "max_age_days": 28
      }
    }
  ],
  "links": {
    "first": "http://api.farm-erp.com/api/daily-records?page=1",
    "last": "http://api.farm-erp.com/api/daily-records?page=10",
    "prev": null,
    "next": "http://api.farm-erp.com/api/daily-records?page=2"
  },
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 150
  }
}
```

### Dashboard KPIs
```json
{
  "data": {
    "total_active_batches": 5,
    "total_birds": 15000,
    "today_mortality": 25,
    "this_week_eggs": 8500,
    "monthly_revenue": 45000,
    "alerts": [
      {
        "type": "high_mortality",
        "batch_id": 3,
        "message": "Batch BATCH003 has high mortality rate (2.5%)"
      }
    ]
  }
}
```

## Authentication Setup

### 1. Create Authentication Controller
```bash
php artisan make:controller Api/AuthController
```

### 2. Implement Auth Methods
```php
<?php
// app/Http/Controllers/Api/AuthController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request)
    {
        return new UserResource($request->user());
    }
}
```

### 3. Add Auth Routes
```php
// routes/api.php
Route::post('auth/login', [Api\AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [Api\AuthController::class, 'logout']);
    Route::get('auth/user', [Api\AuthController::class, 'user']);
    
    // ... other protected routes
});
```

## Testing Your API

### 1. Using Postman/Insomnia

#### Login Request
```
POST http://your-domain.com/api/auth/login
Content-Type: application/json

{
  "email": "test@example.com",
  "password": "password123"
}
```

#### Authenticated Request
```
GET http://your-domain.com/api/daily-records
Authorization: Bearer your-token-here
```

### 2. Using curl
```bash
# Login
curl -X POST http://your-domain.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'

# Get daily records
curl -X GET http://your-domain.com/api/daily-records \
  -H "Authorization: Bearer your-token-here"
```

## Production Deployment

### 1. Environment Setup
```bash
# Production .env additions
API_RATE_LIMIT=60
SANCTUM_STATEFUL_DOMAINS=your-domain.com
SESSION_DOMAIN=.your-domain.com
```

### 2. CORS Configuration
```php
// config/cors.php
'paths' => ['api/*'],
'allowed_methods' => ['*'],
'allowed_origins' => ['your-desktop-app-domain', 'your-mobile-app-domain'],
'allowed_headers' => ['*'],
'supports_credentials' => true,
```

This starter guide gives you the foundation to rapidly expand your API for desktop and Android application integration!