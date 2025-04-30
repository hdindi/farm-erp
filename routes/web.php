<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\BirdTypeController;
use App\Http\Controllers\BreedController;
use App\Http\Controllers\DailyRecordController;
use App\Http\Controllers\FeedRecordController;
use App\Http\Controllers\EggProductionController;
use App\Http\Controllers\DiseaseManagementController;
use App\Http\Controllers\VaccinationLogController;
use App\Http\Controllers\VaccineScheduleController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\FeedTypeController;
use App\Http\Controllers\SalesRecordController;
use App\Http\Controllers\SalesPriceController;
use App\Http\Controllers\SalesTeamController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\DrugController;
use App\Http\Controllers\ModuleController; // Assuming web routes for now
use App\Http\Controllers\PermissionController; // Assuming web routes for now
use App\Http\Controllers\PurchaseOrderStatusController;
use App\Http\Controllers\PurchaseUnitController;
use App\Http\Controllers\RoleController; // Assuming web routes for now
use App\Http\Controllers\SalesUnitController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\SupplierFeedPriceController;
use App\Http\Controllers\VaccineController;
use App\Http\Controllers\ModulePermissionController; // Assuming web routes for now
use App\Http\Controllers\RolePermissionController; // Assuming web routes for now
use App\Http\Controllers\Auth\RegisterController; // Make sure path is correct
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController; // Make sure this import is correct



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


//Route::middleware('guest')->group(function () {
//    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
//    Route::post('register', [RegisterController::class, 'register']);
//    // Also apply to login routes if they are defined separately
//     Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
//     Route::post('login', [LoginController::class, 'login']);
//});


// Welcome Page
Route::get('/', function () {
    return view('welcome'); //
});

// Authentication Routes (Login, Register, Logout etc.)
Auth::routes(); // This handles routes defined in LoginController and RegisterController

// Registration Routes
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);


// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('home'); //
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard'); // Common alias

    // Batch Management
    Route::resource('batches', BatchController::class); //
    Route::resource('bird-types', BirdTypeController::class); //
    Route::resource('breeds', BreedController::class); //
    Route::resource('stages', StageController::class); //

    // Daily Operations
    Route::resource('daily-records', DailyRecordController::class); //
    Route::resource('feed-records', FeedRecordController::class); //
    Route::resource('egg-production', EggProductionController::class); //

    // Health Management
    Route::resource('disease-management', DiseaseManagementController::class); //
    Route::resource('diseases', DiseaseController::class); //
    Route::resource('drugs', DrugController::class); //
    Route::resource('vaccination-logs', VaccinationLogController::class); //
    Route::resource('vaccines', VaccineController::class); //
    Route::patch('/vaccine-schedule/{vaccineSchedule}/mark-administered', [VaccineScheduleController::class, 'markAdministered'])
        ->name('vaccine-schedule.mark-administered'); // Give it the required name
    Route::resource('vaccine-schedule', VaccineScheduleController::class); //
    Route::post('vaccine-schedule/{vaccineSchedule}/mark-administered', [VaccineScheduleController::class, 'markAdministered'])
        ->name('vaccine-schedule.markAdministered'); //
    Route::get('vaccine-schedule-timetable', [VaccineScheduleController::class, 'timetable'])->name('vaccine-schedule.timetable'); //
    Route::get('vaccine-schedule-calendar', [VaccineScheduleController::class, 'calendar'])->name('vaccine-schedule.calendar'); //
    Route::get('vaccine-schedule-dashboard', [VaccineScheduleController::class, 'dashboard'])->name('vaccine-schedule.dashboard'); //


    // Inventory & Purchasing
    Route::resource('purchase-orders', PurchaseOrderController::class); //
    Route::resource('suppliers', SupplierController::class); //
    Route::resource('feed-types', FeedTypeController::class); //
    Route::resource('supplier-feed-prices', SupplierFeedPriceController::class); //
    Route::resource('purchase-units', PurchaseUnitController::class); //
    Route::resource('purchase-order-statuses', PurchaseOrderStatusController::class); //


    // Sales
    Route::resource('sales-records', SalesRecordController::class); //
    Route::resource('sales-prices', SalesPriceController::class); //
    Route::resource('sales-teams', SalesTeamController::class); //
    Route::resource('sales-units', SalesUnitController::class); //


    // System & RBAC (Assuming Web UI for now)
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index'); //
    Route::get('audit-logs/filter', [AuditLogController::class, 'filter'])->name('audit-logs.filter'); //
    Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show'); //

    // --- RBAC Routes (Consider moving to API routes if interacting via JS) ---
    Route::resource('modules', ModuleController::class)->except(['create', 'edit']); //
    Route::get('modules/{module}/permissions', [ModuleController::class, 'permissions'])->name('modules.permissions'); //

    Route::resource('permissions', PermissionController::class)->except(['create', 'edit']); //

    Route::post('module-permissions', [ModulePermissionController::class, 'store'])->name('module-permissions.store'); //
    Route::get('module-permissions/module/{module}', [ModulePermissionController::class, 'modulePermissions'])->name('module-permissions.module'); //
    Route::get('module-permissions/permission/{permission}', [ModulePermissionController::class, 'permissionModules'])->name('module-permissions.permission'); //



    Route::resource('roles', RoleController::class)->except(['create', 'edit']); //
    Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions'); //
    Route::post('roles/{role}/sync-permissions', [RoleController::class, 'syncPermissions'])->name('roles.syncPermissions'); //
    Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.updatePermissions');

    // Permissions and Modules (Index only unless dynamic)
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/modules', [ModuleController::class, 'index'])->name('modules.index');

    // Resource routes for managing Module-Permission links
    Route::resource('module-permissions', ModulePermissionController::class)->except(['show']); // Exclude show if index is enough


    // --- End RBAC Routes ---


    Route::get('/reports/batch-performance', [ReportController::class, 'batchPerformance'])->name('reports.batch-performance');
    // Add other report routes here...
    // Route::get('/reports/egg-production', [ReportController::class, 'eggProductionReport'])->name('reports.egg-production');
    // Existing report routes...
    Route::prefix('reports')->name('reports.')->middleware('auth')->group(function () {
        Route::get('/farm-kpis', [ReportController::class, 'farmKpis'])->name('farm-kpis');
        Route::get('/batch-summary', [ReportController::class, 'batchSummary'])->name('batch-summary');
        Route::get('/daily-egg-summary', [ReportController::class, 'dailyEggSummary'])->name('daily-egg-summary');
        Route::get('/sales-by-salesperson', [ReportController::class, 'salesBySalesperson'])->name('sales-by-salesperson');
        Route::get('/batch-performance', [ReportController::class, 'batchPerformance'])->name('batch-performance'); // Add this if not already present

        // Add routes for the new reports
        Route::get('/feed-consumption', [ReportController::class, 'feedConsumptionReport'])->name('feed-consumption');
        Route::get('/disease-management', [ReportController::class, 'diseaseManagementReport'])->name('disease-management');
        Route::get('/vaccination', [ReportController::class, 'vaccinationReport'])->name('vaccination');
    });

    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class)->middleware('auth'); // Add middleware as needed


// If you added the custom route for permissions update, make sure it's also there:
    Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.updatePermissions'); // Or PATCH

    Route::resource('/permissions', PermissionController::class);

    // --- Role & Permission Management Routes ---
    Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () { // Example prefix/middleware
        // Resource routes for Roles (excluding show maybe, handled by edit)
        Route::resource('roles', RoleController::class);
        // Custom route for updating permissions
        Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.updatePermissions');

        // Index routes for Permissions and Modules
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('/modules', [ModuleController::class, 'index'])->name('modules.index');

        // Add full resource routes if Permissions/Modules are dynamic
        // Route::resource('permissions', PermissionController::class);
        // Route::resource('modules', ModuleController::class);
    });
// --- End Role & Permission Routes ---

});
