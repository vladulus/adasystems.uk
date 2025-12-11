<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Auth\Access\HandlesAuthorization;

class VehiclePolicy
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
     * Poate vedea lista de vehicles?
     */
    public function viewAny(User $user): bool
    {
        return $user->can('vehicles.view');
    }

    /**
     * Poate vedea un vehicle anume?
     */
    public function view(User $user, Vehicle $vehicle): bool
    {
        if (!$user->can('vehicles.view')) {
            return false;
        }

        return $this->checkScope($user, $vehicle);
    }

    /**
     * Poate crea vehicles?
     */
    public function create(User $user): bool
    {
        return $user->can('vehicles.add');
    }

    /**
     * Poate actualiza un vehicle anume?
     */
    public function update(User $user, Vehicle $vehicle): bool
    {
        if (!$user->can('vehicles.edit')) {
            return false;
        }

        return $this->checkScope($user, $vehicle);
    }

    /**
     * Poate șterge un vehicle anume?
     */
    public function delete(User $user, Vehicle $vehicle): bool
    {
        if (!$user->can('vehicles.delete')) {
            return false;
        }

        return $this->checkScope($user, $vehicle);
    }

    /**
     * Verifică scope-ul permisiunii
     */
    private function checkScope(User $user, Vehicle $vehicle): bool
    {
        // scope.all = poate vedea TOATE vehiculele
        if ($user->can('vehicles.scope.all')) {
            return true;
        }

        // scope.own = verifică ownership bazat pe rol
        if ($user->can('vehicles.scope.own')) {
            return $this->isOwnVehicle($user, $vehicle);
        }

        // Fără scope = fără acces
        return false;
    }

    /**
     * Verifică dacă vehiculul "aparține" user-ului bazat pe rolul său
     */
    private function isOwnVehicle(User $user, Vehicle $vehicle): bool
    {
        // Super-admin NU are vehicule "proprii" - trebuie scope.all
        if ($user->hasRole('super-admin')) {
            return false;
        }

        // Admin - vede vehiculele superuserilor pe care îi administrează
        if ($user->hasRole('admin')) {
            $superuserIds = $user->managedSuperusers()->pluck('users.id')->toArray();
            return in_array($vehicle->owner_id, $superuserIds);
        }

        // Superuser (client) - vede vehiculele pe care le deține
        if ($user->isSuperuser()) {
            return $vehicle->owner_id === $user->id;
        }

        // Driver (user) - vede doar vehiculul său
        if ($user->isDriver()) {
            return $user->vehicle_id === $vehicle->id;
        }

        return false;
    }
}
