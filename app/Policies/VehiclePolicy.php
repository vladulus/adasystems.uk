<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Auth\Access\HandlesAuthorization;

class VehiclePolicy
{
    use HandlesAuthorization;

    /**
     * Super-admin are voie la orice pe vehicles.
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->can('view_vehicles');
    }

    public function view(User $user, Vehicle $vehicle): bool
    {
        return $user->hasRole('admin') || $user->can('view_vehicles');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->can('edit_vehicles');
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->hasRole('admin') || $user->can('edit_vehicles');
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $user->hasRole('admin') || $user->can('delete_vehicles');
    }
}
