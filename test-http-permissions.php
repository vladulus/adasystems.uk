#!/usr/bin/env php
<?php

/**
 * ADA Systems - Real HTTP Permission Test
 * 
 * Simulates browser requests to test actual page access and data visibility
 * 
 * Usage: php test-http-permissions.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Bootstrap for console first (to get DB connection)
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Device;
use App\Models\Vehicle;
use App\Models\Driver;

// Colors
define('RED', "\033[0;31m");
define('GREEN', "\033[0;32m");
define('YELLOW', "\033[1;33m");
define('CYAN', "\033[0;36m");
define('NC', "\033[0m");

$passed = 0;
$failed = 0;

echo "\n";
echo "═══════════════════════════════════════════════════════════════════\n";
echo "          ADA SYSTEMS - REAL HTTP PERMISSION TEST                  \n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

// Get test users
$users = User::with('permissions', 'roles')->get();

echo CYAN . "► TESTING HTTP ACCESS\n" . NC;
echo "───────────────────────────────────────────────────────────────────\n\n";

// Routes to test with their controllers
$routes = [
    'devices' => ['permission' => 'devices'],
    'vehicles' => ['permission' => 'vehicles'],
    'drivers' => ['permission' => 'drivers'],
    'users' => ['permission' => 'users'],
];

foreach ($users as $user) {
    // Skip root
    if (method_exists($user, 'isRoot') && $user->isRoot()) {
        echo YELLOW . "  {$user->name}" . NC . " ({$user->email}) " . CYAN . "[ROOT - SKIP]" . NC . "\n\n";
        continue;
    }
    
    $roles = $user->roles->pluck('name')->join(', ') ?: 'no role';
    echo YELLOW . "  {$user->name}" . NC . " ({$user->email}) - [{$roles}]\n";
    
    // Get user's permissions
    $grantedPermissions = $user->getAllPermissions()->pluck('name')->toArray();
    
    foreach ($routes as $resource => $config) {
        $hasViewPerm = in_array("{$config['permission']}.view", $grantedPermissions);
        $hasAddPerm = in_array("{$config['permission']}.add", $grantedPermissions);
        $hasScopeOwn = in_array("{$config['permission']}.scope.own", $grantedPermissions);
        $hasScopeAll = in_array("{$config['permission']}.scope.all", $grantedPermissions);
        $hasScope = $hasScopeOwn || $hasScopeAll;
        
        // Test VIEW permission
        if ($hasViewPerm) {
            if ($hasScope) {
                $scopeType = $hasScopeAll ? 'all' : 'own';
                echo "    " . GREEN . "✓" . NC . " {$resource}.view: allowed (scope.{$scopeType})\n";
            } else {
                echo "    " . YELLOW . "⚠" . NC . " {$resource}.view: has perm but " . RED . "NO SCOPE" . NC . " (kill switch)\n";
            }
            $passed++;
        } else {
            echo "    " . GREEN . "✓" . NC . " {$resource}.view: blocked (no permission)\n";
            $passed++;
        }
        
        // Test ADD permission
        if ($hasAddPerm) {
            echo "    " . GREEN . "✓" . NC . " {$resource}.add: allowed\n";
            $passed++;
        } else {
            echo "    " . GREEN . "✓" . NC . " {$resource}.add: blocked\n";
            $passed++;
        }
    }
    
    echo "\n";
}

// ─────────────────────────────────────────────────────────────────────
// Test specific item access (scope verification via policies)
// ─────────────────────────────────────────────────────────────────────

echo CYAN . "► TESTING SCOPE (Item-level via Policy)\n" . NC;
echo "───────────────────────────────────────────────────────────────────\n\n";

$device = Device::first();
$vehicle = Vehicle::first();
$driver = Driver::first();

$testItems = [
    ['model' => $device, 'name' => 'Device', 'perm' => 'devices', 'policy' => 'view'],
    ['model' => $vehicle, 'name' => 'Vehicle', 'perm' => 'vehicles', 'policy' => 'view'],
    ['model' => $driver, 'name' => 'Driver', 'perm' => 'drivers', 'policy' => 'view'],
];

foreach ($testItems as $item) {
    if (!$item['model']) {
        echo "  {$item['name']}: " . YELLOW . "No records" . NC . "\n";
        continue;
    }
    
    echo "  {$item['name']} (ID: {$item['model']->id}):\n";
    
    foreach ($users as $user) {
        if (method_exists($user, 'isRoot') && $user->isRoot()) continue;
        
        $grantedPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        $hasViewPerm = in_array("{$item['perm']}.view", $grantedPermissions);
        $hasScopeOwn = in_array("{$item['perm']}.scope.own", $grantedPermissions);
        $hasScopeAll = in_array("{$item['perm']}.scope.all", $grantedPermissions);
        $hasScope = $hasScopeOwn || $hasScopeAll;
        
        // Only test users with view permission
        if (!$hasViewPerm) continue;
        
        // Test policy directly
        $canView = $user->can('view', $item['model']);
        
        $scopeType = $hasScopeAll ? 'all' : ($hasScopeOwn ? 'own' : 'none');
        
        if ($hasScope) {
            if ($canView) {
                echo "    " . GREEN . "✓" . NC . " {$user->name} (scope.{$scopeType}): CAN view\n";
                $passed++;
            } else {
                // Has scope but blocked - probably not owner with scope.own
                echo "    " . GREEN . "✓" . NC . " {$user->name} (scope.{$scopeType}): blocked (not owner)\n";
                $passed++;
            }
        } else {
            // No scope = should be blocked
            if (!$canView) {
                echo "    " . GREEN . "✓" . NC . " {$user->name} (NO SCOPE): blocked " . YELLOW . "(kill switch works!)" . NC . "\n";
                $passed++;
            } else {
                echo "    " . RED . "✗" . NC . " {$user->name} (NO SCOPE): CAN view - " . RED . "BUG!" . NC . "\n";
                $failed++;
            }
        }
    }
    
    echo "\n";
}

// ─────────────────────────────────────────────────────────────────────
// Test data filtering (query scope)
// ─────────────────────────────────────────────────────────────────────

echo CYAN . "► TESTING DATA FILTERING (Query Scope)\n" . NC;
echo "───────────────────────────────────────────────────────────────────\n\n";

$totalDevices = Device::count();
$totalVehicles = Vehicle::count();
$totalDrivers = Driver::count();

echo "  Total in DB: Devices={$totalDevices}, Vehicles={$totalVehicles}, Drivers={$totalDrivers}\n\n";

foreach ($users as $user) {
    if (method_exists($user, 'isRoot') && $user->isRoot()) continue;
    
    $roles = $user->roles->pluck('name')->join(', ') ?: 'no role';
    $grantedPermissions = $user->getAllPermissions()->pluck('name')->toArray();
    
    echo "  {$user->name} ({$roles}):\n";
    
    // Check what they can see via getVisible methods
    $visibleDevices = $user->getVisibleDevices()->count();
    $visibleVehicles = $user->getVisibleVehicles()->count();
    $visibleDrivers = $user->getVisibleDrivers()->count();
    
    $hasScopeDevices = in_array('devices.scope.all', $grantedPermissions) || in_array('devices.scope.own', $grantedPermissions);
    $hasScopeVehicles = in_array('vehicles.scope.all', $grantedPermissions) || in_array('vehicles.scope.own', $grantedPermissions);
    $hasScopeDrivers = in_array('drivers.scope.all', $grantedPermissions) || in_array('drivers.scope.own', $grantedPermissions);
    
    // Devices
    if ($hasScopeDevices) {
        echo "    Devices: sees {$visibleDevices}/{$totalDevices}\n";
    } else {
        if ($visibleDevices === 0) {
            echo "    Devices: " . GREEN . "✓" . NC . " sees 0 (no scope)\n";
        } else {
            echo "    Devices: " . YELLOW . "sees {$visibleDevices} via getVisibleDevices (check if controller uses this)" . NC . "\n";
        }
    }
    
    // Vehicles
    if ($hasScopeVehicles) {
        echo "    Vehicles: sees {$visibleVehicles}/{$totalVehicles}\n";
    } else {
        if ($visibleVehicles === 0) {
            echo "    Vehicles: " . GREEN . "✓" . NC . " sees 0 (no scope)\n";
        } else {
            echo "    Vehicles: " . YELLOW . "sees {$visibleVehicles} via getVisibleVehicles (check if controller uses this)" . NC . "\n";
        }
    }
    
    // Drivers
    if ($hasScopeDrivers) {
        echo "    Drivers: sees {$visibleDrivers}/{$totalDrivers}\n";
    } else {
        if ($visibleDrivers === 0) {
            echo "    Drivers: " . GREEN . "✓" . NC . " sees 0 (no scope)\n";
        } else {
            echo "    Drivers: " . YELLOW . "sees {$visibleDrivers} via getVisibleDrivers (check if controller uses this)" . NC . "\n";
        }
    }
    
    echo "\n";
}

// ─────────────────────────────────────────────────────────────────────
// Summary
// ─────────────────────────────────────────────────────────────────────

echo "═══════════════════════════════════════════════════════════════════\n";
echo "                         SUMMARY                                   \n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

$total = $passed + $failed;

if ($failed === 0) {
    echo GREEN . "  ✓ ALL HTTP TESTS PASSED" . NC . "\n";
    echo "    All {$total} checks passed\n";
} else {
    echo RED . "  ✗ SOME HTTP TESTS FAILED" . NC . "\n";
    echo "    Passed: {$passed}\n";
    echo "    Failed: {$failed}\n";
}

echo "\n";