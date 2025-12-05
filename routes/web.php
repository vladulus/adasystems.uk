<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\PermissionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Public marketing site + authentication + dashboard + management
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/home', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// =============================
// AUTHENTICATION ROUTES
// =============================

// Show login form
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login')
    ->middleware('guest');

// Login POST
Route::post('/login', [AuthController::class, 'authenticate'])
    ->name('login.authenticate')
    ->middleware('guest');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Password reset request page
Route::get('/password/request', [AuthController::class, 'showPasswordRequest'])
    ->name('password.request')
    ->middleware('guest');

// =============================
// PROTECTED DASHBOARD
// =============================
Route::middleware('auth')->group(function () {

    // HUB – pagina cu 2 carduri (Management + Pi)
    Route::get('/hub', function () {
        return view('app-hub');
    })->name('hub');

    // Management dashboard (pagina cu Devices / Vehicles / Users / Drivers)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // =============================
    // MANAGEMENT ROUTES
    // =============================
    Route::prefix('management')->name('management.')->group(function () {

        // Management home (tabs page) – doar super-admin / admin
        Route::get('/', [ManagementController::class, 'index'])
            ->name('index')
            ->middleware('role:super-admin|admin');

        // =============================
        // DEVICES
        // =============================
        Route::prefix('devices')->name('devices.')->group(function () {
            Route::get('/', [DeviceController::class, 'index'])
                ->name('index')
                ->middleware('permission:devices.view');

            Route::get('/create', [DeviceController::class, 'create'])
                ->name('create')
                ->middleware('permission:devices.add');

            Route::post('/', [DeviceController::class, 'store'])
                ->name('store')
                ->middleware('permission:devices.add');

            Route::get('/{device}', [DeviceController::class, 'show'])
                ->name('show')
                ->middleware('permission:devices.view');

            Route::get('/{device}/edit', [DeviceController::class, 'edit'])
                ->name('edit')
                ->middleware('permission:devices.edit');

            Route::put('/{device}', [DeviceController::class, 'update'])
                ->name('update')
                ->middleware('permission:devices.edit');

            Route::delete('/{device}', [DeviceController::class, 'destroy'])
                ->name('destroy')
                ->middleware('permission:devices.delete');
        });

        // =============================
        // VEHICLES
        // =============================
        Route::prefix('vehicles')->name('vehicles.')->group(function () {
            Route::get('/', [VehicleController::class, 'index'])
                ->name('index')
                ->middleware('permission:vehicles.view');

            Route::get('/create', [VehicleController::class, 'create'])
                ->name('create')
                ->middleware('permission:vehicles.add');

            Route::post('/', [VehicleController::class, 'store'])
                ->name('store')
                ->middleware('permission:vehicles.add');

            Route::get('/{vehicle}', [VehicleController::class, 'show'])
                ->name('show')
                ->middleware('permission:vehicles.view');

            Route::get('/{vehicle}/edit', [VehicleController::class, 'edit'])
                ->name('edit')
                ->middleware('permission:vehicles.edit');

            Route::put('/{vehicle}', [VehicleController::class, 'update'])
                ->name('update')
                ->middleware('permission:vehicles.edit');

            Route::delete('/{vehicle}', [VehicleController::class, 'destroy'])
                ->name('destroy')
                ->middleware('permission:vehicles.delete');
        });

        // =============================
        // USERS (super-admin, admin, superuser)
        // =============================
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])
                ->name('index')
                ->middleware('permission:users.view');

            Route::get('/create', [UserManagementController::class, 'create'])
                ->name('create')
                ->middleware('permission:users.add');

            Route::post('/', [UserManagementController::class, 'store'])
                ->name('store')
                ->middleware('permission:users.add');

            Route::get('/{user}', [UserManagementController::class, 'show'])
                ->name('show')
                ->middleware('permission:users.view');

            Route::get('/{user}/edit', [UserManagementController::class, 'edit'])
                ->name('edit')
                ->middleware('permission:users.edit');

            Route::put('/{user}', [UserManagementController::class, 'update'])
                ->name('update')
                ->middleware('permission:users.edit');

            Route::delete('/{user}', [UserManagementController::class, 'destroy'])
                ->name('destroy')
                ->middleware('permission:users.delete');
        });

        // =============================
        // DRIVERS
        // =============================
        Route::prefix('drivers')->name('drivers.')->group(function () {
            Route::get('/', [DriverController::class, 'index'])
                ->name('index')
                ->middleware('permission:drivers.view');

            Route::get('/create', [DriverController::class, 'create'])
                ->name('create')
                ->middleware('permission:drivers.add');

            Route::post('/', [DriverController::class, 'store'])
                ->name('store')
                ->middleware('permission:drivers.add');

            Route::get('/{driver}', [DriverController::class, 'show'])
                ->name('show')
                ->middleware('permission:drivers.view');

            Route::get('/{driver}/edit', [DriverController::class, 'edit'])
                ->name('edit')
                ->middleware('permission:drivers.edit');

            Route::put('/{driver}', [DriverController::class, 'update'])
                ->name('update')
                ->middleware('permission:drivers.edit');

            Route::delete('/{driver}', [DriverController::class, 'destroy'])
                ->name('destroy')
                ->middleware('permission:drivers.delete');
        });

        // =============================
        // PERMISSIONS (PERM button)
        // =============================
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/{user}/edit', [PermissionController::class, 'edit'])
                ->name('edit');   // verificarea fină se face în controller

            Route::put('/{user}', [PermissionController::class, 'update'])
                ->name('update');
        });
    });

    // =============================
    // PI DASHBOARD (view devices)
    // =============================
    Route::get('/pi-dashboard', function () {
        $user = auth()->user();
        $devices = $user->getVisibleDevices();
        return view('pi-dashboard', compact('devices'));
    })->name('pi.dashboard')
      ->middleware('permission:dashboard.access');
});

// =============================
// CONTACT PAGES
// =============================
Route::get('/contact', [ContactController::class, 'show'])
    ->name('contact.show');

Route::post('/contact', [ContactController::class, 'send'])
    ->name('contact.send');
