<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Super-admin are voie la orice.
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
    }

    /**
     * Poate vedea lista de useri?
     */
    public function viewAny(User $user): bool
    {
        return $user->can('users.view');
    }

    /**
     * Poate vedea un anumit user?
     */
    public function view(User $user, User $model): bool
    {
        return $user->can('users.view');
    }

    /**
     * Poate crea useri noi?
     */
    public function create(User $user): bool
    {
        return $user->can('users.add');
    }

    /**
     * Poate edita useri?
     */
    public function update(User $user, User $model): bool
    {
        return $user->can('users.edit');
    }

    /**
     * Poate șterge useri?
     */
    public function delete(User $user, User $model): bool
    {
        return $user->can('users.delete');
    }
}
