<?php

namespace App\Policies;

use App\Enums\AdminRoleType;
use App\Models\Admin;
use App\Models\SongLanguage;
use Illuminate\Auth\Access\HandlesAuthorization;

class SongLanguagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     */
    public function create(Admin $user): bool
    {
        return ! $this->isGuest($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Admin $user, SongLanguage $songLanguage): bool
    {
        return ! $this->isGuest($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Admin $user, SongLanguage $songLanguage): bool
    {
        return ! $this->isGuest($user);
    }

    /**
     * Check if the admin has the Guest role.
     */
    private function isGuest(Admin $user): bool
    {
        $user->loadMissing('roles');

        return $user->roles->contains(function ($role) {
            $id = $role->id;
            if ($id instanceof AdminRoleType) {
                return $id === AdminRoleType::Guest;
            }

            return (int) $id === AdminRoleType::Guest->value || $role->name === AdminRoleType::Guest->name();
        });
    }
}
