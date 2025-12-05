<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Driver;
use Illuminate\Auth\Access\HandlesAuthorization;

class DriverPolicy
{
    use HandlesAuthorization;

    /**
     * Super-admin are voie la orice pe drivers.
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->can('view_drivers');
    }

    public function view(User $user, Driver $driver): bool
    {
        return $user->hasRole('admin') || $user->can('view_drivers');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->can('edit_drivers');
    }

    public function update(User $user, Driver $driver): bool
    {
        return $user->hasRole('admin') || $user->can('edit_drivers');
    }

    public function delete(User $user, Driver $driver): bool
    {
        return $user->hasRole('admin') || $user->can('delete_drivers');
    }
}
