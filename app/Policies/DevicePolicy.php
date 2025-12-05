<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Device;
use Illuminate\Auth\Access\HandlesAuthorization;

class DevicePolicy
{
    use HandlesAuthorization;

    /**
     * Super-admin are voie la orice pe devices.
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
    }

    /**
     * Poate vedea lista de devices?
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->can('view_devices');
    }

    /**
     * Poate vedea un device anume?
     */
    public function view(User $user, Device $device): bool
    {
        return $user->hasRole('admin') || $user->can('view_devices');
    }

    /**
     * Poate crea devices?
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->can('edit_devices');
    }

    /**
     * Poate actualiza devices?
     */
    public function update(User $user, Device $device): bool
    {
        return $user->hasRole('admin') || $user->can('edit_devices');
    }

    /**
     * Poate șterge devices?
     */
    public function delete(User $user, Device $device): bool
    {
        return $user->hasRole('admin') || $user->can('delete_devices');
    }
}
