<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Device;
use Illuminate\Auth\Access\HandlesAuthorization;

class DevicePolicy
{
    use HandlesAuthorization;

    /**
     * Doar ROOT (vlad@impulsive.ro) are bypass total.
     * Toți ceilalți (inclusiv super-admini) trec prin permisiuni.
     */
    public function before(User $user, $ability)
    {
        if ($user->isRoot()) {
            return true;
        }
    }

    /**
     * Poate vedea lista de devices?
     */
    public function viewAny(User $user): bool
    {
        return $user->can('devices.view');
    }

    /**
     * Poate vedea un device anume?
     */
    public function view(User $user, Device $device): bool
    {
        if (!$user->can('devices.view')) {
            return false;
        }

        return $this->checkScope($user, $device, 'devices');
    }

    /**
     * Poate crea devices?
     */
    public function create(User $user): bool
    {
        return $user->can('devices.add');
    }

    /**
     * Poate actualiza un device anume?
     */
    public function update(User $user, Device $device): bool
    {
        if (!$user->can('devices.edit')) {
            return false;
        }

        return $this->checkScope($user, $device, 'devices');
    }

    /**
     * Poate șterge un device anume?
     */
    public function delete(User $user, Device $device): bool
    {
        if (!$user->can('devices.delete')) {
            return false;
        }

        return $this->checkScope($user, $device, 'devices');
    }

    /**
     * Poate accesa dashboard-ul device-ului (ADA-Pi dashboard)?
     */
    public function viewDashboard(User $user, Device $device): bool
    {
        if (!$user->can('dashboard.access')) {
            return false;
        }

        return $this->checkScope($user, $device, 'dashboard');
    }

    /**
     * Poate modifica settings-urile device-ului?
     */
    public function manageSettings(User $user, Device $device): bool
    {
        if (!$user->can('settings.access')) {
            return false;
        }

        return $this->checkScope($user, $device, 'devices');
    }

    /**
     * Poate trimite comenzi către device (reboot, DTC, etc)?
     */
    public function sendCommands(User $user, Device $device): bool
    {
        if (!$user->can('devices.edit')) {
            return false;
        }

        return $this->checkScope($user, $device, 'devices');
    }

    /**
     * Verifică scope-ul permisiunii
     */
    private function checkScope(User $user, Device $device, string $category): bool
    {
        // scope.all = poate vedea TOATE device-urile
        if ($user->can("{$category}.scope.all")) {
            return true;
        }

        // scope.own = verifică ownership bazat pe rol
        if ($user->can("{$category}.scope.own")) {
            return $this->isOwnDevice($user, $device);
        }

        // Fără scope = fără acces
        return false;
    }

    /**
     * Verifică dacă device-ul "aparține" user-ului bazat pe rolul său
     */
    private function isOwnDevice(User $user, Device $device): bool
    {
        // Super-admin NU are device-uri "proprii" - trebuie scope.all
        if ($user->hasRole('super-admin')) {
            return false;
        }

        // Admin - vede device-urile pe care le administrează (prin pivot table)
        if ($user->hasRole('admin')) {
            return $user->managedDevices()->where('devices.id', $device->id)->exists();
        }

        // Superuser (client) - vede device-urile pe care le deține
        if ($user->isSuperuser()) {
            return $device->owner_id === $user->id;
        }

        // Driver (user) - vede doar device-ul vehiculului său
        if ($user->isDriver()) {
            return $user->vehicle && $user->vehicle->device_id === $device->id;
        }

        return false;
    }
}