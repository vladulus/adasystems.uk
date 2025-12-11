<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Formularul de editare permisiuni pentru un user.
     */
    public function edit(User $user)
    {
        $currentUser = auth()->user();

        if (! $this->canManagePermissions($currentUser, $user)) {
            return redirect()
                ->back()
                ->with('error', "You don't have access to this function.");
        }

        // Permisiunile pe care userul curent le POATE da target-ului
        $availablePermissions = $this->getAvailablePermissions($currentUser, $user);

        // Grupăm permisiunile pe categorii (devices, vehicles, users, drivers, dashboard, settings)
        $groupedPermissions = $this->groupPermissions($availablePermissions);

        // Permisiunile pe care le are deja userul țintă
        $userPermissions = $user->permissions->pluck('name')->toArray();

        return view('management.permissions.edit', [
            'user'               => $user,
            'groupedPermissions' => $groupedPermissions,
            'userPermissions'    => $userPermissions,
        ]);
    }

    /**
     * Salvăm permisiunile pentru user.
     */
    public function update(Request $request, User $user)
    {
        $currentUser = auth()->user();

        if (! $this->canManagePermissions($currentUser, $user)) {
            return redirect()
                ->back()
                ->with('error', "You don't have access to this function.");
        }

        $validated = $request->validate([
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $requestedPermissions = $validated['permissions'] ?? [];

        // Process scope fields (dashboard_scope, devices_scope, etc.)
        $scopeFields = ['dashboard', 'devices', 'vehicles', 'users', 'drivers'];
        foreach ($scopeFields as $field) {
            $scopeValue = $request->input($field . '_scope');
            if ($scopeValue && $scopeValue !== 'none') {
                $permName = $field . '.scope.' . $scopeValue;
                // Check if permission exists in DB
                if (Permission::where('name', $permName)->exists()) {
                    $requestedPermissions[] = $permName;
                }
            }
        }

        // Filtrăm permisiunile cerute prin ceea ce ARE VOIE să dea userul curent
        $available = $this->getAvailablePermissions($currentUser, $user)->pluck('name')->toArray();
        $allowedToAssign = array_values(array_intersect($requestedPermissions, $available));

        // Setăm permisiunile userului țintă
        $user->syncPermissions($allowedToAssign);

        return redirect()
            ->back()
            ->with('status', 'Permisiunile au fost actualizate.');
    }

    /**
     * Cine are voie să gestioneze permisiunile cui.
     */
    protected function canManagePermissions(User $currentUser, User $targetUser): bool
    {
        // ROOT poate edita pe toată lumea
        if ($currentUser->isRoot()) {
            return true;
        }

        // Nimeni nu poate edita root-ul
        if ($targetUser->isRoot()) {
            return false;
        }

        $currentRole = $currentUser->roles->first()?->name;
        $targetRole  = $targetUser->roles->first()?->name;

        // Superuser și user NU pot edita permisiuni NICIODATĂ
        if (in_array($currentRole, ['superuser', 'user'], true)) {
            return false;
        }

        // De aici încolo doar super-admin și admin
        // Trebuie să aibă permisiunea permissions.edit
        if (!$currentUser->can('permissions.edit')) {
            return false;
        }

        // SUPER-ADMIN
        if ($currentRole === 'super-admin') {
            // Nu poate edita alt super-admin
            if ($targetRole === 'super-admin') {
                return false;
            }
            return true; // poate edita admin, superuser, user
        }

        // ADMIN
        if ($currentRole === 'admin') {
            // Poate doar superuser + user
            return in_array($targetRole, ['superuser', 'user'], true);
        }

        return false;
    }

    /**
     * Permisiunile pe care userul curent le poate DELEGA target-ului.
     */
    protected function getAvailablePermissions(User $currentUser, User $targetUser)
    {
        $allPermissions = Permission::all();

        // ROOT → toate permisiunile
        if ($currentUser->isRoot()) {
            return $allPermissions;
        }

        $currentRole = $currentUser->roles->first()?->name;

        // SUPER-ADMIN → poate da orice permisiune
        if ($currentRole === 'super-admin') {
            return $allPermissions;
        }

        // Permisiunile proprii ale userului curent
        $own = $currentUser->getAllPermissions()->pluck('name')->toArray();

        // ADMIN → poate da doar ce are el însuși
        if ($currentRole === 'admin') {
            return $allPermissions->filter(function (Permission $perm) use ($own) {
                return in_array($perm->name, $own, true);
            });
        }

        // Altcineva nu poate delega nimic
        return collect();
    }

    /**
     * Grupăm permisiunile pe categorii pentru UI.
     */
    protected function groupPermissions($permissions): array
    {
        $grouped = [];

        foreach ($permissions as $permission) {
            $name = $permission->name;
            $parts = explode('.', $name, 2);
            $category = $parts[0] ?? 'other';
            $action   = $parts[1] ?? '';

            $grouped[$category][] = [
                'name'   => $name,
                'action' => $action,
            ];
        }

        ksort($grouped); // sortăm alfabetic categoriile

        return $grouped;
    }
}