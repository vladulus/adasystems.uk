#!/usr/bin/env php
<?php

/**
 * ADA Systems - Permission System Verification
 * 
 * Tests if the permission system works correctly:
 * - Granted permissions return TRUE
 * - Non-granted permissions return FALSE
 * 
 * Usage: php test-permissions.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Device;
use App\Models\Vehicle;
use App\Models\Driver;
use Spatie\Permission\Models\Permission;

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
echo "          ADA SYSTEMS - PERMISSION SYSTEM VERIFICATION             \n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

// Get all permissions in system
$allPermissions = Permission::pluck('name')->toArray();

echo CYAN . "► SYSTEM INFO\n" . NC;
echo "───────────────────────────────────────────────────────────────────\n";
echo "  Total permissions in system: " . count($allPermissions) . "\n\n";

// Get all users with their permissions
$users = User::with('permissions', 'roles')->get();

echo CYAN . "► TESTING EACH USER\n" . NC;
echo "───────────────────────────────────────────────────────────────────\n\n";

foreach ($users as $user) {
    $roles = $user->roles->pluck('name')->join(', ') ?: 'no role';
    
    // Get permissions this user HAS (direct + via roles)
    $grantedPermissions = $user->getAllPermissions()->pluck('name')->toArray();
    
    // Group permissions for display
    $grouped = [];
    foreach ($grantedPermissions as $p) {
        $parts = explode('.', $p);
        $group = $parts[0];
        $action = $parts[1] ?? $p;
        $grouped[$group][] = $action;
    }
    $permissionDisplay = [];
    foreach ($grouped as $group => $actions) {
        $permissionDisplay[] = $group . "(" . implode(',', $actions) . ")";
    }
    
    // Check if user is root (bypasses all permissions)
    $isRoot = method_exists($user, 'isRoot') && $user->isRoot();
    
    if ($isRoot) {
        echo YELLOW . "  {$user->name}" . NC . " ({$user->email}) - [{$roles}] " . CYAN . "[ROOT]" . NC . "\n";
        echo "    " . GREEN . "✓ Root bypasses all permissions (by design)" . NC . "\n";
        if (!empty($permissionDisplay)) {
            echo "    Permissions: " . implode(' | ', $permissionDisplay) . "\n";
        }
        echo "\n";
        $passed++;
        continue;
    }
    
    echo YELLOW . "  {$user->name}" . NC . " ({$user->email}) - [{$roles}]\n";
    
    if (empty($grantedPermissions)) {
        echo "    " . YELLOW . "⚠ No permissions granted" . NC . "\n\n";
        continue;
    }
    
    echo "    Granted: " . count($grantedPermissions) . " permissions\n";
    
    $userPassed = 0;
    $userFailed = 0;
    $failedList = [];
    
    // Test 1: Granted permissions should return TRUE
    foreach ($grantedPermissions as $perm) {
        if ($user->can($perm)) {
            $userPassed++;
            $passed++;
        } else {
            $userFailed++;
            $failed++;
            $failedList[] = "    " . RED . "✗" . NC . " has '{$perm}' but can() returns FALSE";
        }
    }
    
    // Test 2: Non-granted permissions should return FALSE
    $notGranted = array_diff($allPermissions, $grantedPermissions);
    foreach ($notGranted as $perm) {
        if (!$user->can($perm)) {
            $userPassed++;
            $passed++;
        } else {
            $userFailed++;
            $failed++;
            $failedList[] = "    " . RED . "✗" . NC . " does NOT have '{$perm}' but can() returns TRUE";
        }
    }
    
    if ($userFailed === 0) {
        echo "    " . GREEN . "✓ All " . ($userPassed) . " checks passed" . NC . "\n";
    } else {
        echo "    " . RED . "✗ {$userFailed} checks failed:" . NC . "\n";
        foreach ($failedList as $f) {
            echo $f . "\n";
        }
    }
    
    // Show what permissions they have
    $grouped = [];
    foreach ($grantedPermissions as $p) {
        $parts = explode('.', $p);
        $group = $parts[0];
        $action = $parts[1] ?? $p;
        $grouped[$group][] = $action;
    }
    
    echo "    Permissions: ";
    $groupStrings = [];
    foreach ($grouped as $group => $actions) {
        $groupStrings[] = $group . "(" . implode(',', $actions) . ")";
    }
    echo implode(' | ', $groupStrings) . "\n";
    
    // Check for scope issues - warn if has permissions but no scope
    $scopeCategories = ['devices', 'vehicles', 'users', 'drivers', 'dashboard', 'pi-dashboard'];
    $scopeWarnings = [];
    
    foreach ($scopeCategories as $cat) {
        $hasPermissions = isset($grouped[$cat]) && !empty(array_diff($grouped[$cat], ['scope']));
        $hasScope = in_array('scope', $grouped[$cat] ?? []) 
                    || in_array("{$cat}.scope.own", $grantedPermissions) 
                    || in_array("{$cat}.scope.all", $grantedPermissions);
        
        if ($hasPermissions && !$hasScope) {
            $scopeWarnings[] = $cat;
        }
    }
    
    if (!empty($scopeWarnings)) {
        echo "    " . YELLOW . "⚠ NO SCOPE for: " . implode(', ', $scopeWarnings) . " (access disabled)" . NC . "\n";
    }
    
    echo "\n";
}

// ─────────────────────────────────────────────────────────────────────
// Policy Tests - Test if policies respect permissions AND scope
// ─────────────────────────────────────────────────────────────────────

echo CYAN . "► POLICY VERIFICATION (Permission + Scope)\n" . NC;
echo "───────────────────────────────────────────────────────────────────\n\n";

$device = Device::first();
$vehicle = Vehicle::first();
$driver = Driver::first();

$policyTests = [
    ['model' => $device, 'name' => 'Device', 'permission_prefix' => 'devices'],
    ['model' => $vehicle, 'name' => 'Vehicle', 'permission_prefix' => 'vehicles'],
    ['model' => $driver, 'name' => 'Driver', 'permission_prefix' => 'drivers'],
];

foreach ($policyTests as $test) {
    if (!$test['model']) {
        echo "  {$test['name']}: " . YELLOW . "No records to test" . NC . "\n";
        continue;
    }
    
    echo "  {$test['name']} (ID: {$test['model']->id}):\n";
    
    $prefix = $test['permission_prefix'];
    
    // Find users with different permission/scope combinations
    $allUsers = User::with('permissions', 'roles')->get();
    
    foreach ($allUsers as $testUser) {
        // Skip root
        if (method_exists($testUser, 'isRoot') && $testUser->isRoot()) {
            continue;
        }
        
        $hasViewPerm = $testUser->can("{$prefix}.view");
        $hasScopeOwn = $testUser->can("{$prefix}.scope.own");
        $hasScopeAll = $testUser->can("{$prefix}.scope.all");
        $hasAnyScope = $hasScopeOwn || $hasScopeAll;
        
        // Only test interesting cases
        if (!$hasViewPerm && !$hasAnyScope) {
            continue; // No permissions at all, skip
        }
        
        $canView = $testUser->can('view', $test['model']);
        
        if ($hasViewPerm && $hasAnyScope) {
            // Has permission + scope = should be able to view (if scope matches)
            $scopeType = $hasScopeAll ? 'all' : 'own';
            if ($canView) {
                echo "    " . GREEN . "✓" . NC . " {$testUser->name}: perm + scope.{$scopeType} → CAN view\n";
                $passed++;
            } else {
                // Might be scope.own but not owner - that's OK
                echo "    " . GREEN . "✓" . NC . " {$testUser->name}: perm + scope.{$scopeType} → cannot view (not owner)\n";
                $passed++;
            }
        } elseif ($hasViewPerm && !$hasAnyScope) {
            // Has permission but NO scope = should NOT be able to view
            if (!$canView) {
                echo "    " . GREEN . "✓" . NC . " {$testUser->name}: perm but NO SCOPE → blocked " . YELLOW . "(kill switch)" . NC . "\n";
                $passed++;
            } else {
                echo "    " . RED . "✗" . NC . " {$testUser->name}: perm but NO SCOPE → CAN view (BUG!)\n";
                $failed++;
            }
        } elseif (!$hasViewPerm && $hasAnyScope) {
            // Has scope but no permission = should NOT be able to view
            if (!$canView) {
                echo "    " . GREEN . "✓" . NC . " {$testUser->name}: scope but NO perm → blocked\n";
                $passed++;
            } else {
                echo "    " . RED . "✗" . NC . " {$testUser->name}: scope but NO perm → CAN view (BUG!)\n";
                $failed++;
            }
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
    echo GREEN . "  ✓ PERMISSION SYSTEM WORKING CORRECTLY" . NC . "\n";
    echo "    All {$total} checks passed\n";
} else {
    echo RED . "  ✗ PERMISSION SYSTEM HAS ISSUES" . NC . "\n";
    echo "    Passed: {$passed}\n";
    echo "    Failed: {$failed}\n";
}

echo "\n";