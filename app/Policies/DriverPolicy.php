<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Driver;
use Illuminate\Auth\Access\HandlesAuthorization;

class DriverPolicy
{
    use HandlesAuthorization;

    /**
     * Doar ROOT are bypass total.
     * Toți ceilalți (inclusiv super-admini) trec prin permisiuni + scope.
     */
    public function before(User $user, $ability)
    {
        if ($user->isRoot()) {
            return true;
        }
    }

    /**
     * Poate vedea lista de drivers?
     */
    public function viewAny(User $user): bool
    {
        return $user->can('drivers.view');
    }

    /**
     * Poate vedea un driver anume?
     */
    public function view(User $user, Driver $driver): bool
    {
        if (!$user->can('drivers.view')) {
            return false;
        }

        return $this->checkScope($user, $driver);
    }

    /**
     * Poate crea drivers?
     */
    public function create(User $user): bool
    {
        return $user->can('drivers.add');
    }

    /**
     * Poate actualiza un driver anume?
     */
    public function update(User $user, Driver $driver): bool
    {
        if (!$user->can('drivers.edit')) {
            return false;
        }

        return $this->checkScope($user, $driver);
    }

    /**
     * Poate șterge un driver anume?
     */
    public function delete(User $user, Driver $driver): bool
    {
        if (!$user->can('drivers.delete')) {
            return false;
        }

        return $this->checkScope($user, $driver);
    }

    /**
     * Poate muta un driver între vehicule?
     */
    public function move(User $user, Driver $driver): bool
    {
        if (!$user->can('drivers.move')) {
            return false;
        }

        return $this->checkScope($user, $driver);
    }

    /**
     * Verifică scope-ul permisiunii
     */
    private function checkScope(User $user, Driver $driver): bool
    {
        // scope.all = poate vedea TOȚI driverii
        if ($user->can('drivers.scope.all')) {
            return true;
        }

        // scope.own = verifică ownership bazat pe rol
        if ($user->can('drivers.scope.own')) {
            return $this->isOwnDriver($user, $driver);
        }

        // Fără scope = fără acces
        return false;
    }

    /**
     * Verifică dacă driverul "aparține" user-ului bazat pe rolul său
     */
    private function isOwnDriver(User $user, Driver $driver): bool
    {
        // Super-admin NU are driveri "proprii" - trebuie scope.all
        if ($user->hasRole('super-admin')) {
            return false;
        }

        // Admin - vede driverii superuserilor pe care îi administrează
        if ($user->hasRole('admin')) {
            $superuserIds = $user->managedSuperusers()->pluck('users.id')->toArray();
            return $driver->employers()->whereIn('superuser_id', $superuserIds)->exists();
        }

        // Superuser (client) - vede driverii pe care îi angajează
        if ($user->isSuperuser()) {
            return $driver->employers()->where('superuser_id', $user->id)->exists();
        }

        // Driver (user) - vede doar profilul său
        if ($user->isDriver()) {
            return $user->driverProfile && $user->driverProfile->id === $driver->id;
        }

        return false;
    }
}
